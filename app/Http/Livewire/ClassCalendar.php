<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Classroom;
use App\Models\ClassSession;
use App\Models\ClassAttendance;
use Illuminate\Support\Collection;
use Asantibanez\LivewireCalendar\LivewireCalendar;

class ClassCalendar extends LivewireCalendar
{   
    public $token;
    public $user_events;
    public $saved_token = '';

    public function goToPreviousMonth()
    {
        $this->startsAt->subMonthNoOverflow();
        $this->endsAt->subMonthNoOverflow();

        $this->calculateGridStartsEnds();
        $this->emit('render');
    }
    
    public function events() : Collection
    {
        $this->token = request()->segment(3);
        if ($this->saved_token == ''){
            $this->saved_token = $this->token;
        }

        

        $this->user_events = ClassSession::where('class_token', $this->saved_token)
        ->whereDate('class_date', '>=', $this->gridStartsAt)
        ->whereDate('class_date', '<=', $this->gridEndsAt)
        ->get()
        ->map(function (ClassSession $class) {
            $perDay = ClassAttendance::where([
                'class_token' => $this->saved_token, 
                'attendance_day' => $class->class_date])
                ->count();
            $tmp = Classroom::where('class_token', $this->saved_token)->first();
            $section = User::withTrashed()->where(['section' => $tmp->class_section, 'role' => "student", 'school_year_id' => $tmp->class_school_year])->count();
            $percent = ($perDay / $section) * 100;

            return [
                'id' => $class->id,
                'title' => "Attendance",
                'description' => round($percent) . "%",
                'date' => $class->class_date,
            ];
        });
        
        return $this->user_events;
    }

    public function onEventClick($eventId)
    {
        $data = ClassSession::findOrfail($eventId);
        return redirect('/professor/class/' . $this->token . '/calendar/' . $data->class_date);
    }
}