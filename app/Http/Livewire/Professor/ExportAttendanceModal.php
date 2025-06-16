<?php

namespace App\Http\Livewire\Professor;

use PDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Classroom;
use LivewireUI\Modal\ModalComponent;

class ExportAttendanceModal extends ModalComponent
{
    public function mount($classToken, $classDate){
        $this->classToken = $classToken;
        $this->classDate = $classDate;
        $class = Classroom::where('class_token', $this->classToken)->first();
        $this->classSection = $class->class_section;
        $this->classSubject = $class->class_name;
        $this->classSchoolYear = $class->class_school_year;
        $tmp_prof = $class->class_prof;
        $tmp = User::where('token', $tmp_prof)->first();
        $this->professor_name = $tmp->firstname . ' ' . $tmp->lastname;
    }

    public function exportToday(){    
        $date = $this->classDate;
        $token = $this->classToken;
        $professor_name = $this->professor_name;
        $class_section = $this->classSection;
        $subject = $this->classSubject;
        $data = User::withTrashed()->where([
            'section' => $this->classSection,
            'role' => 'student',
            'school_year_id' => $this->classSchoolYear
        ])->orderBy('lastname', 'asc')->get()->toArray();
        $pdfContent = PDF::loadView('print.individual_attendance', compact('data', 'token', 'professor_name', 'class_section', 'date', 'subject'))->output();
        return response()->streamDownload(
            fn () => print($pdfContent),
            $subject . " - " . $class_section . " (" . $date . ")" .  ".pdf"
        );
    }

    public function exportWeekly(){
        $class_section = $this->classSection;
        $subject = $this->classSubject;
        $professor_name = $this->professor_name;
        $date = Carbon::now()->format('Y-m-d');
        $firstday = Carbon::now()->subDays(6)->format('Y-m-d');
        $token = $this->classToken;
        $data = User::withTrashed()->where([
            'section' => $this->classSection,
            'role' => 'student',
            'school_year_id' => $this->classSchoolYear
        ])->orderBy('lastname', 'asc')->get()->toArray();
        $pdfContent = PDF::loadView('print.weekly_attendance', compact('data', 'token', 'professor_name', 'class_section', 'date', 'subject'))->setPaper('A4', 'landscape')->output();
        return response()->streamDownload(
            fn () => print($pdfContent),
            $subject . " - " . $class_section . " (" . $firstday . " to " . $date . ")" .  ".pdf"
        );
    }

    public function render()
    {
        return view('livewire.professor.export-attendance-modal');
    }
}
