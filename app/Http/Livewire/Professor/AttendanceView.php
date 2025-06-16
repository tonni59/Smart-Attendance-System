<?php

namespace App\Http\Livewire\Professor;

use \PDF;
use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Classroom;
use App\Models\ClassStudent;
use Livewire\WithPagination;

class AttendanceView extends Component
{
    use WithPagination;
    public ClassStudent $class;
    public $search = '';
    public $sortField = 'lastname';
    public $sortDirection = 'asc';
    public $classSection;
    public $classToken;
    public $classDate;

    public $listeners = ['refreshProfessors' => 'render'];

    public function paginationView()
    {
        return 'pagination::default';
    }

    public function mount(Classroom $class)
    {
        $this->classDate = request()->segment(5);
        $this->classSection = $class->class_section;
        $this->classToken = $class->class_token;
        $this->classSchoolYear = $class->class_school_year;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        } 
    }

    public function render()
    {
        return view('livewire.professor.attendance-view', [
            'data' => User::withTrashed()->where(['section' => $this->classSection, 'role' => 'student', 'school_year_id' => $this->classSchoolYear])
            ->search([
                'student_no',
                'firstname',
                'lastname',
                'section',
            ], $this->search)
            
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10)
        ]);
    }
}
