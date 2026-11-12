<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\TranscriptVerification;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    // Public search form
    public function index()
    {
        return view('public.verify_transcript');
    }

    // Process verification code lookup
    public function lookup(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        return redirect()->route('public.verify.show', trim($request->code));
    }

    // Display Authenticated Transcript Record
    public function show(string $code)
    {
        $verification = TranscriptVerification::where('verification_code', $code)
            ->with(['student.program.department.college', 'student.semesterResults.semester', 'student.user'])
            ->first();

        if ($verification) {
            AuditLog::create([
                'user_id' => null,
                'action_event' => 'TRANSCRIPT_VERIFIED',
                'description' => "External third party verified transcript for student {$verification->student->student_id_number} via code {$code}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return view('public.verify_transcript', compact('verification', 'code'));
    }
}