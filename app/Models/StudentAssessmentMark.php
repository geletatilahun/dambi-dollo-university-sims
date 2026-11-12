<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssessmentMark extends Model
{
    protected $guarded = [];

    public function component()
    {
        return $this->belongsTo(AssessmentComponent::class, 'assessment_component_id');
    }

    public function registrationCourse()
    {
        return $this->belongsTo(RegistrationCourse::class);
    }
}