<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $guarded = [];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Add any other relationships here if needed, e.g., for course offerings
    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class);
    }
}