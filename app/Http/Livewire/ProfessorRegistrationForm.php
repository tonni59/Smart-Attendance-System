<?php

namespace App\Http\Livewire;

use Validator;
use Livewire\Component;
use App\Rules\AlphaSpaces;

class ProfessorRegistrationForm extends Component
{
    public $firstname, $lastname, $middleinitial, $email, $Password, $Password_confirmation, $username;

    public function rules (){
        return [
            'firstname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'lastname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'middleinitial' => 'max:1|min:0',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|max:30|min:2|unique:users,username',
            'Password' => 'required|max:30|min:6',
            'Password_confirmation' => 'required|max:30|min:6|required_with:Password|same:Password',
        ];
    }

    public function updated($field)
    {
        // if($field == 'email' && $this->email != null){
        //     $tmp = $this->email;
        //     $this->email = $this->email . '@gmail.com';
        //     $validator = Validator::make(['email' => $this->email], ['email' => 'required|email|unique:users,email']);
        //     if($validator->fails()){
        //         $this->email = $tmp;
        //         dd('here');
        //     }
        // }
        // else{
            $this->validateOnly($field);
        // }
    }

    public function render()
    {
        return view('livewire.professor-registration-form');
    }
}
