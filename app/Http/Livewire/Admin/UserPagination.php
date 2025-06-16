<?php

namespace App\Http\Livewire\Admin;

use Request;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Route;

class UserPagination extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'lastname';
    public $sortDirection = 'asc';

    public $listeners = [
        'refreshList' => 'render',
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        } 
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
        return view('livewire.admin.registration-pagination', [
            'data' => User::where([
                'role' => 'professor',
                'approved' => false,
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
