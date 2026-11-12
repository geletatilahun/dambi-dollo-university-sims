<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    // List all curricula across university programs
    public function index()
    {
        $curricula = Curriculum::with(['program.department.college', 'curriculumCourses'])->get();
        $programs = Program::where('is_active', true)->get();

        return view('academic.curricula.index', compact('curricula', 'programs'));
    }

    // Create a new Curriculum Version
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'version' => 'required|string|max:30',
            'title' => 'required|string|max:150',
            'total_credits_required' => 'required|numeric|min:60|max:250',
        ]);

        Curriculum::create($validated);

        return back()->with('success', 'Curriculum version created successfully!');
    }

    // View detailed Year-by-Year Roadmap
    public function show(Curriculum $curriculum)
    {
        $curriculum->load(['program.department', 'curriculumCourses.course.prerequisites']);
        $courses = Course::where('department_id', $curriculum->program->department_id)->get();

        // Group courses by Year Level and Semester Term
        $roadmap = $curriculum->curriculumCourses->groupBy(['year_level', 'semester_term']);

        return view('academic.curricula.show', compact('curriculum', 'courses', 'roadmap'));
    }

    // Attach Course to Year & Semester Term
    public function attachCourse(Request $request, Curriculum $curriculum)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'year_level' => 'required|integer|min:1|max:7',
            'semester_term' => 'required|integer|in:1,2',
        ]);

        // Prevent duplicate course in same curriculum
        $exists = CurriculumCourse::where('curriculum_id', $curriculum->id)
            ->where('course_id', $validated['course_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['course' => 'This course is already placed in this curriculum roadmap.']);
        }

        CurriculumCourse::create([
            'curriculum_id' => $curriculum->id,
            'course_id' => $validated['course_id'],
            'year_level' => $validated['year_level'],
            'semester_term' => $validated['semester_term'],
        ]);

        return back()->with('success', 'Course placed in curriculum roadmap successfully!');
    }

    // Set Prerequisite Dependency between two courses
    public function setPrerequisite(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'prerequisite_course_id' => 'required|exists:courses,id|different:course_id',
        ]);

        DB::table('course_prerequisites')->updateOrInsert([
            'course_id' => $validated['course_id'],
            'prerequisite_course_id' => $validated['prerequisite_course_id'],
        ]);

        return back()->with('success', 'Prerequisite dependency assigned successfully!');
    }
}