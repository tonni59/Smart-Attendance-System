<?php

namespace App\Http\Livewire\Admin\Professor;

use App\Models\User;
use App\Models\VerificationId;
use App\Mail\VerificationMailer;
use Illuminate\Support\Facades\Mail;
use LivewireUI\Modal\ModalComponent;
use Illuminate\Support\Facades\Storage;
use App\Http\Livewire\Admin\UserPagination;

class RegistrationsView extends ModalComponent
{
    public User $user;
    public $token;
    public $photo;
    public $fullname;
    public $email;
    public $username;
    public $user_id;
    public $filename;

    public function reject(){
        if ($this->filename != null){
            $status = 'rejected';
            Mail::to($this->email)->send(new VerificationMailer($status));
            unlink(storage_path('app/photos/'.$this->filename));
            VerificationId::where('user_token', $this->token)->first()->delete();
        }
        $this->closeModal();
    }

    public function approve(){
        if ($this->filename != null) {
            $status = 'approved';
            Mail::to($this->email)->send(new VerificationMailer($status));
            unlink(storage_path('app/photos/'.$this->filename));
            VerificationId::where('user_token', $this->token)->first()->delete();
        }
        User::where('id', $this->user_id)->first()->update(['approved' => 1]);
        $this->closeModalWithEvents([
            UserPagination::getName() => 'refreshList'
        ]);
    }

    public function mount (User $user){
        $this->user = $user;
        $this->user_id = $user->id;
        $this->token = $user->token;
        $this->filename = VerificationId::where('user_token', $user->token)->first();
        if($this->filename != null) {
            $this->photo = url('storage/photos/' . $this->filename->photo);
            $this->filename = $this->filename->photo;
        }
        $this->fullname = $user->lastname . ', ' . $user->firstname . ' ' . $user->middleinitial . '.';
        $this->email = $user->email;
        $this->username = $user->username;
    }

    public function render()
    {
        return view('livewire.admin.professor.registrations-view');
    }
}
