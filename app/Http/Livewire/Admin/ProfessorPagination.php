<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ProfessorPagination extends Component
{
    use WithPagination;
    public $search = '';
    public $sortField = 'lastname';
    public $sortDirection = 'asc';

    public $listeners = ['refreshProfessors' => 'render'];

    public function paginationView()
    {
        return 'pagination::default';
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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.professor-pagination', [
            'data' => User::where([
                'role' => 'professor',
                'approved' => true,
            ])->search([
                'firstname',
                'lastname',
                'username',
                'email',
            ], $this->search)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10)
        ]);
    }
}
