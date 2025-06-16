<?php

namespace App\Http\Livewire\Admin\Student;

use App\Models\User;
use App\Rules\AlphaSpaces;
use LivewireUI\Modal\ModalComponent;
use App\Http\Livewire\Admin\StudentPagination;

class EditModal extends ModalComponent
{
    public User $user;
    public $firstname;
    public $lastname;
    public $middleinitial;
    public $section;
    public $student_no;
    public $tracks = ['ICT 11-A', 'ICT 11-B', 'ICT 12-A', 'ICT 12-B', 'GAS 11-A', 'GAS 11-B', 'GAS 12-A', 'GAS 12-B', 'HUMSS 11-A', 'HUMSS 11-B', 'HUMSS 12-A', 'HUMSS 12-B', 'STEM 11-A', 'STEM 11-B', 'STEM 12-A', 'STEM 12-B', 'ABM 11-A', 'ABM 11-B', 'ABM 12-A', 'ABM 12-B', 'SPORT 11-A', 'SPORT 11-B', 'SPORT 12-A', 'SPORT 12-B'];

    public function updated($field)
    {
        $this->validateOnly($field, [
            'firstname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'middleinitial' => 'min:0|max:1',
            'lastname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'student_no' => 'required|min:3|max:30|unique:users,student_no,' . $this->user->id . ',id|regex:/^02[0-9]{9}$/',
            'section' => 'required',
        ]);
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

    public function update()
    {
        $this->validate([
            'firstname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'middleinitial' => 'min:0|max:1',
            'lastname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'student_no' => 'required|min:3|max:30|unique:users,student_no,' . $this->user->id . ',id',
            'section' => 'required',
        ]);

        $this->user->update([
            'firstname' => $this->firstname,
            'middleinitial' => $this->middleinitial,
            'lastname' => $this->lastname,
            'student_no' => $this->student_no,
            'section' => $this->section,
        ]);

        $this->closeModalWithEvents([
            StudentPagination::getName() => 'refreshStudents'
        ]);
    }

    public function mount(User $user)
    {

        $this->user = $user;
        $this->student_no = $user->student_no;
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->middleinitial = $user->middleinitial;
        $this->section = $user->section;

    }

    public function render()
    {
        return view('livewire.admin.student.edit-modal');
    }
}
