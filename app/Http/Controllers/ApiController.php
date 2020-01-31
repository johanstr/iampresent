<?php

namespace App\Http\Controllers;

use App\Borrowedproduct;
use App\Group;
use App\Jobs\SendReport;
use App\Mail\AttendeesReportingMail;
use App\Registration;
use App\Repositories\ReportRepository;
use App\Student;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function getAllGroups()
    {
        $groups = Group::all();

        return response()->json($groups);
    }

    public function getGroupsByCohort($cohort)
    {
        $groups = Group::where('cohort', '=', $cohort)->get();

        return response()->json($groups);
    }

    public function getStudent($card_uid)
    {
        $student_result = [];

        $student = Student::with('group')
            ->where('card_one_uid', '=', $card_uid)
            ->orWhere('card_two_uid', '=', $card_uid)
            ->first();


        if($student) {
            $student_result[] = [
                'id' => $student->id,
                'first_name' => $student->first_name,
                'prefixes' => $student->prefixes,
                'last_name' => $student->last_name,
                'student_number' => sprintf("%08d", $student->student_number),
                'gender' => $student->gender,
                'card_one_uid' => $student->card_one_uid,
                'card_two_uid' => $student->card_two_uid,
                'group' => $student->group,
                'photo' => $student->getPhoto(),
                'status' => true
            ];
        } else {
            $student_result[] = [
                'status' => false,
                'message' => "Kaart met nummer $card_uid niet gevonden!"
            ];
        }

        return response()->json($student_result);
    }

    public function getAllStudents()
    {
        //ini_set('max_execution_time', 360);
        $raw_students = Student::all();
        $raw_students->load('group');

        $students = [];

        foreach($raw_students as $raw_student) {
            $students[] = [
                'id' => $raw_student->id,
                'student_number' => $raw_student->student_number,
                'first_name' => utf8_encode($raw_student->first_name),
                'prefixes' => utf8_encode($raw_student->prefixes),
                'last_name' => utf8_encode($raw_student->last_name),
                'full_name' => utf8_encode($raw_student->getFullName()),
                'group' => $raw_student->group,
                'card_one_uid' => (is_null($raw_student->card_one_uid) || empty($raw_student->card_one_uid) ? '' : $raw_student->card_one_uid),
                'card_two_uid' => (is_null($raw_student->card_two_uid) || empty($raw_student->ocard_two_uid) ? '' : $raw_student->card_two_uid),
                'has_card_one' => $raw_student->hasStudentCard(),
                'has_card_two' => $raw_student->hasSecondCard(),
                'photo' => $raw_student->getPhoto()
            ];
        }

        return response() //->json($students)->header('Content-Type', 'application/json');
           ->json($students, 200, array('Content-Type' => 'application/json'));
        // , JSON_UNESCAPED_UNICODE
    }


    // Request $request
    public function registerInOut(Request $request)
    {
        $body = json_decode($request->getContent());
        $card_uid = $body->card_uid;

        $result = [];

        // Get date and time
        $dt = Carbon::now();

        // Get student first
        $student = Student::with('group')
            ->where('card_one_uid', '=', $card_uid)
            ->orWhere('card_two_uid', '=', $card_uid)
            ->first();

        if($student) {

            // Search if there is already an IN registration
            $registration = Registration::where('student_id', '=', $student->id)
                ->where('registration_date', '=', $dt->toDateString())->first();

            if (empty($registration) || is_null($registration)) {
                // No registration yet
                $registration = new Registration;

                $registration->registration_time_in = $dt->toTimeString();
                $registration->registration_date = $dt->toDateString();
                $registration->student_id = $student->id;
                $registration->permitted_absence = 0;
                $registration->save();
            } else {
                // There is already an IN registration, so we will check-out the student
                $registration->registration_time_out = $dt->toTimeString();
                $registration->permitted_absence = 0;
                $registration->update();
            }

            $result[] = [
                'name' => $student->getFullName(),
                'student_number' => sprintf("%08d", $student->student_number),
                'group' => $student->group,
                'photo' => $student->getPhoto(),
                'registration_time_in' => $registration->registration_time_in,
                'registration_time_out' => empty($registration->registration_time_out) || is_null($registration->registration_time_out) ? '' : $registration->registration_time_out,
                'status' => true
            ];
        } else {
            $result[] = [
                'status' => false,
                'message' => "Kaart met nummer <span class=\"highlight-message\">$card_uid</span> niet gevonden!"
            ];
        }

        return response()->json($result);

    }

    public function saveCards(Request $request)
    {
        $requestBody = json_decode($request->getContent());

        $id = $requestBody->student_id;
        $card_one = $requestBody->card_uid;
        $card_two = $requestBody->ov_uid;

        $result = [];

        $student = Student::find($id);

        if($student) {
            if ($student->card_uid != $card_one || $student->ov_uid != $card_two) {
                $student->card_uid = empty($card_one) ? null : $card_one;
                $student->ov_uid = empty($card_two) ? null : $card_two;
                $student->update();

                $result = [
                    'id' => $id,
                    'new_card_uid' => $card_one,
                    'new_ov_uid' => $card_two,
                    'message' => 'Studentgegevens opgeslagen!',
                    'status' => true
                ];
            }
        } else {
            $result = [
                'status' => false,
                'message' => 'Student niet gevonden!'
            ];
        }

        return response()->json($result);
    }

    public function sendReport($daypart, $date)
    {

        SendReport::dispatch($daypart, $date);

        return response()->json(
            [
                'message' => 'Mail wordt verzonden!',
                'to' => config('iampresent.absentie-coordinator.naam'),
                'mail' => config('iampresent.absentie-coordinator.email')
            ]);
    }

    public function saveManualRegistration(Request $request)
    {
       $requestBody = json_decode($request->getContent());

       $id = $requestBody->student_id;
       $date = $requestBody->date;
       $time_in = $requestBody->time_in;
       $time_out = $requestBody->time_out;
       $permitted_absence = $requestBody->permitted_absence;

       // Eerst zoeken of er al een registratie is voor deze student op deze datum
       $registration_found = Registration::where('student_id', '=', $id)
                                          ->where('registration_date', '=', $date)
                                          ->first();

       if(empty($registration_found) || is_null($registration_found)) {
          // er is nog geen registratie
          $registration = new Registration();
          $registration->student_id = $id;
          $registration->registration_date = $date;
          $registration->registration_time_in = $time_in;
          $registration->registration_time_out = (empty($time_out) || is_null($time_out) ? null : $time_out);
          $registration->permitted_absence = $permitted_absence;
          $registration->save();
       } else {
          // Er is al een registratie, dus uitchecken registreren
          if(!empty($time_out)) {
             $registration_found->registration_time_out = $time_out;
             $registration_found->update();
          } else {
             $registration_found->permitted_absence = $permitted_absence;
             $registration_found->update();
          }
       }

       return response()->json([ "id" => $id, "date" => $date, "time_in" => $time_in, "time_out" => $time_out ]);
    }


    public function saveStudent(Request $request)
    {
       $requestBody = json_decode($request->getContent());

       $group = Group::find($requestBody->group_id);
       $student = Student::where('student_number', '=', $requestBody->student_number)->first();

       if(is_null($student) || empty($student)) {
          if (!is_null($group) || !empty($group)) {
             $new_student = new Student();
             $new_student->student_number = $requestBody->student_number;
             $new_student->first_name = $requestBody->first_name;
             $new_student->prefixes = $requestBody->prefixes;
             $new_student->last_name = $requestBody->last_name;
             $new_student->group_id = $requestBody->group_id;
             $new_student->card_uid = $requestBody->card_one;
             $new_student->ov_uid = $requestBody->card_two;

             $new_student->save();

             return response()->json([
                'msg' => 'Student saved',
                'id' => $new_student->id,
                'student_number' => $new_student->student_number,
                'first_name' => $new_student->first_name,
                'prefixes' => $new_student->prefixes,
                'last_name' => $new_student->last_name,
                'group' => $new_student->group
             ])->setStatusCode(201, 'Created');
          } else {
             return response()->json(['msg' => 'Student not saved. Group not found!'])
                  ->setStatusCode(404, 'Not found');
          }
       }

       return response()->json(['msg' => 'Student with the number ' . $requestBody->student_number . ' already exists'])
            ->setStatusCode(406, 'Not Acceptable');
    }

    public function saveGroup(Request $request)
    {
       $requestBody = json_decode($request->getContent());

       $group = Group::where('group_name', '=', $requestBody->group_name)
                     ->where('cohort', '=', $requestBody->cohort)
                     ->first();

       if(is_null($group) || empty($group)) {
          $new_group = new Group();
          $new_group->group_name = $requestBody->group_name;
          $new_group->cohort = $requestBody->cohort;
          $new_group->daypart = 0;
          $new_group->timeschedule_id = 0;
          $new_group->save();

          return response()->json([
             'msg' => 'Group saved',
             'id' => $new_group->id,
             'group' => $new_group->group
          ])->setStatusCode(201, 'Created');
       }

       return response()->json(['msg' => 'Group with this name and cohort already exists'])
            ->setStatusCode(409, 'Conflict');
    }

    public function getRegistrationOfStudent($date, $id)
    {
       $search_date = Carbon::parse($date);

       $registration = Registration::where('registration_date', '=', $search_date->format("Y-m-d"))
                                    ->where('student_id', '=', $id)->first();

       return response()->json([
          'search_date' => $search_date->format("Y-m-d"),
          'student_id' => $id,
          'registration' => $registration
       ])->setStatusCode(200);
       //if($registration)
         //return response()->json($registration)->setStatusCode(200);

       //return response()->json([ 'msg' => 'No registration for this date and/or student found'])->setStatusCode(204);
    }

    public function getOpenBorrows($student_id)
    {
       $borrowList = [];

       $borrowsCollection = Borrowedproduct::where('student_id', '=', $student_id)
          ->where('returned_at', '=', null)
          ->get();

       if($borrowsCollection) {
          foreach($borrowsCollection as $borrowItem) {
             $borrowList[] = [
                'product_id' => $borrowItem->product_id,
                'product_name' => $borrowItem->product->name,
                'product_image' => $borrowItem->product->image(),
                'borrowed_at' => $borrowItem->borrowed_at,
                'returned_at' => $borrowItem->returned_at
             ];
          }
       }

       return response()->json($borrowList, 200);
    }
}
