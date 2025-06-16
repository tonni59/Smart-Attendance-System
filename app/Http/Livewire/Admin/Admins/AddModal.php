<?php

namespace App\Http\Livewire\Admin\Admins;

use App\Models\User;
use App\Rules\AlphaSpaces;
use Illuminate\Support\Str;
use LivewireUI\Modal\ModalComponent;

class AddModal extends ModalComponent
{
    public User $user;
    public $firstname;
    public $middleinitial;
    public $lastname;
    public $email;
    public $username;
    public $password;
    public $password_confirmation;

    public function rules (){
        return [
            'firstname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'lastname' => ['required', 'max:30', 'min:2', new AlphaSpaces],
            'middleinitial' => 'max:1|min:0',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|min:3|max:30|unique:users,username',
            'password' => 'required|min:3|max:30|confirmed',
        ];
    }

    protected $validationAttributes = [
        'firstname' => 'first name',
        'middleinitial' => 'middle initial',
        'lastname' => 'last name',
        'username' => 'username',
        'password' => 'password',
    ];

    public function updated($field)
    {
        $this->validateOnly($field);
    }


    public function create()
    {
        $validatedData = $this->validate();

        $data = [
            'firstname' => $validatedData['firstname'],
            'middleinitial' => $validatedData['middleinitial'],
            'lastname' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => bcrypt($validatedData['password']),
            'role' => 'admin',
            'token' => Str::random(20),
            'approved' => 1,
        ];

        User::create($data);
        $this->closeModal();
        return redirect(request()->header('Referer'))->with('success', 'Admin account created successfully!');
    }

    public function render()
    {
        return view('livewire.admin.admins.add-modal');
    }
}
