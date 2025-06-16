<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use Redirect,Response;
use App\Models\Classroom;
use App\Models\SchoolYear;
use Illuminate\Support\Str;
use App\Mail\ServerUpMailer;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use App\Mail\ServerDownMailer;
use App\Models\ClassAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;


class AdminController extends Controller
{
    
    public function login()
    {
        return view('admin.login');
    }

    public function dashboard()
    {
        $student = User::where('role', 'student')->count('id');
        $prof = User::where([
            ['role', 'professor'],
            ['approved', 1]
        ])->count('id');
        $class = Classroom::count('id');
        return view('admin.dashboard', compact('student', 'prof', 'class'));
    }

    public function archived(){
        $data = User::onlyTrashed()->paginate(10);
        return view('admin.archived')->with('data', $data);
    }

    public function archivedProfile($token){
        $user = User::withTrashed()->where('token', $token)->first();
        return view('admin.archived-profile', compact('user', 'token'));
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'UserName' => 'required',
            'password' => 'required'
        ]);     

        $data = ([
            'username' => $request->UserName,
            'password' => $request->password,
            'role' => 'admin'
        ]);


        if (auth()->attempt($data)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'UserName' => 'The provided credentials do not match our records.',
        ]);
    }

    public function registrations()
    {
        $data = User::where([
            'role' => 'professor',
            'approved' => false
        ])->paginate(10);
        return view('admin.registrations')->with('data', $data);
    }

    public function professors()
    {
        $data = User::where([
            'role' => 'professor',
            'approved' => true
        ])->paginate(10);
        return view('admin.professors')->with('data', $data);
    }

    public function students()
    {
        $data = User::where('role', 'student')->paginate(10);
        return view('admin.students')->with('data', $data);
    }

    public function admins(){
        $data = User::where('role', 'admin')->paginate(10);
        return view('admin.admins')->with('data', $data);
    }

    public function edit(Request $request){
        $user = User::where('token', $request->token)->first();
        return response()->json(['data'=>$user]);
    }

    public function classes(){
        $data = Classroom::paginate(10);
        return view('admin.classes');
    }

    public function classView($token){
        $subject = Classroom::where('class_token', $token)->first();       

        $student = User::withTrashed()->where([
            'role' => 'student',
            'section' => $subject->class_section,
        ])->get();
        $students = $student->count();

        $class = ClassSession::where(['class_token' => $token])->get();
        $session = $class->count();

        $temp = ClassAttendance::where(['class_token' => $token, 'attendance_day' => Carbon::now()->format('Y-m-d')])->get();
        $attendance = ($temp->count() != 0) ? $temp->count() / $students * 100 : 0;
        $attendance = round($attendance);
        return view('admin.class-view', compact('subject', 'students', 'session', 'attendance'));
    }

    public function profile($token){
        $user = User::where('token', $token)->first();
        return view('admin.professors-profile', compact('user', 'token'));
    }

    public function settings(){
        $currentSchoolYear = SchoolYear::latest()->first()->year;
        $nextSchoolYear = (int)substr($currentSchoolYear, 0, 4) + 1 . '-' . (int)substr($currentSchoolYear, 5, 4) + 1;
        return view('admin.settings', compact('currentSchoolYear', 'nextSchoolYear'));
    }
    
    public function maintenance(Request $request)
    {
        $validatedData = $request->validate([
            'Password' => 'required',
        ], [
            'Password.required' => 'Please enter your password.',
        ]);

        $hashedPassword = Auth::user()->password;
        $providedPassword = $validatedData['Password'];
        
        if (Hash::check($providedPassword, $hashedPassword)) {
            if (app()->isDownForMaintenance()){
                Artisan::call('up');
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new ServerUpMailer());
                }
                return redirect()->route('admin.settings')->with('success', 'Maintenance mode is now turned off.');
            } else {
                $randomUUID = (string) Str::uuid();
                Artisan::call('down', [
                    '--secret' => $randomUUID,
                ]);
                $url = '/' . $randomUUID;
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new ServerDownMailer($randomUUID));
                }
                return redirect($url)->with('success', 'Maintenance mode is now on.');
            }
        } else {
            return back()->withErrors([
                'Password' => 'The password you entered is incorrect.',
            ]);
        }
    }

    public function activateSchoolYear(Request $request)
    {
        // Validate the password
        $validatedData = $request->validate([
            'syPassword' => 'required',
        ], [
            'syPassword.required' => 'Please enter your password.',
        ]);

        $currentSchoolYear = SchoolYear::latest()->first()->year;
        $nextSchoolYear = (int)substr($currentSchoolYear, 0, 4) + 1 . '-' . (int)substr($currentSchoolYear, 5, 4) + 1;

        $firstPart = substr($currentSchoolYear, 0, 4);
        
        if ($firstPart <= Carbon::now()->format('Y')) {
            return back()->withErrors([
                'syPassword' => 'The next school year could not be activated because the current school year is not yet over.',
            ]);
        }

        // Check if the password is correct
        if (Hash::check($validatedData['syPassword'], Auth::user()->password)) {
            // Get the current school year and the next school year

            // Create a new school year record for the next school year
            $schoolYear = new SchoolYear;
            $schoolYear->year = $nextSchoolYear;
            $schoolYear->save();

            // Promote all Grade 12
            $grade12 = User::where('section', 'like', '% 12-%')
                ->where('role', 'student')
                ->get();
            foreach ($grade12 as $user) {
                $user->section = 'Graduated';
                $user->save();
                $user->delete(); // Delete the user
            }

            // Promote all Grade 11
            $grade11 = User::where('section', 'like', '% 11-%')
                ->where('role', 'student')
                ->get();


            // Insert or update records in the users table here
            foreach ($grade11 as $student) {
                $newStudent = $student->replicate();
                $student->student_no = $student->student_no . ' (old)';
                $student->save();
                $newStudent->school_year_id = $nextSchoolYear;
                $newStudent->section = str_replace(' 11-', ' 12-', $student->section);
                $newStudent->token = Str::random(20);
                $newStudent->save();
                $student->delete(); // Delete the old record
            }
            
            // Redirect to the settings page with a success message
            return redirect()->route('admin.settings')->with('success', 'The next school year has been activated.');
        } else {
            // Redirect to the settings page with an error message
            return back()->withErrors([
                'syPassword' => 'The password you entered is incorrect.',
            ]);
        }
    }
}
