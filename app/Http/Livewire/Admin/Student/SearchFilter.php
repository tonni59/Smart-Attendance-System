<?php

namespace App\Http\Livewire\Admin\Student;

use LivewireUI\Modal\ModalComponent;
use App\Http\Livewire\Admin\StudentPagination;

class SearchFilter extends ModalComponent
{
    public $track;
    public $grade;
    public $section;

    public function filter(){
        $this->validate([
            'track' => 'required',
            'grade' => 'required',
            'section' => 'required',
        ]);

        $string = $this->track . " " .$this->grade . "-" . $this->section;
        $this->emit('getFilter', $string)->component(StudentPagination::class);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.admin.student.search-filter');
    }
}
