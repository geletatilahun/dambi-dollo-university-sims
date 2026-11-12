<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterResult extends Model
{
    // Allow all fields to be mass-assigned
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}