<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Student;
use App\Models\SemesterResult;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Enrollment Distribution by Degree Program & Gender
        $enrollmentByProgram = Program::withCount([
            'students as total_students',
            'students as male_students' => fn($q) => $q->where('gender', 'Male'),
            'students as female_students' => fn($q) => $q->where('gender', 'Female'),
        ])->get();

        // 2. University Academic Standing Breakdown
        $standingSummary = SemesterResult::select('academic_standing', DB::raw('count(*) as count'))
            ->groupBy('academic_standing')
            ->pluck('count', 'academic_standing')
            ->toArray();

        // 3. Cohort Totals
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('status', 'Active')->count(),
            'graduated_students' => Student::where('status', 'Graduated')->count(),
        ];

        return view('reports.index', compact('enrollmentByProgram', 'standingSummary', 'stats'));
    }
}