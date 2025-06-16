<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AdminPagination extends Component
{
    use WithPagination;
    public $search = '';
    public $sortField = 'lastname';
    public $sortDirection = 'asc';

    public $listeners = ['refreshAdmins' => 'render'];

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
        return view('livewire.admin.admin-pagination', [
            'data' => User::where([
                'role' => 'admin',
            ])->search([
                'firstname',
                'lastname',
                'username',
            ], $this->search)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10)
        ]);
    }
}
