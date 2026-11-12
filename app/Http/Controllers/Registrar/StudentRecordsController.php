<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Student;

class StudentRecordsController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'program'])->paginate(15);
        return view('registrar.students', compact('students'));
    }
}