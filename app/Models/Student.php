<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];

    public function user() { return $this->belongsTo(User::class); }
    public function program() { return $this->belongsTo(Program::class); }
    public function semesterResults() { return $this->hasMany(SemesterResult::class); }
    public function registrations() { return $this->hasMany(CourseRegistration::class); }
    public function attendanceRecords() { return $this->hasMany(AttendanceRecord::class); }
    public function documentRequests() { return $this->hasMany(DocumentRequest::class);  }
    public function graduationClearance() { return $this->hasOne(GraduationClearance::class); }
    public function statusHistories() { return $this->hasMany(StudentStatusHistory::class); }
    public function programTransfers() { return $this->hasMany(ProgramTransfer::class); }
    public function advisingNotes() {return $this->hasMany(AdvisingNote::class); }
    public function advisorAssignment() { return $this->hasOne(AcademicAdvisorAssignment::class); }
    public function advisor() { return $this->belongsTo(User::class, 'academic_advisor_user_id'); }
    public function transcriptVerification() { return $this->hasOne(TranscriptVerification::class); }
    public function curriculum() { return $this->belongsTo(Curriculum::class); }
}