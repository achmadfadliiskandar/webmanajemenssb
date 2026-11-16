<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;

    public function login(){
        $this->validate([
            'email'     => 'required|email',
            'password'  => 'required'
        ]);
        if (Auth::attempt(['email'=> $this->email, 'password'=>$this->password])) {
            // session()->regenerate();
            return redirect('admin/dashboard');
        }else{
            session()->flash('error', 'Alamat Email atau Password Anda salah!.');
            return redirect()->route('login');
        }
    }

    public function render()
    {
        $judul = 'Login';
        return view('livewire.auth.login')->layout('layouts.app',['judul'=>$judul]);
    }
}
