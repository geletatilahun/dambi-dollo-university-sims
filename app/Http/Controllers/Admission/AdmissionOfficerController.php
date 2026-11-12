<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\AdmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdmissionOfficerController extends Controller
{
    public function __construct(
        protected AdmissionService $admissionService
    ) {}

    public function index(Request $request)
    {
        $status = $request->get('status', 'All');

        $query = Application::with('program')->latest();
        if ($status !== 'All') {
            $query->where('status', $status);
        }

        $applications = $query->paginate(15);
        return view('admissions.index', compact('applications', 'status'));
    }

    public function show(Application $application)
    {
        $application->load(['program.department.college', 'reviewer', 'student']);
        return view('admissions.show', compact('application'));
    }

    public function review(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => 'required|in:Accepted,Rejected,UnderReview',
            'reviewer_remarks' => 'nullable|string|max:500',
        ]);

        $application->update([
            'status' => $data['status'],
            'reviewer_remarks' => $data['reviewer_remarks'],
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Application status successfully updated to {$data['status']}.");
    }

    public function enroll(Application $application)
    {
        try {
            $student = $this->admissionService->convertApplicantToStudent($application);

            return redirect()->route('admissions.show', $application->id)->with('success', 
                "Applicant successfully enrolled! Assigned Student ID: {$student->student_id_number} | Temporary Password: {$student->initial_password}"
            );
        } catch (\Exception $e) {
            return back()->withErrors(['enroll' => $e->getMessage()]);
        }
    }
}