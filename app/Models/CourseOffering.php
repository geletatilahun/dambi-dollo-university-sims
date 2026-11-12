<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseOffering extends Model
{
    protected $guarded = [];

    public function course() { return $this->belongsTo(Course::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function instructors() { return $this->belongsToMany(User::class, 'course_instructors', 'course_offering_id', 'instructor_user_id'); }
    public function registrationCourses() { return $this->hasMany(RegistrationCourse::class); }
    public function gradeSheet() { return $this->hasOne(GradeSheet::class); }
    public function attendanceSessions()  { return $this->hasMany(AttendanceSession::class);}
    public function timetables() { return $this->hasMany(Timetable::class); }
    public function assessmentComponents(){ return $this->hasMany(AssessmentComponent::class); }
}