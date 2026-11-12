<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DocumentRequest;
use App\Models\GraduationClearance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student->load(['documentRequests', 'graduationClearance']);
        $activeYear = AcademicYear::where('is_current', true)->first();

        return view('student.services', compact('student', 'activeYear'));
    }

    // Submit Document Request
    public function storeDocumentRequest(Request $request)
    {
        $request->validate([
            'request_type' => 'required|in:Official Transcript,Enrollment Verification Letter,Medium of Instruction Certificate,Letter of Recommendation',
            'purpose' => 'required|string|max:400',
        ]);

        $student = Auth::user()->student;
        $count = DocumentRequest::count() + 1;
        $trackingNumber = sprintf('DOC-%s-%04d', date('Y'), $count);

        DocumentRequest::create([
            'tracking_number' => $trackingNumber,
            'student_id' => $student->id,
            'request_type' => $request->request_type,
            'purpose' => $request->purpose,
            'status' => 'Pending',
        ]);

        return back()->with('success', "Document request submitted successfully! Tracking Number: {$trackingNumber}");
    }

    // Apply for Graduation Clearance
    public function applyGraduation(Request $request)
    {
        $student = Auth::user()->student;
        $activeYear = AcademicYear::where('is_current', true)->first();

        if ($student->graduationClearance) {
            return back()->with('success', 'Graduation clearance application is already on file.');
        }

        GraduationClearance::create([
            'student_id' => $student->id,
            'academic_year_id' => $activeYear->id,
            'overall_status' => 'InClearance',
            'degree_awarded' => $student->program->name,
        ]);

        return back()->with('success', 'Graduation clearance dossier opened! Complete clearances from designated offices.');
    }
}