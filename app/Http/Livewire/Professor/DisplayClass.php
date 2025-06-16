<?php

namespace App\Http\Livewire\Professor;

use Livewire\Component;
use App\Models\Classroom;
use App\Models\SchoolYear;

class DisplayClass extends Component
{
    public $listeners = ['refreshDashboard' => 'render'];
    public $sy;
    public $schoolYear;

    public function mount(){
        $this->sy = SchoolYear::all();
        $temp = SchoolYear::latest()->first()->year;
        $this->schoolYear = $temp;
        $this->latestSy = $temp;
    }

    public function updatedSchoolYear($value){
        if ($value == 'all'){
            $this->schoolYear = '';
        } else {
            $this->schoolYear = $value;

        }     
    }

    public function render()
    {
        $query = Classroom::where('class_prof', auth()->user()->token);

        // If the $schoolYear property is not an empty string, add a where clause to the query
        if ($this->schoolYear) {
            $query->where('class_school_year', $this->schoolYear);
        }

        return view('livewire.professor.display-class', [
            'classes' => $query->get(),
            'param' => '/professor/class/',
        ]);
    }
}
