<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvisingNote extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_user_id');
    }
}