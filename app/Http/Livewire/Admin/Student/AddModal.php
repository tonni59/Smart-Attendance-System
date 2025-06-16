<?php

namespace App\Http\Livewire\Admin\Student;

use App\Models\SchoolYear;
use App\Models\User;
use App\Rules\AlphaSpaces;
use Illuminate\Support\Str;
use LivewireUI\Modal\ModalComponent;

class AddModal extends ModalComponent
{
    public User $user;
    public $firstname;
    public $middleinitial;
    public $lastname;
    public $student_no;
    public $section = 'ICT 11-A';
    public $tracks = ['ICT 11-A', 'ICT 11-B', 'ICT 12-A', 'ICT 12-B', 'GAS 11-A', 'GAS 11-B', 'GAS 12-A', 'GAS 12-B', 'HUMSS 11-A', 'HUMSS 11-B', 'HUMSS 12-A', 'HUMSS 12-B', 'STEM 11-A', 'STEM 11-B', 'STEM 12-A', 'STEM 12-B', 'ABM 11-A', 'ABM 11-B', 'ABM 12-A', 'ABM 12-B', 'SPORT 11-A', 'SPORT 11-B', 'SPORT 12-A', 'SPORT 12-B'];

    public function rules () {
        return [
            'firstname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'middleinitial' => 'min:0|max:1',
            'lastname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'student_no' => 'required|min:3|max:30|unique:users,student_no|regex:/^02[0-9]{9}$/',
            'section' => 'required',
        ];
    }

    public function updated($field)
    {
        $this->validateOnly($field);
    }

    public function messages()
    {
        return [
            'student_no.regex' => 'The :attribute must be a valid student ID.',
        ];
    }

    protected $validationAttributes = [
        'firstname' => 'first name',
        'middleinitial' => 'middle initial',
        'lastname' => 'last name',
        'student_no' => 'student number',
        'section' => 'section',
    ];

    public function create()
    {
        $validatedData = $this->validate();
        
        $data = [
            'firstname' => $validatedData['firstname'],
            'middleinitial' => $validatedData['middleinitial'],
            'lastname' => $validatedData['lastname'],
            'student_no' => $validatedData['student_no'],
            'section' => $validatedData['section'],
            'role' => 'student',
            'token' => Str::random(20),
            'school_year_id' => SchoolYear::latest()->first()->year,
        ];

        User::create($data);
        $this->closeModal();
        return redirect(request()->header('Referer'))->with('success', 'Student added successfully!');
    }


    public function render()
    {
        return view('livewire.admin.student.add-modal', [
            'tracks' => $this->tracks,
        ]);
    }
}
