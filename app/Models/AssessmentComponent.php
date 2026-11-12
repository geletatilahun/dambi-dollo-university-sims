<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentComponent extends Model
{
    protected $guarded = [];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function marks()
    {
        return $this->hasMany(StudentAssessmentMark::class);
    }
}