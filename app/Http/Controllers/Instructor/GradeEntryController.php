<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AssessmentComponent;
use App\Models\CourseOffering;
use App\Models\RegistrationCourse;
use App\Models\StudentAssessmentMark;
use App\Services\AcademicProgressionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeEntryController extends Controller
{
    public function __construct(
        protected AcademicProgressionService $progressionService
    ) {}

    // View Multi-Column Mark Sheet
    public function showOffering(CourseOffering $offering)
    {
        $offering->load([
            'course',
            'assessmentComponents',
            'registrationCourses.courseRegistration.student',
            'registrationCourses.assessmentMarks'
        ]);

        // Auto-provision default Ethiopian university assessment breakdown if empty
        if ($offering->assessmentComponents->isEmpty()) {
            AssessmentComponent::create(['course_offering_id' => $offering->id, 'title' => 'Assignments / Tests', 'max_weight' => 20.00]);
            AssessmentComponent::create(['course_offering_id' => $offering->id, 'title' => 'Midterm Exam', 'max_weight' => 30.00]);
            AssessmentComponent::create(['course_offering_id' => $offering->id, 'title' => 'Final Exam', 'max_weight' => 50.00]);
            $offering->load('assessmentComponents');
        }

        $components = $offering->assessmentComponents;
        $totalWeight = $components->sum('max_weight');

        return view('instructor.grades', compact('offering', 'components', 'totalWeight'));
    }

    // Configure Custom Assessment Components
    public function storeComponent(Request $request, CourseOffering $offering)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'max_weight' => 'required|numeric|min:1|max:100',
        ]);

        $currentWeight = $offering->assessmentComponents->sum('max_weight');
        if (($currentWeight + $validated['max_weight']) > 100) {
            return back()->withErrors(['weight' => "Cannot add component. Total evaluation weight would exceed 100% (Current: {$currentWeight}%)."]);
        }

        AssessmentComponent::create([
            'course_offering_id' => $offering->id,
            'title' => $validated['title'],
            'max_weight' => $validated['max_weight'],
        ]);

        return back()->with('success', 'Assessment component added successfully!');
    }

    // Reset Components to Default (20% Assignments, 30% Midterm, 50% Final)
    public function resetComponents(CourseOffering $offering)
    {
        DB::transaction(function () use ($offering) {
            $offering->assessmentComponents()->delete();
            AssessmentComponent::create(['course_offering_id' => $offering->id, 'title' => 'Continuous Assessment', 'max_weight' => 20.00]);
            AssessmentComponent::create(['course_offering_id' => $offering->id, 'title' => 'Midterm Exam', 'max_weight' => 30.00]);
            AssessmentComponent::create(['course_offering_id' => $offering->id, 'title' => 'Final Exam', 'max_weight' => 50.00]);
        });

        return back()->with('success', 'Assessment scheme reset to standard Ethiopian university weights (20/30/50).');
    }

    // Save Component Marks & Auto-Calculate Total and Letter Grade
    public function updateMarks(Request $request, CourseOffering $offering)
    {
        $request->validate(['scores' => 'required|array']);

        DB::transaction(function () use ($request, $offering) {
            $components = $offering->assessmentComponents->keyBy('id');

            foreach ($request->scores as $regCourseId => $compScores) {
                $item = RegistrationCourse::findOrFail($regCourseId);
                $totalComputedScore = 0.0;

                foreach ($compScores as $compId => $score) {
                    if ($score !== null && $score !== '') {
                        $comp = $components->get($compId);
                        $scoreFloat = (float) $score;

                        // Clamp to component max weight
                        $clampedScore = min($scoreFloat, (float) $comp->max_weight);

                        StudentAssessmentMark::updateOrCreate(
                            [
                                'assessment_component_id' => $compId,
                                'registration_course_id' => $item->id,
                            ],
                            ['score' => $clampedScore]
                        );

                        $totalComputedScore += $clampedScore;
                    }
                }

                // Map final cumulative percentage to letter grade & points
                $resolved = $this->progressionService->resolveGrade($totalComputedScore);

                $item->update([
                    'final_score' => $totalComputedScore,
                    'grade_letter' => $resolved['grade'],
                    'grade_points' => $resolved['point'],
                ]);
            }
        });

        return back()->with('success', 'Continuous assessment marks saved! Weighted totals and letter grades recalculated.');
    }
}