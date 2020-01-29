<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'first_name', 'prefixes', 'last_name', 'student_number', 
        'gender', 'email', 'group_id', 'card_one_uid', 'card_two_uid', 
        'photo'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
