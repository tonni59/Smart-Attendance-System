<?php

namespace App\Http\Livewire\Qr;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Classroom;
use App\Models\ClassAttendance;
use App\Models\ClassSession;

class QrLive extends Component
{
    public $qrlive;
    public $subject;
    public $classroom;
    public $students;
    public $firstname = "";
    public $lastname = "";
    public $middleinitial = "";
    public $student_no = "";
    public $show = "";
    public $lateTime = "";
    public $endTime = "";
    public $status = "";

    protected $rules = [
        'qrlive' => 'required',
    ];

    protected $validationAttributes = [
        'qrlive' => 'QR Code or Student number',
    ];

    public function excused($token){
        $user = User::find($token);

        if (!$user){
            $this->addError('qrlive', 'User not found');
            return;
        }

        $data = [
            'student_token' => $user->student_no,
            'class_token' => $this->subject,
            'attendance_day' => now()->format('Y-m-d'),
            'status' => 'excused'
        ];

        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->middleinitial = $user->middleinitial;
        $this->student_no = $user->student_no;
        $this->status = 'excused';
        $qr_student_section = $user->section;

        $validateSection = Classroom::where([
            'class_token' => $data['class_token'],
            'class_section' => $qr_student_section,
        ])->first();

        if (!$validateSection){
            $this->addError('qrlive', 'Student not allowed to attend this class');
            return;
        }

        if ($user) {
            $test = ClassAttendance::where([
                ['student_token', $this->qrlive],
                ['class_token', $this->subject],
                ['attendance_day', Carbon::now()->format('Y-m-d')],
            ])->first();

            if ($test) {
                $this->addError('qrlive', 'Student already scanned' );
            } else {       
                ClassAttendance::create($data); 
                $this->show = "show";
            }
    
        } else {
            $this->addError('qrlive', 'QR Code or Student number is invalid.');
        }
        $this->qrlive = '';   
    }

    public function qrCode(){
        $this->firstname = "";
        $this->lastname = "";
        $this->middleinitial = "";
        $this->student_no = "";
        $this->show = "";
        $this->validate();
        
        $this->status = Carbon::parse(now()->format('H:i:s'))->greaterThan(Carbon::parse($this->lateTime)) ? 'late' : 'present';

        //If the current time is greater that the end time, redirect to class dashboard with a message of "Class has been ended"
        if (Carbon::parse(now()->format('H:i:s'))->greaterThan(Carbon::parse($this->endTime))){
            return redirect()->route('professors.class.dashboard', $this->subject)->with('error', 'Class has been ended');
        }


        $data = [
            'student_token' => $this->qrlive,
            'class_token' => $this->subject,
            'attendance_day' => now()->format('Y-m-d'),
            'status' => $this->status
        ];

        $user = User::where('student_no', $data['student_token'])->first();
        
        if (!$user){
            $this->addError('qrlive', 'User not found');
            return;
        }

        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->middleinitial = $user->middleinitial;
        $this->student_no = $user->student_no;
        $qr_student_section = $user->section;

        $validateSection = Classroom::where([
            'class_token' => $data['class_token'],
            'class_section' => $qr_student_section,
        ])->first();

        if (!$validateSection){
            $this->addError('qrlive', 'You are not allowed to attend this class');
            return;
        }

        if ($user) {

            $test = ClassAttendance::where([
                ['student_token', $this->qrlive],
                ['class_token', $this->subject],
                ['attendance_day', Carbon::now()->format('Y-m-d')],
            ])->first();

            if ($test) {
                $this->addError('qrlive', 'Student already scanned' );
            } else {       
                ClassAttendance::create($data); 
                $this->show = "show";
            }
    
        } else {
            $this->addError('qrlive', 'QR Code or Student number is invalid.');
        }
        $this->qrlive = '';     
        //Refresh component
        // $this->emit('refreshComponent');
    }

    public function mount($token){
        $this->subject = $token;
        $temp = ClassSession::where('class_token', $token)->first();
        $this->lateTime = $temp->class_late;
        $this->endTime = $temp->class_end_time;
        $this->classroom = Classroom::where('class_token', $token)->first();
    }

    public function render()
    {
        $this->students = User::where([
            'role' => 'student',
            'section' => $this->classroom->class_section,
            'school_year_id' => $this->classroom->class_school_year,
        ])->orderBy('lastname', 'asc')->get();

        return view('livewire.qr.qr-live', [
            'data' => $this->students,
        ]);
    }
}
