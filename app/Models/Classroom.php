<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $guarded = [];

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}