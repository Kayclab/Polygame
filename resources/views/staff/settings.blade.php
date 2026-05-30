@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
    <div class="px-4 sm:px-8 lg:px-16 xl:px-24 py-8 max-w-6xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-10">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="1.33" viewBox="0 0 16 16"><path d="M4.67 1.33h6.66a1 1 0 011 1v11.34a1 1 0 01-1 1H4.67a1 1 0 01-1-1V2.33a1 1 0 011-1z" /></svg>
                <span class="text-[10px] font-bold uppercase tracking-widest text-purple-600">Panel Kontrol Staff</span>
            </div>
            <h1 class="font-mono text-3xl font-bold text-zinc-900 leading-tight">Pengaturan</h1>
            <p class="mt-2 text-sm text-gray-500 max-w-xl leading-relaxed">Kelola profil akun dan informasi anda.</p>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">{{ session('error') }}</div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 items-start">
                {{-- Informasi Profil Card --}}
                <div class="bg-white rounded-[2rem] border border-zinc-100 shadow-sm p-8 flex flex-col gap-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 opacity-60">
                            <svg class="w-4 h-4 text-zinc-900" fill="none" stroke="currentColor" stroke-width="1.33" viewBox="0 0 16 16"><circle cx="8" cy="5.5" r="2.5" /><path d="M2.67 14a5.33 5.33 0 0110.66 0" /></svg>
                            <span class="text-sm font-bold uppercase tracking-wider text-zinc-900">Informasi Profil</span>
                        </div>
                        <span class="px-2.5 py-1 bg-purple-600/5 border border-purple-600/10 rounded-xl text-[9px] font-bold uppercase tracking-wide text-purple-600">{{ $user->role }}</span>
                    </div>

                    <div class="flex flex-col gap-5">
                        <div class="flex flex-col gap-1.5"><label for="n_kry" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Nama Lengkap</label><input type="text" id="n_kry" name="n_kry" value="{{ old('n_kry', $user->n_kry) }}" class="w-full h-12 px-5 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600/40 transition" /></div>
                        <div class="flex flex-col gap-1.5"><label for="email" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Alamat Email</label><input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full h-12 px-5 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600/40 transition" /></div>
                        <div class="flex flex-col gap-1.5"><label for="jab" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Jabatan</label><input type="text" id="jab" name="jab" value="{{ $user->jab }}" readonly class="w-full h-12 px-5 bg-gray-100 border border-zinc-100 rounded-2xl text-sm text-zinc-500 cursor-not-allowed focus:outline-none transition" /></div>
                        <div class="flex flex-col gap-1.5"><label for="tgl_mulai_kerja" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Tanggal Mulai Kerja</label><input type="date" id="tgl_mulai_kerja" name="tgl_mulai_kerja" value="{{ $user->tgl_mulai_kerja }}" readonly class="w-full h-12 px-5 bg-gray-100 border border-zinc-100 rounded-2xl text-sm text-zinc-500 cursor-not-allowed focus:outline-none transition" /></div>
                        <div class="flex flex-col gap-1.5"><label for="tmpt_lahir" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Tempat Lahir</label><input type="text" id="tmpt_lahir" name="tmpt_lahir" value="{{ old('tmpt_lahir', $user->tmpt_lahir) }}" class="w-full h-12 px-5 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600/40 transition" /></div>
                        <div class="flex flex-col gap-1.5"><label for="tgl_lahir" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Tanggal Lahir</label><input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $user->tgl_lahir) }}" class="w-full h-12 px-5 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600/40 transition" /></div>
                        <div class="flex flex-col gap-1.5"><label for="telp" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Nomor Telepon</label><input type="text" id="telp" name="telp" value="{{ old('telp', $user->telp) }}" class="w-full h-12 px-5 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600/40 transition" /></div>
                        <div class="flex flex-col gap-1.5">
                            <label for="alamat" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">Alamat Rumah</label>
                            <textarea id="alamat" name="alamat" rows="3" class="w-full px-5 py-4 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600/40 transition resize-none">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>
                    </div>
                    <button type="submit" name="aksi" value="informasi" class="w-full h-12 flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 active:scale-[0.98] text-white rounded-2xl shadow-[0_4px_6px_-4px_rgba(147,51,234,0.3),0_10px_15px_-3px_rgba(147,51,234,0.2)] transition-all duration-150"><span class="text-[12px] font-black uppercase tracking-wide">Update Informasi</span></button>
                </div>

                {{-- Keamanan Card --}}
                <div class="bg-white rounded-[2rem] border border-zinc-100 shadow-sm p-8 flex flex-col gap-6">
                    <div class="flex items-center gap-2 opacity-60">
                        <svg class="w-4 h-4 text-zinc-900" fill="none" stroke="currentColor" stroke-width="1.33" viewBox="0 0 16 16"><rect x="2.67" y="7.33" width="10.66" height="7.34" rx="1" /><path d="M5.33 7.33V4.67a2.67 2.67 0 015.34 0v2.66" /><circle cx="8" cy="10.67" r="1" /></svg>
                        <span class="text-sm font-bold uppercase tracking-wider text-zinc-900">Keamanan</span>
                    </div>

                    <div class="flex flex-col gap-4">
                        @foreach (['current_password' => 'Kata Sandi Saat Ini', 'new_password' => 'Kata Sandi Baru', 'confirm_password' => 'Konfirmasi Kata Sandi Baru'] as $id => $label)
                            <div class="flex flex-col gap-1.5">
                                <label for="{{ $id }}" class="text-[10px] font-bold uppercase tracking-wide text-gray-500">{{ $label }}</label>
                                <div class="relative">
                                    <input type="password" id="{{ $id }}" name="{{ $id }}" placeholder="••••••••" class="w-full h-12 pl-5 pr-11 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900/50 placeholder-zinc-400/60 focus:outline-none focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600/40 transition" />
                                    <button type="button" onclick="togglePassword('{{ $id }}', this)" class="absolute right-4 top-1/2 -translate-y-1/2 opacity-40 hover:opacity-70 transition-opacity"><svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.33" viewBox="0 0 16 16"><path d="M1.33 8S3.67 2.67 8 2.67 14.67 8 14.67 8 12.33 13.33 8 13.33 1.33 8 1.33 8z" /><circle cx="8" cy="8" r="2" /></svg></button>
                                </div>
                            </div>
                        @endforeach

                        <button type="submit" name="aksi" value="password" class="w-full h-12 flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 active:scale-[0.98] text-white rounded-2xl shadow-[0_4px_6px_-4px_rgba(147,51,234,0.3),0_10px_15px_-3px_rgba(147,51,234,0.2)] transition-all duration-150"><span class="text-[12px] font-black uppercase tracking-wide">Ubah Kata Sandi</span></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(id, btn) { const input = document.getElementById(id); input.type = input.type === 'password' ? 'text' : 'password'; }
    </script>
@endpush