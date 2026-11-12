<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AdvisingNote;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvisorController extends Controller
{
    // Advisor Portal: View Assigned Advisees with Risk Indicators
    public function index()
    {
        $advisor = Auth::user();

        $advisees = Student::where('academic_advisor_user_id', $advisor->id)
            ->with(['program.department', 'semesterResults' => fn($q) => $q->latest('id')])
            ->get()
            ->map(function ($student) {
                $latestResult = $student->semesterResults->first();
                $cgpa = $latestResult ? (float) $latestResult->cgpa : 0.00;
                $sgpa = $latestResult ? (float) $latestResult->sgpa : 0.00;
                $standing = $latestResult ? $latestResult->academic_standing : 'Good Standing';

                // Automated "At-Risk" Flagging
                $isAtRisk = ($cgpa < 2.00 || $sgpa < 1.75 || in_array($standing, ['Academic Warning', 'Probation', 'Academic Dismissal']));

                return [
                    'student' => $student,
                    'cgpa' => $cgpa,
                    'sgpa' => $sgpa,
                    'standing' => $standing,
                    'is_at_risk' => $isAtRisk,
                ];
            });

        return view('advisor.index', compact('advisees'));
    }

    // Advisee Detailed Progress Dossier & Counseling Log
    public function show(Student $student)
    {
        // Security check: Must be the assigned advisor or Admin/Department Head
        if ($student->academic_advisor_user_id !== Auth::id() && !Auth::user()->hasRole(['SuperAdmin', 'DepartmentHead', 'Registrar'])) {
            abort(403, 'Unauthorized access to advisee dossier.');
        }

        $student->load([
            'program.department',
            'semesterResults.semester',
            'registrations.semester',
            'registrations.registrationCourses.courseOffering.course',
            'advisingNotes.advisor'
        ]);

        return view('advisor.show', compact('student'));
    }

    // Record an Advising Session / Intervention Note
    public function storeNote(Request $request, Student $student)
    {
        $validated = $request->validate([
            'session_date' => 'required|date',
            'intervention_type' => 'required|string',
            'observations' => 'required|string|max:1000',
            'recommendations' => 'required|string|max:1000',
            'is_confidential' => 'nullable|boolean',
        ]);

        AdvisingNote::create([
            'student_id' => $student->id,
            'advisor_user_id' => Auth::id(),
            'session_date' => $validated['session_date'],
            'intervention_type' => $validated['intervention_type'],
            'observations' => $validated['observations'],
            'recommendations' => $validated['recommendations'],
            'is_confidential' => $request->boolean('is_confidential'),
        ]);

        return back()->with('success', 'Advising intervention note recorded successfully!');
    }

    // Department Head / Admin: Assign Advisors to Students
    public function assignIndex()
    {
        $students = Student::with(['program', 'advisor'])->latest()->paginate(20);
        $advisors = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Instructor', 'AcademicAdvisor', 'DepartmentHead']))->get();

        return view('academic.advisors.assign', compact('students', 'advisors'));
    }

    // Process Advisor Assignment
    public function assignAdvisor(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'advisor_user_id' => 'required|exists:users,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $student->update(['academic_advisor_user_id' => $validated['advisor_user_id']]);

        $advisor = User::find($validated['advisor_user_id']);

        return back()->with('success', "Advisor {$advisor->name} assigned to student {$student->student_id_number}.");
    }

    // Student View: "My Advisor & Advising Advice"
    public function studentAdvisorView()
    {
        $student = Auth::user()->student->load(['advisor', 'advisingNotes' => fn($q) => $q->where('is_confidential', false)->latest()]);

        return view('student.advisor', compact('student'));
    }
}