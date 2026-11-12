<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    protected $guarded = [];

    public function student() { return $this->belongsTo(Student::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function registrationCourses() { return $this->hasMany(RegistrationCourse::class); }
}