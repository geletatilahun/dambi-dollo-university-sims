<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AddDropRequest;
use App\Models\CourseRegistration;
use App\Models\RegistrationCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddDropApprovalController extends Controller
{
    public function index()
    {
        $requests = AddDropRequest::with([
            'student.program',
            'courseOffering.course',
            'semester',
            'reviewer'
        ])->latest()->paginate(15);

        return view('academic.add_drop.index', compact('requests'));
    }

    public function approve(AddDropRequest $addDrop)
    {
        if ($addDrop->status !== 'Pending') {
            return back()->withErrors(['status' => 'Request is no longer pending.']);
        }

        DB::transaction(function () use ($addDrop) {
            $registration = CourseRegistration::firstOrCreate(
                ['student_id' => $addDrop->student_id, 'semester_id' => $addDrop->semester_id],
                ['status' => 'Approved', 'total_registered_credits' => 0]
            );

            $offering = $addDrop->courseOffering->load('course');

            if ($addDrop->request_type === 'Drop') {
                // Mark course item as Dropped
                $item = RegistrationCourse::where('course_registration_id', $registration->id)
                    ->where('course_offering_id', $offering->id)
                    ->first();

                if ($item) {
                    $item->update(['item_status' => 'Dropped']);
                }
            } elseif ($addDrop->request_type === 'Add') {
                // Check capacity
                $enrolledCount = RegistrationCourse::where('course_offering_id', $offering->id)
                    ->where('item_status', 'Registered')
                    ->count();

                if ($enrolledCount >= $offering->capacity) {
                    throw new \Exception("Cannot approve: Course offering {$offering->course->code} - {$offering->section} is at maximum capacity ({$offering->capacity} students).");
                }

                // Add or reactivate course item
                RegistrationCourse::updateOrCreate(
                    [
                        'course_registration_id' => $registration->id,
                        'course_offering_id' => $offering->id,
                    ],
                    ['item_status' => 'Registered']
                );
            }

            // Recalculate total registered credits
            $newTotalCredits = RegistrationCourse::where('course_registration_id', $registration->id)
                ->where('item_status', 'Registered')
                ->join('course_offerings', 'registration_courses.course_offering_id', '=', 'course_offerings.id')
                ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
                ->sum('courses.credit_hours');

            $registration->update(['total_registered_credits' => $newTotalCredits]);

            // Mark request approved
            $addDrop->update([
                'status' => 'Approved',
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', "Add/Drop request for {$addDrop->student->student_id_number} was successfully approved!");
    }

    public function reject(Request $request, AddDropRequest $addDrop)
    {
        $request->validate(['remarks' => 'nullable|string|max:300']);

        $addDrop->update([
            'status' => 'Rejected',
            'reviewer_remarks' => $request->remarks,
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Add/Drop request rejected.");
    }
}