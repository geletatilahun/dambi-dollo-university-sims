<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\GradeSheet;
use App\Models\CourseOffering;
use App\Services\AcademicProgressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeWorkflowController extends Controller
{
    public function __construct(
        protected AcademicProgressionService $progressionService
    ) {}

    // Department Head: Review list of submitted grade sheets
    public function departmentIndex()
    {
        $gradeSheets = GradeSheet::with(['courseOffering.course', 'courseOffering.semester'])
            ->whereIn('status', ['Submitted', 'DepartmentEndorsed', 'Published'])
            ->get();

        return view('academic.grades.department', compact('gradeSheets'));
    }

    // Registrar: Review list of endorsed grade sheets to publish
    public function registrarIndex()
    {
        $gradeSheets = GradeSheet::with(['courseOffering.course', 'courseOffering.semester'])
            ->whereIn('status', ['DepartmentEndorsed', 'Published'])
            ->get();

        return view('academic.grades.registrar', compact('gradeSheets'));
    }

    // Step A: Instructor submits grade sheet
    public function submit(Request $request, CourseOffering $offering)
    {
        $sheet = GradeSheet::firstOrCreate(['course_offering_id' => $offering->id]);
        $sheet->update(['status' => 'Submitted']);

        return back()->with('success', 'Grade sheet submitted to Department Head for endorsement.');
    }

    // Step B: Department Head endorses grade sheet
    public function endorse(Request $request, GradeSheet $sheet)
    {
        $sheet->update(['status' => 'DepartmentEndorsed']);
        return back()->with('success', 'Grade sheet successfully endorsed and forwarded to Registrar.');
    }

    // Step C: Registrar publishes grades (Locks marks & runs GPA calculations)
    public function publish(Request $request, GradeSheet $sheet)
    {
        DB::transaction(function () use ($sheet) {
            $sheet->update(['status' => 'Published']);

            $offering = $sheet->courseOffering->load('registrationCourses.courseRegistration');
            $semesterId = $offering->semester_id;

            // Recalculate SGPA & CGPA for every student in this offering
            foreach ($offering->registrationCourses as $regCourse) {
                $studentId = $regCourse->courseRegistration->student_id;
                $this->progressionService->computeSemesterResults($studentId, $semesterId);
            }
        });

        return back()->with('success', 'Grades officially published! Student GPAs and academic standings updated.');
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

        $semesterResults = \App\Models\SemesterResult::where('student_id', $student->id)
            ->with('semester')
            ->get()
            ->keyBy('semester_id');

        return view('student.transcript', compact('student', 'registrations', 'semesterResults'));
    }
}