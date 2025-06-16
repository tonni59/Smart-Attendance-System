<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromView, ShouldAutoSize
{
    public $students;
    public $token;
    public $date;
    public $section;
    
    public function __construct($students, $dates, $token, $section)
    {
        $this->students = $students;
        $this->dates = $dates;
        $this->token = $token;
        $this->section = $section;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('print.excel', [
            'students' => $this->students,
            'dates' => $this->dates,
            'token' => $this->token,
            'section' => $this->section
        ]);
    }
}
