<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use App\Services\AdmissionService;
use Illuminate\Http\Request;

class PublicApplicationController extends Controller
{
    public function __construct(
        protected AdmissionService $admissionService
    ) {}

    public function create()
    {
        $programs = Program::where('is_active', true)->get();
        return view('admissions.apply', compact('programs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'first_name' => 'required|string|max:50',
            'middle_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date|before:today',
            'national_id' => 'nullable|string|max:50',
            'email' => 'required|email|unique:applications,email|unique:users,email',
            'phone_number' => 'required|string|max:25',
            'study_mode' => 'required|in:Regular,Extension,Weekend,Summer',
            'entry_type' => 'required|in:Freshman,AdvancedStanding,Transfer',
        ]);

        $application = $this->admissionService->submitApplication($data);

        return redirect()->route('admissions.submitted', $application->id);
    }

    public function submitted(Application $application)
    {
        return view('admissions.submitted', compact('application'));
    }
}