<?php

namespace App\Http\Controllers;

class DocumentationController extends Controller
{
    // Document 1: Official User Manual & Operational Handbook
    public function userManual()
    {
        return view('docs.user_manual');
    }

    // Document 2: Comprehensive Technical Architecture & Data Dictionary
    public function technicalManual()
    {
        return view('docs.technical_manual');
    }

    // Document 3: Academic Policy Guidelines & Standard Operating Procedures (SOP)
    public function guidelines()
    {
        return view('docs.guidelines');
    }
}