<?php

namespace App\Http\Livewire\Student;

use LivewireUI\Modal\ModalComponent;

class Terms extends ModalComponent
{
    public function mount($token){
        $this->token = $token;
    }

    public function render()
    {
        return view('livewire.student.terms');
    }
}
