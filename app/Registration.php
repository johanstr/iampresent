<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'student_id', 'registration_date', 'registration_time_in', 'registration_time_out'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
