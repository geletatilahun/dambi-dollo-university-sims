<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeChangeRequest extends Model
{
    protected $guarded = [];

    public function registrationCourse()
    {
        return $this->belongsTo(RegistrationCourse::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}