<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $guarded = [];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}