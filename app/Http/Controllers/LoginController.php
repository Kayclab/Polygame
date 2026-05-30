<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function view() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        $credentials = $request->only('email', 'password');

        $karyawan = karyawan::where('email', $credentials['email'])->first();

        if ($karyawan && Hash::check($credentials['password'], $karyawan->password)) {
            Auth::login($karyawan);

            // TAMBAHKAN INI:
            $request->session()->regenerate();

            if($karyawan->role === 'owner') {
                return redirect()->route('owner.index'); 
            } else if ($karyawan->role === 'staff') {
                return redirect()->intended('/staff');
            }
        }

        return back()->withErrors(['email' => 'Email atau Password Salah']);
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }
}
