<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\CourseOffering;
use App\Models\GradeSheet;
use App\Models\Announcement;
use App\Models\Department;
use App\Models\College;
use App\Models\AddDropRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('roles');
        $primaryRole = $user->roles->first()?->name ?? 'Student';
        $metrics = [];

        // 1. Fetch Targeted Announcements
        $targetAudience = match ($primaryRole) {
            'Student'    => ['All', 'Students'],
            'Instructor' => ['All', 'Instructors'],
            default      => ['All', 'Students', 'Instructors'],
        };

        $announcements = Announcement::with('author')
            ->where('is_active', true)
            ->whereIn('target_audience', $targetAudience)
            ->latest()
            ->take(5)
            ->get();

        // 2. Role-Specific Data Aggregation
        if ($primaryRole === 'Student') {
            $student = Student::where('user_id', $user->id)->with('program')->first();
            $metrics['student'] = $student;
            $metrics['latestResult'] = $student ? $student->semesterResults()->latest('id')->first() : null;

        } elseif ($primaryRole === 'Instructor') {
            $metrics['offerings'] = CourseOffering::whereHas('instructors', function($q) use ($user) {
                $q->where('instructor_user_id', $user->id);
            })->with('course')->get();

        } elseif ($primaryRole === 'Dean') {
            // College Dean metrics
            $metrics['totalCollegeStudents'] = Student::count();
            $metrics['departments'] = Department::withCount('programs')->get();
            $metrics['pendingEndorsements'] = GradeSheet::where('status', 'Submitted')->count();
            $metrics['activeProgramsCount'] = \App\Models\Program::where('is_active', true)->count();

        } elseif ($primaryRole === 'DepartmentHead') {
            // Department Head metrics & action queues
            $metrics['totalStudents'] = Student::count();
            $metrics['pendingAddDrop'] = AddDropRequest::where('status', 'Pending')->count();
            $metrics['pendingEndorsements'] = GradeSheet::where('status', 'Submitted')->count();
            $metrics['offerings'] = CourseOffering::with(['course', 'instructors'])->latest()->take(6)->get();

        } elseif (in_array($primaryRole, ['Registrar', 'SuperAdmin', 'AdmissionOfficer'])) {
            $metrics['totalStudents'] = Student::count();
            $metrics['activeStudents'] = Student::where('status', 'Active')->count();
            $metrics['pendingGrades'] = GradeSheet::where('status', '!=', 'Published')->count();
        }

        return view('dashboard', compact('user', 'primaryRole', 'metrics', 'announcements'));
    }
}