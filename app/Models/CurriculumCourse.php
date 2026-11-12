<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourse extends Model
{
    protected $guarded = [];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}