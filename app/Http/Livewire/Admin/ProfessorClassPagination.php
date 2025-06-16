<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Classroom;
use Livewire\WithPagination;

class ProfessorClassPagination extends Component
{
    use WithPagination;
    public $search = '';

    public function paginationView()
    {
        return 'pagination::default';
    }

    public function mount($token){
        $this->token = $token;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        return view('livewire.admin.professor-class-pagination', [
            'classes' => Classroom::where('class_prof', $this->token)->paginate(9)
        ]);
    }
}
