<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgramTransfer extends Model
{
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromProgram()
    {
        return $this->belongsTo(Program::class, 'from_program_id');
    }

    public function toProgram()
    {
        return $this->belongsTo(Program::class, 'to_program_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}