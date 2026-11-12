<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;

class CourseCatalogController extends Controller
{
    // List all courses in the catalog
    public function indexCourses()
    {
        $courses = Course::with('department')->latest()->paginate(15);
        $departments = Department::all();

        return view('academic.courses.index', compact('courses', 'departments'));
    }

    // Add a new course
    public function storeCourse(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|string|max:20|unique:courses,code',
            'title' => 'required|string|max:150',
            'credit_hours' => 'required|numeric|min:1|max:8',
            'ects' => 'required|numeric|min:1|max:15',
        ]);

        Course::create($request->only('department_id', 'code', 'title', 'credit_hours', 'ects'));

        return back()->with('success', 'Course added to catalog successfully!');
    }

    // List and manage semester offerings
    public function indexOfferings()
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $offerings = CourseOffering::with(['course.department', 'semester', 'instructors'])
            ->when($activeSemester, fn($q) => $q->where('semester_id', $activeSemester->id))
            ->latest('id')
            ->paginate(15);

        $courses = Course::all();
        $semesters = Semester::all();
        
        // Retrieve lecturers to assign
        $instructors = User::whereHas('roles', fn($q) => $q->where('name', 'Instructor'))->get();

        return view('academic.offerings.index', compact('offerings', 'courses', 'semesters', 'instructors', 'activeSemester'));
    }

    // Create a semester section and assign an instructor
    public function storeOffering(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'section' => 'required|string|max:20',
            'capacity' => 'required|integer|min:5|max:200',
            'instructor_id' => 'nullable|exists:users,id',
        ]);

        $offering = CourseOffering::create($request->only('course_id', 'semester_id', 'section', 'capacity'));

        // Assign instructor if selected
        if ($request->filled('instructor_id')) {
            $offering->instructors()->attach($request->instructor_id);
        }

        return back()->with('success', 'Course offering and section created successfully!');
    }
}