<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\Program;
use App\Models\ProgramTransfer;
use App\Models\Student;
use App\Models\StudentStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentLifecycleController extends Controller
{
    // View detailed lifecycle dossier (Status history, transfer history)
    public function show(Student $student)
    {
        $student->load([
            'user', 
            'program.department.college', 
            'curriculum', 
            'semesterResults.semester', 
            'statusHistories.actor',
            'programTransfers.fromProgram',
            'programTransfers.toProgram'
        ]);

        $programs = Program::where('is_active', true)->where('id', '!=', $student->program_id)->get();

        // CHANGE THIS LINE:
        return view('registrar.student_profile', compact('student', 'programs'));
    }

    // Update Student Status (Active, Leave of Absence, Suspended, Withdrawn, Readmitted)
    public function updateStatus(Request $request, Student $student)
    {
        $validated = $request->validate([
            'status' => 'required|in:Active,LeaveOfAbsence,Suspended,Withdrawn,Dismissed,Readmitted',
            'reason' => 'required|string|max:400',
        ]);

        $oldStatus = $student->status;

        DB::transaction(function () use ($student, $oldStatus, $validated) {
            $student->update(['status' => $validated['status']]);

            StudentStatusHistory::create([
                'student_id' => $student->id,
                'previous_status' => $oldStatus,
                'new_status' => $validated['status'],
                'reason' => $validated['reason'],
                'changed_by_user_id' => Auth::id(),
            ]);
        });

        return back()->with('success', "Student status successfully updated to {$validated['status']}.");
    }

    // Submit Program/Department Transfer Request
    public function requestTransfer(Request $request, Student $student)
    {
        $validated = $request->validate([
            'to_program_id' => 'required|exists:programs,id|different:' . $student->program_id,
            'reason' => 'required|string|max:400',
        ]);

        ProgramTransfer::create([
            'student_id' => $student->id,
            'from_program_id' => $student->program_id,
            'to_program_id' => $validated['to_program_id'],
            'reason' => $validated['reason'],
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Program transfer request submitted for Registrar approval.');
    }

    // Approve Program Transfer: Changes student's program and reassigns curriculum
    public function approveTransfer(ProgramTransfer $transfer)
    {
        if ($transfer->status !== 'Pending') {
            return back()->withErrors(['status' => 'Transfer request is already resolved.']);
        }

        DB::transaction(function () use ($transfer) {
            $student = $transfer->student;

            // Find an active curriculum for the new program
            $newCurriculum = Curriculum::where('program_id', $transfer->to_program_id)
                ->where('is_active', true)
                ->latest('id')
                ->first();

            if (!$newCurriculum) {
                $newCurriculum = Curriculum::create([
                    'program_id' => $transfer->to_program_id,
                    'version' => '2026-Harmonized',
                    'title' => 'Standard Program Curriculum',
                    'total_credits_required' => 140.0,
                    'is_active' => true,
                ]);
            }

            // Update student's program and curriculum
            $student->update([
                'program_id' => $transfer->to_program_id,
                'curriculum_id' => $newCurriculum->id,
            ]);

            // Mark transfer approved
            $transfer->update([
                'status' => 'Approved',
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', 'Program transfer approved! Student profile and curriculum updated.');
    }

    // Reject Program Transfer
    public function rejectTransfer(Request $request, ProgramTransfer $transfer)
    {
        $request->validate(['remarks' => 'nullable|string|max:300']);

        $transfer->update([
            'status' => 'Rejected',
            'reviewer_remarks' => $request->remarks,
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Program transfer request rejected.');
    }
}