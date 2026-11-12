<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $guarded = [];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}