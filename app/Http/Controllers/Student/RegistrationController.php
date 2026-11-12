<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\CourseOffering;
use App\Models\CourseRegistration;
use App\Models\RegistrationCourse;
use App\Models\SemesterResult;

class RegistrationController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $activeSemester = Semester::where('is_active', true)->first();
        
        $availableOfferings = CourseOffering::with('course')
            ->where('semester_id', $activeSemester->id)
            ->get();

        $currentRegistration = CourseRegistration::with('registrationCourses.courseOffering.course')
            ->where('student_id', $student->id)
            ->where('semester_id', $activeSemester->id)
            ->first();

        return view('student.registration', compact('student', 'activeSemester', 'availableOfferings', 'currentRegistration'));
    }

    public function store(Request $request)
    {
        $request->validate(['offerings' => 'required|array|min:1']);
        $student = Auth::user()->student;
        $activeSemester = Semester::where('is_active', true)->first();

        // 1. Fetch Student's Passed Courses from Academic History
        $passedCourseIds = RegistrationCourse::whereHas('courseRegistration', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->whereNotIn('grade_letter', ['F', 'Fx', null]) // Must not be failed or ungraded
            ->join('course_offerings', 'registration_courses.course_offering_id', '=', 'course_offerings.id')
            ->pluck('course_offerings.course_id')
            ->toArray();

        // 2. Check Prerequisites for each chosen course
        foreach ($request->offerings as $offeringId) {
            $offering = CourseOffering::with('course.prerequisites')->findOrFail($offeringId);

            foreach ($offering->course->prerequisites as $prereq) {
                if (!in_array($prereq->id, $passedCourseIds)) {
                    return back()->withErrors([
                        'prerequisite' => "REGISTRATION BLOCKED: {$offering->course->code} requires prerequisite '{$prereq->code} - {$prereq->title}' to be passed first with a passing grade."
                    ]);
                }
            }
        }

        // 3. Save Registration if all prerequisites are fulfilled
        $registration = CourseRegistration::updateOrCreate(
            ['student_id' => $student->id, 'semester_id' => $activeSemester->id],
            ['status' => 'Approved', 'total_registered_credits' => 0]
        );

        $registration->registrationCourses()->delete();
        $totalCredits = 0;

        foreach ($request->offerings as $offeringId) {
            $offering = CourseOffering::with('course')->findOrFail($offeringId);
            RegistrationCourse::create([
                'course_registration_id' => $registration->id,
                'course_offering_id' => $offering->id,
                'item_status' => 'Registered',
            ]);
            $totalCredits += (float) $offering->course->credit_hours;
        }

        $registration->update(['total_registered_credits' => $totalCredits]);

        return redirect()->route('student.registration')->with('success', 'Course registration completed successfully!');
    }

    public function viewGrades()
    {
        $student = Auth::user()->student;
        $registrations = CourseRegistration::with(['semester', 'registrationCourses.courseOffering.course'])
            ->where('student_id', $student->id)
            ->get();

        return view('student.grades', compact('student', 'registrations'));
    }

    public function printTranscript()
    {
        $student = Auth::user()->student->load(['program.department.college']);
        
        $registrations = CourseRegistration::with([
            'semester',
            'registrationCourses.courseOffering.course'
        ])
        ->where('student_id', $student->id)
        ->get();

        // Check if there are any ungraded/pending courses across all terms
        $hasPendingGrades = false;
        foreach ($registrations as $reg) {
            if ($reg->registrationCourses->where('item_status', 'Registered')->whereNull('grade_letter')->isNotEmpty()) {
                $hasPendingGrades = true;
                break;
            }
        }

        $verification = null;
        // ONLY generate official verification reference if all grades are published!
        if (!$hasPendingGrades) {
            $verification = \App\Models\TranscriptVerification::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'verification_code' => 'DDU-VER-' . strtoupper(substr(md5($student->id . $student->student_id_number . 'ddu_secret'), 0, 8)),
                    'issued_at' => now(),
                ]
            );
        }

        $semesterResults = SemesterResult::where('student_id', $student->id)
            ->with('semester')
            ->get()
            ->keyBy('semester_id');

        return view('student.transcript', compact('student', 'registrations', 'semesterResults', 'hasPendingGrades', 'verification'));
    }
}