<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Classroom;
use App\Models\SchoolYear;
use Livewire\WithPagination;

class DisplayClass extends Component
{
    use WithPagination;
    public $search = '';
    public $sy;
    public $schoolYear;

    public function mount(){
        $this->sy = SchoolYear::all();
        $temp = SchoolYear::latest()->first()->year;
        $this->schoolYear = $temp;
        $this->latestSy = $temp;
    }

    public function paginationView()
    {
        return 'pagination::default';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Classroom::query();

        // If the $schoolYear property is not an empty string, add a where clause to the query
        if ($this->schoolYear != 'all') {
            $query->where('class_school_year', $this->schoolYear);
        }

        return view('livewire.admin.display-class', [
            'classes' => $query->search([
                'class_name',
                'class_section',
                'class_prof',
                'class_room',
            ], $this->search)->paginate(12),
            'param' => '/admin/classes/',
        ]);
    }
}
