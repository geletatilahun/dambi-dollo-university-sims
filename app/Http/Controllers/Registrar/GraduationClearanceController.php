<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\GraduationClearance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraduationClearanceController extends Controller
{
    // List Document Requests for Records Staff
    public function documentsIndex()
    {
        $requests = DocumentRequest::with(['student.program', 'processor'])->latest()->paginate(15);
        return view('registrar.documents', compact('requests'));
    }

    // Process Document Request
    public function updateDocumentStatus(Request $request, DocumentRequest $doc)
    {
        $request->validate([
            'status' => 'required|in:Processing,Issued,Rejected',
            'officer_remarks' => 'nullable|string|max:300',
        ]);

        $doc->update([
            'status' => $request->status,
            'officer_remarks' => $request->officer_remarks,
            'processed_by_user_id' => Auth::id(),
            'issued_at' => $request->status === 'Issued' ? now() : $doc->issued_at,
        ]);

        return back()->with('success', "Document request {$doc->tracking_number} updated to {$request->status}.");
    }

    // List Graduation Clearance Candidates
    public function clearanceIndex()
    {
        $candidates = GraduationClearance::with(['student.program.department', 'academicYear'])->latest()->paginate(15);
        return view('registrar.clearance', compact('candidates'));
    }

    // Sign off an office clearance checklist item
    public function signOff(Request $request, GraduationClearance $clearance)
    {
        $field = $request->validate([
            'office' => 'required|in:status_department,status_library,status_services,status_finance',
            'action' => 'required|in:Cleared,Pending',
        ]);

        $clearance->update([$field['office'] => $field['action']]);

        // If all 4 offices are cleared, mark overall as ClearedForGraduation
        if (
            $clearance->status_department === 'Cleared' &&
            $clearance->status_library === 'Cleared' &&
            $clearance->status_services === 'Cleared' &&
            $clearance->status_finance === 'Cleared' &&
            $clearance->overall_status !== 'Graduated'
        ) {
            $clearance->update(['overall_status' => 'ClearedForGraduation']);
        }

        return back()->with('success', 'Clearance checklist updated.');
    }

    // Final Approval: Officially Graduate the Student
    public function graduate(Request $request, GraduationClearance $clearance)
    {
        if ($clearance->overall_status !== 'ClearedForGraduation') {
            return back()->withErrors(['clearance' => 'Cannot graduate student until all 4 offices have signed off.']);
        }

        $clearance->update([
            'overall_status' => 'Graduated',
            'graduated_at' => now(),
        ]);

        $clearance->student->update(['status' => 'Graduated']);

        return back()->with('success', "Student {$clearance->student->student_id_number} officially graduated!");
    }
}