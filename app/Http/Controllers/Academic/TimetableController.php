<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\CourseOffering;
use App\Models\Timetable;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TimetableController extends Controller
{
    // Master Timetable Management & Hall Directory (Admin/Department)
    public function index()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $classrooms = Classroom::all();
        $offerings = CourseOffering::with(['course', 'instructors'])
            ->when($activeSemester, fn($q) => $q->where('semester_id', $activeSemester->id))
            ->get();

        $timetables = Timetable::with(['courseOffering.course', 'courseOffering.instructors', 'classroom'])
            ->when($activeSemester, fn($q) => $q->whereHas('courseOffering', fn($co) => $co->where('semester_id', $activeSemester->id)))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('academic.timetables.index', compact('classrooms', 'offerings', 'timetables', 'activeSemester'));
    }

    // Add Lecture Hall / Classroom
    public function storeClassroom(Request $request)
    {
        $validated = $request->validate([
            'building' => 'required|string|max:100',
            'room_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:10|max:500',
        ]);

        Classroom::create($validated);

        return back()->with('success', 'Classroom / Lecture Hall added successfully!');
    }

    // Schedule Course Session with Automated Clash Detection
    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $day = $validated['day_of_week'];
        $start = $validated['start_time'];
        $end = $validated['end_time'];
        $roomId = $validated['classroom_id'];
        $offeringId = $validated['course_offering_id'];

        // 1. CLASSROOM CLASH CHECK: Is the room already occupied at that time?
        // Overlap: (existing_start < new_end) AND (existing_end > new_start)
        $roomClash = Timetable::where('classroom_id', $roomId)
            ->where('day_of_week', $day)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->with(['courseOffering.course', 'classroom'])
            ->first();

        if ($roomClash) {
            throw ValidationException::withMessages([
                'schedule' => "ROOM CONFLICT: {$roomClash->classroom->building} - {$roomClash->classroom->room_number} is already booked on {$day} ({$roomClash->start_time} - {$roomClash->end_time}) for course {$roomClash->courseOffering->course->code}."
            ]);
        }

        // 2. INSTRUCTOR CLASH CHECK: Is the assigned instructor already teaching another class?
        $offering = CourseOffering::with('instructors')->findOrFail($offeringId);
        $instructorIds = $offering->instructors->pluck('id')->toArray();

        if (!empty($instructorIds)) {
            $instructorClash = Timetable::where('day_of_week', $day)
                ->where('start_time', '<', $end)
                ->where('end_time', '>', $start)
                ->whereHas('courseOffering.instructors', fn($q) => $q->whereIn('instructor_user_id', $instructorIds))
                ->with('courseOffering.course')
                ->first();

            if ($instructorClash) {
                throw ValidationException::withMessages([
                    'schedule' => "INSTRUCTOR CONFLICT: The assigned lecturer is already teaching course {$instructorClash->courseOffering->course->code} on {$day} from {$instructorClash->start_time} to {$instructorClash->end_time}."
                ]);
            }
        }

        // Save schedule
        Timetable::create($validated);

        return back()->with('success', 'Class session scheduled successfully without timetable clashes!');
    }

    // Weekly Schedule Grid for Logged-In User (Student or Instructor)
    public function mySchedule()
    {
        $user = Auth::user();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $activeSemester = Semester::where('is_active', true)->first();

        $query = Timetable::with(['courseOffering.course', 'classroom'])
            ->when($activeSemester, fn($q) => $q->whereHas('courseOffering', fn($co) => $co->where('semester_id', $activeSemester->id)));

        if ($user->hasRole('Student')) {
            $student = $user->student;
            $query->whereHas('courseOffering.registrationCourses', function ($q) use ($student) {
                $q->whereHas('courseRegistration', fn($cr) => $cr->where('student_id', $student->id))
                  ->where('item_status', 'Registered');
            });
        } elseif ($user->hasRole('Instructor')) {
            $query->whereHas('courseOffering.instructors', fn($q) => $q->where('instructor_user_id', $user->id));
        }

        $schedules = $query->get()->groupBy('day_of_week');

        return view('academic.timetables.schedule', compact('days', 'schedules', 'activeSemester'));
    }
}