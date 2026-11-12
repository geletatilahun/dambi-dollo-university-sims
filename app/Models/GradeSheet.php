<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeSheet extends Model
{
    // Allow all fields to be mass-assigned
    protected $guarded = [];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }
}