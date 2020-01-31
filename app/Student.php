<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'first_name', 'prefixes', 'last_name', 'student_number', 
        'gender', 'email', 'group_id', 'card_one_uid', 'card_two_uid', 
        'photo', 
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function countRegistrations()
    {
        return count($this->registrations);
    }

    public function getPhoto()
    {
        // if (is_null($this->photo))
        //     return asset('img/default-student.png');

        return $this->photo; //"data:image/" . $this->photo_type . ";base64, " . base64_encode($this->photo);
    }

    public function getFullName()
    {
        return sprintf(
            "%s, %s%s",
            $this->last_name,
            $this->first_name,
            (!empty($this->prefixes) ? " {$this->prefixes}" : "")
        );
    }

    public function hasBothCards()
    {
        return (!empty($this->card_one_uid) && !empty($this->card_two_uid));
    }

    public function hasStudentCard()
    {
        return !empty($this->card_one_uid);
    }

    public function hasSecondCard()
    {
        return !empty($this->card_two_uid);
    }

    public function hasAttendedOn($date)
    {
        ini_set('max_execution_time', 180); //3 minutes

        $date_to_check = '';

        if (is_string($date))
            $date_to_check = $date;
        elseif (is_a($date, 'Carbon'))
            $date_to_check = $date->toDateString();

        $registration = Registration::where('student_id', '=', $this->id)
            ->where('registration_date', '=', $date_to_check)->first();

        return $registration;
    }
}
