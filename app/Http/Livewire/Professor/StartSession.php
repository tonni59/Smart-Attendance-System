<?php

namespace App\Http\Livewire\Professor;

use Carbon\Carbon;
use App\Models\Classroom;
use App\Models\ClassSession;
use LivewireUI\Modal\ModalComponent;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StartSession extends ModalComponent
{

    public $endTime;
    public $minutesSeekbar = 5;
    public $token;

    public function startSession(){
        $class = Classroom::where('class_token', $this->token)->first();
        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i:s');
        ClassSession::create([
            'class_token' => $this->token,
            'class_date' => $date,
            'class_start_time' => $time,
            'class_end_time' => Carbon::parse($class->class_end_time)->format('H:i:s'),
            'class_late' => Carbon::parse($time)->addMinutes($this->minutesSeekbar)->format('H:i:s'),
        ]);
        $this->closeModal();
        $this->redirect('/professor/class/'. $this->token .'/start');
    }

    public function mount($token)
    {
        $this->token = $token;
    }

    public function render()
    {
        return view('livewire.professor.start-session');
    }
}
