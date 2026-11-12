<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AddDropRequest;
use App\Models\CourseOffering;
use App\Models\CourseRegistration;
use App\Models\RegistrationCourse;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddDropController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        $activeSemester = Semester::where('is_active', true)->first();

        if (!$activeSemester) {
            return back()->withErrors(['semester' => 'No active academic semester term is currently configured.']);
        }

        // Check if Add/Drop deadline has passed
        $isWindowOpen = Carbon::now()->startOfDay()->lte(Carbon::parse($activeSemester->add_drop_deadline)->endOfDay());

        // Get currently registered courses for this semester
        $registration = CourseRegistration::with(['registrationCourses.courseOffering.course'])
            ->where('student_id', $student->id)
            ->where('semester_id', $activeSemester->id)
            ->first();

        $enrolledOfferingIds = $registration 
            ? $registration->registrationCourses->where('item_status', 'Registered')->pluck('course_offering_id')->toArray()
            : [];

        // Available offerings to "Add" (excluding already enrolled ones)
        $availableOfferings = CourseOffering::with('course')
            ->where('semester_id', $activeSemester->id)
            ->whereNotIn('id', $enrolledOfferingIds)
            ->get();

        // Student's submitted requests history
        $myRequests = AddDropRequest::with(['courseOffering.course'])
            ->where('student_id', $student->id)
            ->where('semester_id', $activeSemester->id)
            ->latest()
            ->get();

        return view('student.add_drop', compact('student', 'activeSemester', 'isWindowOpen', 'registration', 'availableOfferings', 'myRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'request_type' => 'required|in:Add,Drop',
            'reason' => 'required|string|max:400',
        ]);

        $student = Auth::user()->student;
        $activeSemester = Semester::where('is_active', true)->first();

        // Enforce deadline
        if (Carbon::now()->startOfDay()->gt(Carbon::parse($activeSemester->add_drop_deadline)->endOfDay())) {
            return back()->withErrors(['deadline' => 'The Add/Drop registration window for this semester has officially closed.']);
        }

        // Check for duplicate pending request for same course offering
        $existing = AddDropRequest::where('student_id', $student->id)
            ->where('course_offering_id', $validated['course_offering_id'])
            ->where('status', 'Pending')
            ->exists();

        if ($existing) {
            return back()->withErrors(['duplicate' => 'You already have a pending Add/Drop request for this course.']);
        }

        AddDropRequest::create([
            'student_id' => $student->id,
            'semester_id' => $activeSemester->id,
            'course_offering_id' => $validated['course_offering_id'],
            'request_type' => $validated['request_type'],
            'reason' => $validated['reason'],
            'status' => 'Pending',
        ]);

        return back()->with('success', "Your {$validated['request_type']} request has been submitted to the Department Head for review.");
    }
}