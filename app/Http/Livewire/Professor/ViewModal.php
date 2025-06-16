<?php

namespace App\Http\Livewire\Professor;

use App\Models\User;
use LivewireUI\Modal\ModalComponent;

class ViewModal extends ModalComponent
{
    public User $user;
    public $firstname;
    public $lastname;
    public $middleinitial;
    public $section;
    public $student_no;
    public $token;

    public function mount($user)
    {
        $pattern = '/\s*\(.*\)/';
        $this->user = User::withTrashed()->find($user);
        $this->student_no = preg_replace($pattern, '', $this->user->student_no); 
        $this->firstname = $this->user->firstname;
        $this->lastname = $this->user->lastname;
        $this->middleinitial = $this->user->middleinitial;
        $this->section = $this->user->section;  
        $this->token = $this->user->token;
    }

    public function render()
    {
        return view('livewire.professor.view-modal');
    }
}
