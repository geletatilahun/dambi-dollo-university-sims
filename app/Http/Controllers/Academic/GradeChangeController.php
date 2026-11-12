<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\GradeChangeRequest;
use App\Models\RegistrationCourse;
use App\Services\AcademicProgressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeChangeController extends Controller
{
    public function __construct(
        protected AcademicProgressionService $progressionService
    ) {}

    // Review Queue for Department Heads & Registrars
    public function index()
    {
        $requests = GradeChangeRequest::with([
            'registrationCourse.courseOffering.course',
            'registrationCourse.courseRegistration.student',
            'requester'
        ])->latest()->paginate(15);

        return view('academic.grades.changes', compact('requests'));
    }

    // Instructor Initiates a Correction Request
    public function store(Request $request, RegistrationCourse $item)
    {
        $request->validate([
            'proposed_score' => 'required|numeric|min:0|max:100',
            'reason' => 'required|string|max:500',
        ]);

        $resolved = $this->progressionService->resolveGrade((float) $request->proposed_score);

        GradeChangeRequest::create([
            'registration_course_id' => $item->id,
            'previous_score' => $item->final_score ?? 0,
            'previous_grade' => $item->grade_letter ?? 'F',
            'proposed_score' => $request->proposed_score,
            'proposed_grade' => $resolved['grade'],
            'reason' => $request->reason,
            'status' => 'Pending',
            'requested_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Grade change request submitted for Department and Registrar review.');
    }

    // Department Head Endorses
    public function endorse(GradeChangeRequest $change)
    {
        $change->update(['status' => 'DeptEndorsed']);
        return back()->with('success', 'Grade change endorsed and forwarded to Registrar.');
    }

    // Registrar Final Approval: Overwrites marks & triggers recalculation
    public function approve(GradeChangeRequest $change)
    {
        DB::transaction(function () use ($change) {
            $item = $change->registrationCourse;
            $resolved = $this->progressionService->resolveGrade((float) $change->proposed_score);

            // 1. Update official score and points
            $item->update([
                'final_score' => $change->proposed_score,
                'grade_letter' => $change->proposed_grade,
                'grade_points' => $resolved['point'],
            ]);

            // 2. Mark request approved
            $change->update([
                'status' => 'Approved',
                'approved_by_user_id' => Auth::id(),
                'resolved_at' => now(),
            ]);

            // 3. Recalculate Student's SGPA and CGPA
            $studentId = $item->courseRegistration->student_id;
            $semesterId = $item->courseOffering->semester_id;
            $this->progressionService->computeSemesterResults($studentId, $semesterId);
        });

        return back()->with('success', 'Grade change approved! Official marks updated and student GPA recalculated.');
    }

    // Reject Request
    public function reject(GradeChangeRequest $change)
    {
        $change->update([
            'status' => 'Rejected',
            'approved_by_user_id' => Auth::id(),
            'resolved_at' => now(),
        ]);

        return back()->with('success', 'Grade change request rejected.');
    }
}