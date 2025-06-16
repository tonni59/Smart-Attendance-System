<?php

namespace App\Http\Livewire\Admin;

use \PDF;
use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class StudentPagination extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'lastname';
    public $sortDirection = 'asc';
    public $showEditModal = false;
    public $filterString = '';

    public $listeners = [
        'refreshStudents' => 'render',
        'getFilter' => 'getFilter'
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

    public function exportStudents(){
        $date = Carbon::now()->format('F d, Y'); 
        if ($this->filterString == ''){
            $data = User::where('role', 'student')
            ->search([
                'student_no',
                'firstname',
                'lastname',
                'section',
            ], $this->search)->get();
        } else {
            $data = User::where([
                ['role', 'student'],
                ['section', $this->filterString]
            ])
            ->search([
                'student_no',
                'firstname',
                'lastname',
            ], $this->search)->get();
        }
        $pdfContent = PDF::loadView('print.student_list', compact('data', 'date'))->output();

        return response()->streamDownload(
             fn () => print($pdfContent),
             'student_list.pdf'
        );
    }

    public function getFilter($string)
    {   
        $this->filterString = $string;
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
        if ($this->filterString == ''){
            $data = User::where('role', 'student')
            ->search([
                'student_no',
                'firstname',
                'lastname',
                'section',
            ], $this->search);
        } else {
            $data = User::where([
                ['role', 'student'],
                ['section', $this->filterString]
            ])
            ->where('section', $this->filterString)
            ->search([
                'student_no',
                'firstname',
                'lastname',
            ], $this->search);
        }

        return view('livewire.admin.student-pagination', [
            'data' => $data            
            ->orderBy($this->sortField, $this->sortDirection)
        ->paginate(10)
        ]);
    }
}