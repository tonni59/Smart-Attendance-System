<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ArchivedPagination extends Component
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

    public function updatedSearch()
    {
        $this->resetPage();
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
        return view('livewire.admin.archived-pagination', [
            'data' => User::onlyTrashed()->where('role', 'professor')->search([
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
