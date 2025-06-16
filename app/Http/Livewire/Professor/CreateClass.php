<?php

namespace App\Http\Livewire\Professor;

use App\Models\User;
use App\Models\Classroom;
use App\Models\SchoolYear;
use Illuminate\Support\Str;
use LivewireUI\Modal\ModalComponent;
use App\Rules\ClassEndAfterClassStart;

class CreateClass extends ModalComponent
{
    public $class_name;
    public $class_room;
    public $class_section;
    public $class_start;
    public $class_end;
    public $days = [];
    public $schoolYear;
    public $dropdown;
    public $track;
    public $monday, $tuesday, $wednesday, $thursday, $friday;

    public function updatedTrack($data){
        $this->class_section = $data;
    }

    public $messages = [
        'class_end.after_or_equal' => 'The :attribute field must be at least 1 hour after the :other field.',
    ];

    public function updated($field){
        $checked_days = [];

        if ($this->monday) {
            $checked_days[] = 'Monday';
        }
        
        if ($this->tuesday) {
            $checked_days[] = 'Tuesday';
        }

        if ($this->wednesday) {
            $checked_days[] = 'Wednesday';
        }

        if ($this->thursday) {
            $checked_days[] = 'Thursday';
        }

        if ($this->friday) {
            $checked_days[] = 'Friday';
        }

        $this->days = implode(', ', $checked_days);

        $this->validateOnly($field, [
            'class_name' => 'required|min:3|max:30',
            'class_room' => 'required|numeric',
            'class_section' => 'required',
            'days' => 'required',
            'class_start' => 'required',
            'class_end' => ['required', new ClassEndAfterClassStart($this->class_start)],
        ]);
    }

    public function mount(){
        $this->dropdown = ['ICT 11-A', 'ICT 11-B', 'ICT 12-A', 'ICT 12-B', 'GAS 11-A', 'GAS 11-B', 'GAS 12-A', 'GAS 12-B', 'HUMSS 11-A', 'HUMSS 11-B', 'HUMSS 12-A', 'HUMSS 12-B', 'STEM 11-A', 'STEM 11-B', 'STEM 12-A', 'STEM 12-B', 'ABM 11-A', 'ABM 11-B', 'ABM 12-A', 'ABM 12-B', 'SPORT 11-A', 'SPORT 11-B', 'SPORT 12-A', 'SPORT 12-B'];
        $this->class_section = $this->dropdown[0];
        $this->schoolYear = SchoolYear::latest()->first()->year;
    }

    public function createClass(){
        $checked_days = [];

        if ($this->monday) {
            $checked_days[] = 'Monday';
        }
        
        if ($this->tuesday) {
            $checked_days[] = 'Tuesday';
        }

        if ($this->wednesday) {
            $checked_days[] = 'Wednesday';
        }

        if ($this->thursday) {
            $checked_days[] = 'Thursday';
        }

        if ($this->friday) {
            $checked_days[] = 'Friday';
        }

        $this->days = implode(', ', $checked_days);

        $this->validate([
            'class_name' => 'required|min:3|max:30',
            'class_room' => 'required|numeric',
            'class_section' => 'required',
            'days' => 'required',
            'class_start' => 'required',
            'class_end' => ['required', new ClassEndAfterClassStart($this->class_start)],
        ]);

        $data = [
            'class_name' => $this->class_name,
            'class_room' => $this->class_room,
            'class_section' => $this->class_section,
            'class_prof' => auth()->user()->token,
            'class_token' => Str::random(20),
            'class_school_year' => $this->schoolYear,
            'class_days' => $this->days,
            'class_start_time' => $this->class_start,
            'class_end_time' => $this->class_end,
        ];
        
        Classroom::create($data);
        $this->closeModal();
        return redirect(request()->header('Referer'))->with('success', 'Class created successfully!');
    }

    public function updateSelect($data){
        $this->class_section = $data;
    }

    public function render()
    {
        return view('livewire.professor.create-class');
    }
}
