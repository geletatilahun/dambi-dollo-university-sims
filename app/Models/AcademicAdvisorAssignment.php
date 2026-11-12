<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicAdvisorAssignment extends Model
{
    protected $guarded = [];

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_user_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}