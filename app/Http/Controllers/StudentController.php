<?php

namespace App\Http\Controllers;

use \PDF;
use App\Models\User;
use App\Models\Student;
use App\Rules\AlphaSpaces;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ClassAttendance;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentController extends Controller
{
    public function index()
    {
        return view('students.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'FirstName' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'LastName' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'MiddleInitial' => 'min:0|max:1',
            'Course' => 'required',
            'StudentNumber' => 'required|max:30|min:2',
        ]);

        $studentNumber = $request->input('StudentNumber');
        $data = User::where([
            'student_no' => $studentNumber,
            'firstname' => $request->FirstName,
            'lastname' => $request->LastName,
            'middleinitial' => $request->MiddleInitial,
            'section' => $request->Course,
        ])->first();
        

        if (!$data){
            return redirect()->back()->with('error', 'Invalid Details. Please try again.');
        }else{
            $token = $data->token;
            return redirect('/student/'.$token.'/qrcode');
        }
    }

    public function destroy($token)
    {
        $user = User::where('token', $token)->first();
        ClassAttendance::where('student_token', $user->student_no)->delete();
        $user->delete();
        return redirect()->back()->with('success', 'Student Deleted');
    }

    public function show($token)
    {
        $data = User::where('token', $token)->first();
        return view('students.qr', compact('data'));
    }

    public function download($token)
    {
        $data = User::withTrashed()->where('token', $token)->first();
        $pattern = '/\s*\(.*\)/';
        $data->student_no = preg_replace($pattern, '', $data->student_no);
        $pdf = PDF::loadView('print.qr_print', compact('data'));

        $customPaper = array(0,0,360, 504);

        $pdf->setPaper($customPaper);
        $pdf->render();
        return $pdf->stream('qr_code.pdf');
    }

}
