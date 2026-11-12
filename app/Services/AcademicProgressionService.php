<?php

namespace App\Services;

use App\Models\CourseRegistration;
use App\Models\RegistrationCourse;
use App\Models\SemesterResult;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class AcademicProgressionService
{
    /**
     * Standard university grading scale tiers
     */
    protected array $gradingScale = [
        ['letter' => 'A+', 'point' => 4.00, 'min' => 90.0],
        ['letter' => 'A',  'point' => 4.00, 'min' => 85.0],
        ['letter' => 'A-', 'point' => 3.75, 'min' => 80.0],
        ['letter' => 'B+', 'point' => 3.50, 'min' => 75.0],
        ['letter' => 'B',  'point' => 3.00, 'min' => 70.0],
        ['letter' => 'B-', 'point' => 2.75, 'min' => 65.0],
        ['letter' => 'C+', 'point' => 2.50, 'min' => 60.0],
        ['letter' => 'C',  'point' => 2.00, 'min' => 50.0],
        ['letter' => 'C-', 'point' => 1.75, 'min' => 45.0],
        ['letter' => 'D',  'point' => 1.00, 'min' => 40.0],
        ['letter' => 'F',  'point' => 0.00, 'min' => 0.0],
    ];

    /**
     * Maps numerical score (0 - 100) to Letter Grade and Grade Points
     */
    public function resolveGrade(float $totalMark): array
    {
        $clamped = max(0.0, min(100.0, $totalMark));

        foreach ($this->gradingScale as $tier) {
            if ($clamped >= $tier['min']) {
                return [
                    'grade' => $tier['letter'],
                    'point' => $tier['point'],
                ];
            }
        }

        return ['grade' => 'F', 'point' => 0.00];
    }

    /**
     * Compute and commit SGPA, CGPA, and academic standing.
     */
    /**
     * Compute and commit SGPA, CGPA, and academic standing only if all courses are finalized.
     */
    public function computeSemesterResults(int $studentId, int $semesterId): SemesterResult
    {
        return DB::transaction(function () use ($studentId, $semesterId) {
            $registration = CourseRegistration::where('student_id', $studentId)
                ->where('semester_id', $semesterId)
                ->with(['registrationCourses.courseOffering.course'])
                ->firstOrFail();

            // Check if ANY active registered course is still missing a grade
            $hasPendingCourses = $registration->registrationCourses
                ->where('item_status', 'Registered')
                ->whereNull('grade_letter')
                ->isNotEmpty();

            // 1. Calculate Semester GPA only if all courses are graded
            $semCreditTotal = 0.0;
            $semGradePointsEarned = 0.0;
            $allGraded = true;

            foreach ($registration->registrationCourses as $item) {
                if ($item->item_status !== 'Registered') {
                    continue;
                }

                if ($item->grade_points === null) {
                    $allGraded = false;
                    continue;
                }

                $credit = (float) $item->courseOffering->course->credit_hours;
                $points = (float) $item->grade_points;

                $semCreditTotal += $credit;
                $semGradePointsEarned += ($credit * $points);
            }

            // If courses are pending, SGPA cannot be finalized
            $sgpa = ($allGraded && $semCreditTotal > 0) ? round($semGradePointsEarned / $semCreditTotal, 2) : 0.00;

            // 2. Cumulative GPA across all fully published semesters
            $allRegistrations = CourseRegistration::where('student_id', $studentId)
                ->with(['registrationCourses.courseOffering.course'])
                ->get();

            $cumCreditTotal = 0.0;
            $cumGradePointsEarned = 0.0;

            foreach ($allRegistrations as $reg) {
                foreach ($reg->registrationCourses as $enrolled) {
                    if ($enrolled->item_status === 'Registered' && $enrolled->grade_points !== null) {
                        $credit = (float) $enrolled->courseOffering->course->credit_hours;
                        $cumCreditTotal += $credit;
                        $cumGradePointsEarned += ($credit * (float) $enrolled->grade_points);
                    }
                }
            }

            $cgpa = $cumCreditTotal > 0 ? round($cumGradePointsEarned / $cumCreditTotal, 2) : 0.00;

            // Academic Standing
            $standing = $hasPendingCourses ? 'Term In Progress' : 'Good Standing';
            if (!$hasPendingCourses) {
                if ($cgpa >= 3.75) {
                    $standing = 'Very Great Distinction';
                } elseif ($cgpa >= 3.50) {
                    $standing = 'Distinction';
                } elseif ($cgpa < 1.75 || $sgpa < 1.50) {
                    $standing = 'Academic Warning';
                } elseif ($sgpa < 1.00) {
                    $standing = 'Academic Dismissal';
                }
            }

            return SemesterResult::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'semester_id' => $semesterId,
                ],
                [
                    'sgpa' => $sgpa,
                    'cgpa' => $cgpa,
                    'academic_standing' => $standing,
                ]
            );
        });
    }
}