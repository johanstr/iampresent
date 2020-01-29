<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'group_name', 'daypart', 'cohort', 'mentor', 
    ];

    public function student()
    {
        return $this->hasMany(Student::class);
    }
}
