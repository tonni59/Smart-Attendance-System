<?php

namespace App\Http\Livewire;

use App\Models\VerificationId;
use Livewire\Component;
use Livewire\WithFileUploads;

class IdVerification extends Component
{   
    use WithFileUploads;

    public $photo;

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:5120',
        ]);
    }

    protected $validationAttributes = [
        'photo' => 'file',
    ];

    public function save()
    {
        $userToken = auth()->user()->token;
        if($this->photo == null) {
            return redirect(request()->header('Referer'))->with('error', 'Please upload a file.');
        }
        $fileExtension = $this->photo->getClientOriginalExtension();
        $this->photo->storeAs('photos', $userToken . '.' . $fileExtension);
        VerificationId::create([
            'user_token' => $userToken,
            'photo' => $userToken . '.' . $fileExtension,
        ]);
        return redirect(request()->header('Referer'))->with('success', 'Your ID has been submitted for verification.');
    }

    public function render()
    {
        return view('livewire.id-verification');
    }
}
