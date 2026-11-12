<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationCourse extends Model
{
    protected $guarded = [];

    public function courseRegistration() { return $this->belongsTo(CourseRegistration::class); }
    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function assessmentMarks() { return $this->hasMany(StudentAssessmentMark::class); }
}