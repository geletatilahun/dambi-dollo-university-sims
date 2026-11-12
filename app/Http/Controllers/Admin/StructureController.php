<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\Request;

class StructureController extends Controller
{
    public function index()
    {
        $colleges = College::with(['departments.programs'])->get();
        return view('admin.structure.index', compact('colleges'));
    }

    public function storeCollege(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:colleges,code',
            'name' => 'required|string|max:150',
        ]);

        College::create($request->only('code', 'name'));

        return back()->with('success', 'College created successfully!');
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'code' => 'required|string|max:20|unique:departments,code',
            'name' => 'required|string|max:150',
        ]);

        Department::create($request->only('college_id', 'code', 'name'));

        return back()->with('success', 'Department created successfully!');
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|string|max:20|unique:programs,code',
            'name' => 'required|string|max:150',
            'degree_level' => 'required|in:Undergraduate,Postgraduate,PhD,Diploma',
            'study_mode' => 'required|in:Regular,Extension,Weekend,Summer',
            'duration_years' => 'required|integer|min:1|max:7',
        ]);

        Program::create($validated);

        return back()->with('success', 'Degree Program created successfully!');
    }
}