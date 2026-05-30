<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user()->fresh();

        return view('staff.settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user()->fresh();

        if ($request->aksi === 'informasi') {
            $user->n_kry = $request->n_kry;
            $user->jab = $request->jab;
            $user->alamat = $request->alamat;
            $user->tmpt_lahir = $request->tmpt_lahir;
            $user->tgl_lahir = $request->tgl_lahir;
            $user->tgl_mulai_kerja = $request->tgl_mulai_kerja;
            $user->telp = $request->telp;
            $user->email = $request->email;

            if (!$user->isDirty()) {
                return back()->with('error', 'Tidak ada data yang diubah');
            }

            $user->save();

            return back()->with('success', 'Informasi berhasil diperbarui');
        }

        if ($request->aksi === 'password') {
            if (!$request->filled('current_password')) {
                return back()->with('error', 'Password saat ini wajib diisi');
            }

            if (!$request->filled('new_password')) {
                return back()->with('error', 'Password baru wajib diisi');
            }

            if (!$request->filled('confirm_password')) {
                return back()->with('error', 'Konfirmasi password wajib diisi');
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password saat ini salah');
            }

            if ($request->new_password !== $request->confirm_password) {
                return back()->with('error', 'Konfirmasi password tidak cocok');
            }

            if (Hash::check($request->new_password, $user->password)) {
                return back()->with('error', 'Password baru tidak boleh sama dengan password lama');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return back()->with('success', 'Kata sandi berhasil diperbarui');
        }

        return back()->with('error', 'Aksi tidak valid');
    }
}