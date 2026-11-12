<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\CourseOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // View attendance dashboard & student summaries for a course offering
    public function index(CourseOffering $offering)
    {
        $offering->load(['course', 'attendanceSessions.records', 'registrationCourses.courseRegistration.student']);
        
        $sessions = $offering->attendanceSessions()->latest('session_date')->get();
        $totalSessions = $sessions->count();

        // Calculate attendance summary per student in this offering
        $enrolledStudents = $offering->registrationCourses
            ->where('item_status', 'Registered')
            ->map(function ($regCourse) use ($offering, $totalSessions) {
                $student = $regCourse->courseRegistration->student;
                
                $records = AttendanceRecord::whereHas('session', fn($q) => $q->where('course_offering_id', $offering->id))
                    ->where('student_id', $student->id)
                    ->get();

                $presentCount = $records->whereIn('status', ['Present', 'Late'])->count();
                $percentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : 100.0;

                return [
                    'student' => $student,
                    'present_count' => $presentCount,
                    'absent_count' => $records->where('status', 'Absent')->count(),
                    'excused_count' => $records->where('status', 'Excused')->count(),
                    'percentage' => $percentage,
                ];
            });

        return view('instructor.attendance.index', compact('offering', 'sessions', 'totalSessions', 'enrolledStudents'));
    }

    // Form to take attendance for a new class session
    public function create(CourseOffering $offering)
    {
        $offering->load(['course', 'registrationCourses.courseRegistration.student']);
        $students = $offering->registrationCourses
            ->where('item_status', 'Registered')
            ->map(fn($rc) => $rc->courseRegistration->student);

        return view('instructor.attendance.create', compact('offering', 'students'));
    }

    // Store attendance records for the class session
    public function store(Request $request, CourseOffering $offering)
    {
        $request->validate([
            'session_date' => 'required|date',
            'topic' => 'nullable|string|max:200',
            'attendance' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $offering) {
            $session = AttendanceSession::create([
                'course_offering_id' => $offering->id,
                'session_date' => $request->session_date,
                'topic' => $request->topic ?? 'Lecture Session',
            ]);

            foreach ($request->attendance as $studentId => $status) {
                AttendanceRecord::create([
                    'attendance_session_id' => $session->id,
                    'student_id' => $studentId,
                    'status' => in_array($status, ['Present', 'Absent', 'Late', 'Excused']) ? $status : 'Present',
                ]);
            }
        });

        return redirect()->route('instructor.attendance.index', $offering->id)
            ->with('success', 'Attendance recorded successfully!');
    }
}