<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Rules\AlphaSpaces;

class StudentRegistrationForm extends Component
{
    public $tracks = ['ICT 11-A', 'ICT 11-B', 'ICT 12-A', 'ICT 12-B', 'GAS 11-A', 'GAS 11-B', 'GAS 12-A', 'GAS 12-B', 'HUMSS 11-A', 'HUMSS 11-B', 'HUMSS 12-A', 'HUMSS 12-B', 'STEM 11-A', 'STEM 11-B', 'STEM 12-A', 'STEM 12-B', 'ABM 11-A', 'ABM 11-B', 'ABM 12-A', 'ABM 12-B', 'SPORT 11-A', 'SPORT 11-B', 'SPORT 12-A', 'SPORT 12-B'];
    public $firstname, $lastname, $middleinitial, $student_no, $course;

    public function attributes()
    {
        return [
            'student_no' => 'student number',
        ];
    }

    public function messages()
    {
        return [
            'student_no.regex' => 'The :attribute must be a valid student ID.',
        ];
    }

    public function rules(){
        return [
            'firstname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'lastname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'middleinitial' => 'min:0|max:1',
            'course' => 'required',
            'student_no' => 'required|max:30|min:2|regex:/^02[0-9]{9}$/',
        ];
    }
    

    public function updated($field)
    {
        $this->validateOnly($field);
    }
    
    public function render()
    {
        return view('livewire.student-registration-form');
    }
}
