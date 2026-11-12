<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $years = AcademicYear::with('semesters')->latest()->get();
        return view('admin.calendar.index', compact('years'));
    }

    public function storeYear(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:academic_years,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        AcademicYear::create($request->only('code', 'start_date', 'end_date'));

        return back()->with('success', 'Academic Year created successfully!');
    }

    public function storeSemester(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start' => 'required|date',
            'registration_deadline' => 'required|date|after_or_equal:registration_start',
            'add_drop_deadline' => 'required|date|after_or_equal:registration_deadline',
            'grade_submission_deadline' => 'required|date|after_or_equal:add_drop_deadline',
        ]);

        // Use $validated so _token is excluded:
        Semester::create($validated);

        return back()->with('success', 'Academic Semester term created successfully!');
    }

    public function activateSemester(Semester $semester)
    {
        // Deactivate all, then activate chosen
        Semester::query()->update(['is_active' => false]);
        $semester->update(['is_active' => true]);

        return back()->with('success', "Semester {$semester->name} is now set as the active university term!");
    }
}