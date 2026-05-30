@extends('layouts.app')

@section('title', 'Staf - Poly Games Cafe')

@section('content')
<div class="px-6 sm:px-10 lg:px-14 py-8">

    {{-- ---- Page Header ---- --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Direktori Tim</span>
            </div>
            <h1 class="text-zinc-900 text-3xl font-bold font-mono leading-9 mb-1">Manajemen Staf</h1>
            <p class="text-gray-500 text-sm font-normal font-mono leading-6 opacity-80">
                Kelola tim dan akses ringkasan operasional detail untuk setiap staf.
            </p>
        </div>
        <button onclick="openOnboardModal()"
                class="flex-shrink-0 flex items-center gap-2 px-5 h-10 bg-purple-600 rounded-2xl text-white text-xs font-bold font-inter uppercase leading-4 tracking-wider hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20),0px_10px_15px_-3px_rgba(147,51,234,0.20)]">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Tambah Staf
        </button>
    </div>

    {{-- ---- Employee Table Card ---- --}}
    <div class="bg-white rounded-[40px] shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-offset-[-1px] outline-zinc-100 overflow-hidden">

        {{-- Table Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 sm:px-8 py-5 sm:h-24 bg-gray-50/20 border-b border-zinc-100">
            <div>
                <p class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider opacity-70">Daftar Staf</p>
                <p class="text-gray-500 text-[10px] font-medium font-mono uppercase leading-4 mt-0.5">
                    Klik staf untuk melihat ringkasan operasional detail
                </p>
            </div>

            {{-- Role Filter Dropdown --}}
            <div class="relative" id="filter-wrapper">
                <button onclick="toggleFilter()"
                        id="filter-btn"
                        class="flex items-center gap-2.5 w-44 sm:w-52 h-10 sm:h-12 px-4 bg-white rounded-2xl outline outline-1 outline-offset-[-1px] outline-zinc-100 hover:outline-purple-300 transition-all">
                    <i data-lucide="filter" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                    <span id="filter-label" class="flex-1 text-left text-sm font-bold font-inter uppercase leading-5 tracking-wide text-zinc-900">Semua Jabatan</span>
                    <i data-lucide="chevron-down" id="filter-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0"></i>
                </button>

                <div id="filter-dropdown"
                     class="hidden absolute right-0 top-14 w-52 bg-white rounded-2xl shadow-[0px_10px_25px_-5px_rgba(0,0,0,0.10)] outline outline-1 outline-zinc-100 overflow-hidden z-30">
                    <button onclick="filterRole('all','Semua Jabatan')"
                            class="filter-opt w-full px-5 py-3.5 text-left text-[10px] font-black font-inter uppercase leading-4 tracking-wide bg-purple-600 text-white hover:bg-purple-700 transition-colors">
                        Semua Jabatan
                    </button>
                    <button onclick="filterRole('owner','Owner')"
                            class="filter-opt w-full px-5 py-3.5 text-left text-[10px] font-black font-inter uppercase leading-4 tracking-wide text-zinc-900 hover:bg-zinc-50 transition-colors">
                        Owner
                    </button>
                    <button onclick="filterRole('barista','Barista')"
                            class="filter-opt w-full px-5 py-3.5 text-left text-[10px] font-black font-inter uppercase leading-4 tracking-wide text-zinc-900 hover:bg-zinc-50 transition-colors">
                        Barista
                    </button>
                    <button onclick="filterRole('game master','Game Master')"
                            class="filter-opt w-full px-5 py-3.5 text-left text-[10px] font-black font-inter uppercase leading-4 tracking-wide text-zinc-900 hover:bg-zinc-50 transition-colors">
                        Game Master
                    </button>
                </div>
            </div>
        </div>

        {{-- ===================== MOBILE CARD LIST (< md) ===================== --}}
        <div class="lg:hidden divide-y divide-zinc-100" id="employee-card-list">

            @foreach($karyawans as $k)
                <div class="emp-card px-5 py-5 hover:bg-zinc-50/60 transition-colors cursor-pointer" 
                    data-role="{{ strtolower($k->jab) }}" 
                    onclick="openDetailPanelById('{{ $k->id_kry }}')">
                    
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-2xl outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-zinc-900 text-xs font-bold font-inter">
                                    {{ strtoupper(substr($k->n_kry, 0, 1)) }}
                                </span>
                            </div>
                            
                            <div class="min-w-0">
                                <p class="text-zinc-900 text-sm font-bold font-inter leading-5 truncate">{{ $k->n_kry }}</p>
                                <p class="text-gray-400 text-xs font-inter leading-4 truncate">{{ $k->email }}</p>
                            </div>
                        </div>

                        @php
                            $badgeColor = 'bg-amber-50 text-amber-600 outline-amber-100'; // Default Barista
                            if(strtolower($k->jab) == 'owner') $badgeColor = 'bg-purple-600/5 text-purple-600 outline-purple-600/10';
                            if(strtolower($k->jab) == 'game master') $badgeColor = 'bg-emerald-50 text-emerald-600 outline-emerald-100';
                        @endphp
                        
                        <span class="inline-flex items-center px-2.5 py-1 {{ $badgeColor }} rounded-full text-[9px] font-bold font-inter uppercase leading-3 tracking-wide outline outline-1 flex-shrink-0">
                            {{ $k->jab }}
                        </span>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1" style="padding-left:52px">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="phone" class="w-3 h-3 text-purple-500 opacity-60"></i>
                            <span class="text-zinc-700 text-xs font-bold font-inter uppercase">{{ $k->telp ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="w-3 h-3 text-gray-400"></i>
                            <span class="text-gray-500 text-xs font-inter truncate max-w-[150px]">{{ $k->alamat ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3 h-3 text-gray-400"></i>
                            <span class="text-gray-500 text-xs font-inter">{{ $k->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <div class="mt-3 flex justify-end gap-1">
                        <button onclick="event.stopPropagation(); openEditEmpModalById('{{ $k->id_kry }}')" 
                                class="flex items-center gap-1.5 px-3 h-8 rounded-xl bg-purple-600/5 hover:bg-purple-600/10 transition-colors">
                            <i data-lucide="pencil" class="w-3.5 h-3.5 text-purple-600"></i>
                            <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4">Edit</span>
                        </button>
                        <button onclick="event.stopPropagation(); confirmDeleteEmp('{{ $k->id_kry }}', '{{ $k->n_kry }}')" 
                                class="flex items-center gap-1.5 px-3 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 transition-colors">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-500"></i>
                            <span class="text-rose-500 text-[10px] font-bold font-inter uppercase leading-4">Hapus</span>
                        </button>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- ===================== DESKTOP TABLE (>= md) ===================== --}}
        <div class="hidden lg:block w-full overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead>
                    <tr class="bg-gray-50/10 border-b border-zinc-100">
                        <th class="px-8 py-5 text-left text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Staf & ID</th>
                        <th class="px-8 py-5 text-left text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide w-48">Jabatan</th>
                        <th class="px-8 py-5 text-left text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Kontak & Alamat</th>
                        <th class="px-8 py-5 text-left text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide w-36">Tanggal Bergabung</th>
                        <th class="px-8 py-5 text-right text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="employee-table-body">
                    @foreach($karyawans as $k)
                        @php
                            // Logika warna badge berdasarkan jabatan
                            $jabatan = strtolower($k->jab);
                            $badgeClass = 'bg-amber-50 text-amber-600 outline-amber-100'; // Default Barista
                            
                            if($jabatan == 'owner') {
                                $badgeClass = 'bg-purple-600/5 text-purple-600 outline-purple-600/10';
                            } elseif($jabatan == 'game master') {
                                $badgeClass = 'bg-emerald-50 text-emerald-600 outline-emerald-100';
                            }
                            
                            // Ambil inisial nama
                            $inisial = strtoupper(substr($k->n_kry, 0, 1));
                        @endphp

                        <tr class="emp-row border-b border-zinc-100/50 cursor-pointer group hover:bg-zinc-50/60 transition-colors" 
                            data-role="{{ $jabatan }}" 
                            onclick="openDetailPanelById('{{ $k->id_kry }}')">
                            
                            {{-- Nama & Email --}}
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-11 h-11 bg-gray-100 rounded-2xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-zinc-900 text-xs font-bold font-inter leading-4">{{ $inisial }}</span>
                                    </div>
                                    <div>
                                        <p class="text-zinc-900 text-base font-bold font-inter leading-5">{{ $k->n_kry }}</p>
                                        <p class="text-gray-500 text-xs font-medium font-inter leading-4 opacity-60">{{ $k->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Jabatan --}}
                            <td class="px-8 py-6">
                                <span class="inline-flex items-center px-3 py-1 {{ $badgeClass }} rounded-full text-[9px] font-bold font-inter uppercase leading-3 tracking-wide shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-offset-[-1px]">
                                    {{ $k->jab }}
                                </span>
                            </td>

                            {{-- Kontak & Alamat --}}
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="phone" class="w-3.5 h-3.5 text-purple-600 opacity-60 flex-shrink-0"></i>
                                        <span class="text-zinc-900/80 text-xs font-bold font-inter uppercase leading-4">{{ $k->telp ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400 opacity-40 flex-shrink-0"></i>
                                        <span class="text-gray-500 text-xs font-medium font-inter leading-4 truncate max-w-[200px]">{{ $k->alamat ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Tanggal Bergabung --}}
                            <td class="px-8 py-6">
                                <span class="text-gray-500 text-xs font-bold font-inter uppercase leading-4 opacity-70">
                                    {{ $k->tgl_mulai_kerja }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                    <button onclick="event.stopPropagation(); openEditEmpModalById('{{ $k->id_kry }}')" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-purple-600/10 transition-colors">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5 text-gray-400"></i>
                                    </button>

                                    <button onclick="event.stopPropagation(); confirmDeleteEmp('{{ $k->id_kry }}', '{{ $k->n_kry }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-rose-50 transition-colors group">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-gray-400 group-hover:text-rose-500"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>{{-- end card --}}
</div>
@endsection


{{-- ===================== MODALS ===================== --}}
@push('modals')

{{-- ONBOARD MEMBER MODAL --}}
<div id="onboard-backdrop" data-modal-backdrop data-modal-panel="onboard-panel"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closePanel(this, document.getElementById('onboard-panel'))">
    <div id="onboard-panel" class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl px-7 pt-7 pb-7 flex flex-col gap-5 opacity-0 scale-95 transition-all duration-200 max-h-[90vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-1.5 mb-2">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5 text-purple-600"></i>
                    <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Tindakan Administratif</span>
                </div>
                <h2 class="text-zinc-900 text-2xl font-bold font-mono leading-7 mb-1">Tambah Staf Baru</h2>
                <p class="text-gray-400 text-xs font-normal font-mono leading-5">Buat akun staf baru dan atur kredensial akses awal.</p>
            </div>
            <button onclick="closePanel(document.getElementById('onboard-backdrop'), document.getElementById('onboard-panel'))"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 ml-4 mt-1">
                <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>

        <form method="POST" action="/owner/management-karyawan" class="flex flex-col gap-3.5">
            @csrf

            {{-- Full Name --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Nama Lengkap</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="text" name="n_kry" placeholder="cth. Budi Santoso"
                           class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required />
                </div>
            </div>

            {{-- Role + Phone (2 col) --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Jabatan</label>
                    <div class="relative">
                        <i data-lucide="shield" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <select name="jab"
                                class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                            <option value="">Pilih...</option>
                            <option value="owner">Owner</option>
                            <option value="barista">Barista</option>
                            <option value="game master">Game Master</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Nomor Telepon</label>
                    <div class="relative">
                        <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="text" name="telp" placeholder="08xxxxxxxxxx"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required />
                    </div>
                </div>
            </div>

            {{-- Place of Birth + Date of Birth (2 col) --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Tempat Lahir</label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="text" name="tmpt_lahir" placeholder="cth. Jakarta"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all"/>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Tanggal Lahir</label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="date" name="tgl_lahir"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter focus:outline-2 focus:outline-purple-400 transition-all" />
                    </div>
                </div>
            </div>

            {{-- Starting Work From --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Mulai Kerja Dari</label>
                <div class="relative">
                    <i data-lucide="briefcase" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="date" name="tgl_mulai_kerja"
                           class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter focus:outline-2 focus:outline-purple-400 transition-all" />
                </div>
            </div>

            {{-- --- BAGIAN TAMBAHAN: ROLE SELECTION --- --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Role Sistem</label>
                <div class="relative">
                    <i data-lucide="lock-keyhole" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <select name="role" required
                            class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                        <option value="staff" selected>Staff</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
            </div>
            {{-- --------------------------------------- --}}

            {{-- Email --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Email / Nama Pengguna</label>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="email" name="email" placeholder="email@company.com"
                           class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required />
                </div>
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Kata Sandi</label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="password" name="password" placeholder="••••••••" id="onboard-pass"
                           class="w-full h-12 pl-11 pr-11 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all"/>
                    <button type="button" onclick="togglePasswordField('onboard-pass', this)"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full h-14 mt-1 bg-purple-600 rounded-2xl flex items-center justify-center gap-2 text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.35)]">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Simpan Staf
            </button>
        </form>
    </div>
</div>

{{-- EDIT EMPLOYEE MODAL --}}
<div id="edit-emp-backdrop" data-modal-backdrop data-modal-panel="edit-emp-panel"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closePanel(this, document.getElementById('edit-emp-panel'))">
    <div id="edit-emp-panel" class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl px-7 pt-7 pb-7 flex flex-col gap-5 opacity-0 scale-95 transition-all duration-200 max-h-[90vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-1.5 mb-2">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5 text-purple-600"></i>
                    <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Tindakan Administratif</span>
                </div>
                <h2 class="text-zinc-900 text-2xl font-bold font-mono leading-7 mb-1">Edit Profil Staf</h2>
                <p class="text-gray-400 text-xs font-normal font-mono leading-5">Perbarui detail pekerjaan dan informasi akses.</p>
            </div>
            <button onclick="closePanel(document.getElementById('edit-emp-backdrop'), document.getElementById('edit-emp-panel'))"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 ml-4 mt-1">
                <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>

        <form method="POST" id="edit-emp-form" action="" class="flex flex-col gap-3.5">
            @csrf @method('PUT')

            {{-- Full Name --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Nama Lengkap</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="text" id="edit-n_kry" name="n_kry"
                           class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required />
                </div>
            </div>

            {{-- Jabatan + Phone (2 col) --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Jabatan</label>
                    <div class="relative">
                        <i data-lucide="shield" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <select id="edit-jab" name="jab"
                                class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                            <option value="">Pilih...</option>
                            <option value="Owner">Owner</option>
                            <option value="Barista">Barista</option>
                            <option value="Game Master">Game Master</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Nomor Telepon</label>
                    <div class="relative">
                        <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="text" id="edit-telp" name="telp"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required />
                    </div>
                </div>
            </div>

            {{-- Place of Birth + Date of Birth (2 col) --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Tempat Lahir</label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="text" id="edit-tmpt_lahir" name="tmpt_lahir"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required/>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Tanggal Lahir</label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="date" id="edit-tgl_lahir" name="tgl_lahir"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter focus:outline-2 focus:outline-purple-400 transition-all"/>
                    </div>
                </div>
            </div>

            {{-- Starting Work From --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Mulai Kerja Dari</label>
                <div class="relative">
                    <i data-lucide="briefcase" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="date" id="edit-tgl_mulai_kerja" name="tgl_mulai_kerja"
                           class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter focus:outline-2 focus:outline-purple-400 transition-all"/>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Role Sistem</label>
                <div class="relative">
                    <i data-lucide="lock-keyhole" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <select id="edit-role" name="role" required
                            class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                        <option value="staff" selected>Staff</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
            </div>

            {{-- Email --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Email / Nama Pengguna</label>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    {{-- Ganti bagian email di modal kamu --}}
                    <input type="email" id="edit-email" name="email"
                        class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required/>
                </div>
            </div>

            {{-- Password saat ini --}}
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Password Saat Ini</label>
                <div class="relative">
                    <i data-lucide="shield-check" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="text" value="Tersimpan aman sebagai hash" disabled
                           class="w-full h-12 pl-11 pr-4 bg-gray-50 rounded-2xl outline outline-1 outline-zinc-200 text-gray-400 text-sm font-inter cursor-not-allowed" />
                </div>
                <p class="text-gray-400 text-[10px] font-inter leading-4">
                    Password lama tidak dapat ditampilkan. Isi password baru di bawah jika ingin mengganti akses staf.
                </p>
            </div>

            {{-- Password baru --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Password Baru</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="password" id="edit-password" name="password" placeholder="Kosongkan jika tetap"
                               class="w-full h-12 pl-11 pr-11 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" />
                        <button type="button" onclick="togglePasswordField('edit-password', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Konfirmasi</label>
                    <div class="relative">
                        <i data-lucide="lock-keyhole" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="password" id="edit-password_confirmation" name="password_confirmation" placeholder="Ulangi password"
                               class="w-full h-12 pl-11 pr-11 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" />
                        <button type="button" onclick="togglePasswordField('edit-password_confirmation', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full h-14 mt-1 bg-purple-600 rounded-2xl flex items-center justify-center gap-2 text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.35)]">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Perbarui Profil
            </button>
        </form>
    </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div id="del-emp-backdrop" data-modal-backdrop data-modal-panel="del-emp-panel"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closePanel(this, document.getElementById('del-emp-panel'))">
    <div id="del-emp-panel" class="relative w-full max-w-sm bg-white rounded-[32px] shadow-2xl px-8 pt-8 pb-8 flex flex-col items-center gap-5 text-center opacity-0 scale-95 transition-all duration-200">
        <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center shadow-[0px_8px_30px_rgba(244,63,94,0.12)]">
            <i data-lucide="trash-2" class="w-6 h-6 text-rose-500"></i>
        </div>
        <div>
            <div class="flex items-center justify-center gap-1.5 mb-2">
                <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-rose-500"></i>
                <span class="text-rose-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Konfirmasi Penghapusan</span>
            </div>
            <h3 class="text-zinc-900 text-lg font-bold font-mono mb-1">Hapus Staf</h3>
            <p class="text-gray-500 text-sm font-inter leading-5">
                Anda akan menghapus <span id="del-emp-name" class="font-bold text-zinc-900"></span> dari tim. Data ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="flex gap-3 w-full">
            <button onclick="closePanel(document.getElementById('del-emp-backdrop'), document.getElementById('del-emp-panel'))"
                    class="flex-1 h-11 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-zinc-50 transition-colors">
                Batal
            </button>
            <form id="del-emp-form" method="POST" action="" class="flex-1">
                @csrf 
                @method('DELETE')
                <button type="submit"
                        class="w-full h-11 bg-rose-500 rounded-2xl text-white text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-rose-600 active:scale-[0.98] transition-all">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- NOTIFICATION MODAL --}}
<div id="notify-backdrop" data-modal-backdrop data-modal-panel="notify-panel"
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closePanel(this, document.getElementById('notify-panel'))">
    <div id="notify-panel" class="relative w-full max-w-sm bg-white rounded-[32px] shadow-2xl px-8 pt-8 pb-8 flex flex-col items-center gap-5 text-center opacity-0 scale-95 transition-all duration-200">
        <div id="notify-icon-wrap" class="w-14 h-14 bg-purple-600/10 rounded-2xl flex items-center justify-center shadow-[0px_8px_30px_rgba(147,51,234,0.12)]">
            <i id="notify-icon" data-lucide="check-circle" class="w-6 h-6 text-purple-600"></i>
        </div>
        <div>
            <div class="flex items-center justify-center gap-1.5 mb-2">
                <i id="notify-kicker-icon" data-lucide="bell" class="w-3.5 h-3.5 text-purple-600"></i>
                <span id="notify-kicker" class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Pemberitahuan Sistem</span>
            </div>
            <h3 id="notify-title" class="text-zinc-900 text-lg font-bold font-mono mb-1">Berhasil</h3>
            <p id="notify-message" class="text-gray-500 text-sm font-inter leading-5">Perubahan berhasil disimpan.</p>
        </div>
        <button type="button" onclick="closePanel(document.getElementById('notify-backdrop'), document.getElementById('notify-panel'))"
                class="w-full h-11 bg-purple-600 rounded-2xl text-white text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.25)]">
            Mengerti
        </button>
    </div>
</div>

{{-- ===================== STAFF DETAIL SLIDE PANEL ===================== --}}

<div id="detail-overlay"
     class="fixed inset-0 z-40 bg-black/30 backdrop-blur-[2px] hidden transition-opacity duration-300 opacity-0"
     onclick="closeDetailPanel()"></div>

{{-- Slide Panel --}}
<div id="detail-panel"
     class="fixed top-0 right-0 z-50 h-full w-full sm:w-[420px] bg-white shadow-[-8px_0_40px_rgba(0,0,0,0.08)] flex flex-col translate-x-full transition-transform duration-300 ease-out overflow-hidden">

    {{-- Header --}}
    <div class="flex items-start gap-4 px-7 pt-7 pb-6 border-b border-zinc-100 flex-shrink-0">
        <div id="dp-avatar" class="w-14 h-14 bg-purple-600/10 rounded-2xl flex items-center justify-center flex-shrink-0">
            <span id="dp-initial" class="text-purple-600 text-xl font-bold font-inter">K</span>
        </div>
        <div class="flex-1 min-w-0">
            <p id="dp-name" class="text-zinc-900 text-xl font-bold font-mono leading-6 truncate">Nama Staf</p>
            <p id="dp-role" class="text-purple-600 text-[10px] font-black font-inter uppercase leading-3 tracking-widest mt-1.5">Owner</p>
        </div>
        <button onclick="closeDetailPanel()"
                class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 -mt-0.5">
            <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
        </button>
    </div>

    {{-- Scrollable Body --}}
    <div class="flex-1 overflow-y-auto">

        {{-- Personal Information --}}
        <div class="px-7 pt-6 pb-5">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="user" class="w-3.5 h-3.5 text-purple-500"></i>
                <span class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-widest">Informasi Personal</span>
            </div>
            <div class="bg-white rounded-2xl outline outline-1 outline-zinc-100 p-5 flex flex-col gap-5">

                {{-- Row 1: Place of Birth + Date of Birth --}}
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <p class="text-zinc-900 text-[9px] font-bold font-inter uppercase leading-3 tracking-widest mb-2">Tempat Lahir</p>
                        <div class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
                            <p id="dp-pob" class="text-zinc-900 text-sm font-bold font-inter leading-5">Tempat lahir</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-zinc-900 text-[9px] font-bold font-inter uppercase leading-3 tracking-widest mb-2">Tanggal Lahir</p>
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
                            <p id="dp-dob" class="text-zinc-900 text-sm font-bold font-inter leading-5">1990-05-12</p>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-zinc-100"></div>

                {{-- Starting Work From --}}
                <div>
                    <p class="text-zinc-900 text-[9px] font-bold font-inter uppercase leading-3 tracking-widest mb-2">Mulai Kerja Dari</p>
                    <div class="flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
                        <p id="dp-joindate" class="text-zinc-900 text-sm font-bold font-inter leading-5">2024-01-15</p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Operational Stats --}}
        <div class="px-7 py-5">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="activity" class="w-3.5 h-3.5 text-purple-500"></i>
                <span class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-widest">Statistik Operasional</span>
            </div>
            <div class="grid grid-cols-2 gap-3">

                {{-- Rating --}}
                <div class="bg-white rounded-2xl outline outline-1 outline-zinc-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <i data-lucide="star" class="w-4 h-4 text-amber-400" style="fill:#fbbf24"></i>
                        <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-widest">Evaluasi</span>
                    </div>
                    <p id="dp-rating" class="text-zinc-900 text-2xl font-bold font-mono leading-7 mb-1">-</p>
                    <p id="dp-rating-note" class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide">Belum Ada Data</p>
                </div>

                {{-- Kasbon --}}
                <div class="bg-white rounded-2xl outline outline-1 outline-zinc-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <i data-lucide="dollar-sign" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-widest">Kasbon</span>
                    </div>
                    <p id="dp-kasbon" class="text-zinc-900 text-2xl font-bold font-mono leading-7 mb-1">Rp 0</p>
                    <p id="dp-kasbon-note" class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide">Pinjaman Aktif</p>
                </div>

                {{-- Overtime --}}
                <div class="bg-white rounded-2xl outline outline-1 outline-zinc-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <i data-lucide="clock" class="w-4 h-4 text-purple-400"></i>
                        <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-widest">Lembur</span>
                    </div>
                    <p class="text-zinc-900 text-2xl font-bold font-mono leading-7 mb-1"><span id="dp-overtime">0</span><span class="text-base font-medium text-gray-400 ml-0.5">j</span></p>
                    <p id="dp-overtime-note" class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide">Total Periode Ini</p>
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-2xl outline outline-1 outline-zinc-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <i data-lucide="trending-up" class="w-4 h-4 text-emerald-500"></i>
                        <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-widest">Status</span>
                    </div>
                    <p id="dp-status" class="text-emerald-500 text-2xl font-bold font-mono leading-7 mb-1">AKTIF</p>
                    <p id="dp-status-note" class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide">Kepegawaian</p>
                </div>

            </div>
        </div>

        {{-- Recent Actions --}}
        <div class="px-7 py-5">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="history" class="w-3.5 h-3.5 text-purple-500"></i>
                <span class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-widest">Aktivitas Terbaru</span>
            </div>
            <div id="dp-recent-actions" class="bg-white rounded-2xl outline outline-1 outline-zinc-100 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 hover:bg-zinc-50/60 transition-colors cursor-pointer">
                    <div>
                        <p class="text-zinc-900 text-sm font-bold font-inter leading-5">Belum ada aktivitas terbaru</p>
                        <p class="text-gray-400 text-xs font-inter leading-4 mt-0.5">—</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 flex-shrink-0"></i>
                </div>
            </div>
        </div>

        {{-- Bottom padding --}}
        <div class="h-4"></div>
    </div>

    {{-- Footer --}}
    <div class="px-7 py-5 border-t border-zinc-100 flex-shrink-0">
        <button onclick="closeDetailPanel()"
                class="w-full h-14 bg-purple-600 rounded-2xl flex items-center justify-center gap-2 text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.25)]">
            Tutup Ringkasan
        </button>
    </div>

</div>

@endpush

@push('scripts')
<script>
    const employeeDetails = @json($employeeDetails ?? []);

    // ---- Modal helpers ----
    function openPanel(backdrop, panel) {
        if (!backdrop || !panel) return;
        backdrop.classList.remove('hidden', 'opacity-0');
        requestAnimationFrame(() => {
            backdrop.classList.add('opacity-100');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        });
        document.body.style.overflow = 'hidden';
        if (window.lucide) lucide.createIcons();
    }

    function closePanel(backdrop, panel) {
        if (!backdrop || !panel) return;
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            backdrop.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    // ---- Filter dropdown ----
    const filterBtn      = document.getElementById('filter-btn');
    const filterDropdown = document.getElementById('filter-dropdown');
    const filterChevron  = document.getElementById('filter-chevron');
    let filterOpen = false;

    function toggleFilter() {
        filterOpen = !filterOpen;
        if (filterOpen) {
            filterDropdown.classList.remove('hidden');
            filterChevron.style.transform = 'rotate(180deg)';
            filterBtn.classList.add('outline-purple-400');
        } else {
            filterDropdown.classList.add('hidden');
            filterChevron.style.transform = '';
            filterBtn.classList.remove('outline-purple-400');
        }
    }

    function filterRole(role, label) {
        document.getElementById('filter-label').textContent = label;

        // Update active option highlight
        document.querySelectorAll('.filter-opt').forEach(btn => {
            const isActive = btn.textContent.trim().toLowerCase() === label.toLowerCase();
            btn.classList.toggle('bg-purple-600', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('hover:bg-purple-700', isActive);
            btn.classList.toggle('text-zinc-900', !isActive);
            btn.classList.toggle('hover:bg-zinc-50', !isActive);
        });

        // Filter rows (desktop table)
        document.querySelectorAll('.emp-row').forEach(row => {
            row.style.display = (role === 'all' || row.dataset.role === role) ? '' : 'none';
        });
        // Filter cards (mobile)
        document.querySelectorAll('.emp-card').forEach(card => {
            card.style.display = (role === 'all' || card.dataset.role === role) ? '' : 'none';
        });

        // Close dropdown
        filterDropdown.classList.add('hidden');
        filterChevron.style.transform = '';
        filterBtn.classList.remove('outline-purple-400');
        filterOpen = false;
    }

    // Close filter when clicking outside
    document.addEventListener('click', e => {
        const wrapper = document.getElementById('filter-wrapper');
        if (filterOpen && wrapper && !wrapper.contains(e.target)) {
            filterDropdown.classList.add('hidden');
            filterChevron.style.transform = '';
            filterBtn.classList.remove('outline-purple-400');
            filterOpen = false;
        }
    });

    // ---- Onboard modal ----
    function openOnboardModal() {
        openPanel(document.getElementById('onboard-backdrop'), document.getElementById('onboard-panel'));
    }

    // ---- Edit modal ----
    function openEditEmpModalById(id) {
        const data = employeeDetails[id] || {};
        openEditEmpModal(
            data.id || id,
            data.name || '',
            data.role || '',
            data.phone || '',
            data.pob || '',
            data.dob || '',
            data.join_date || '',
            data.system_role || 'staff',
            data.email || ''
        );
    }

    function openEditEmpModal(id, n, jab, telp, tmpt, tgl, mulai, role, email) {
        const form = document.getElementById('edit-emp-form');
        // Sesuaikan prefix URL dengan route Laravel kamu
        form.action = `/owner/management-karyawan/${id}`;

        // Isi Field (Handle Null agar tidak muncul tulisan "null" di input)
        document.getElementById('edit-n_kry').value = n || '';
        document.getElementById('edit-telp').value = telp || '';
        document.getElementById('edit-tmpt_lahir').value = tmpt || '';
        document.getElementById('edit-tgl_lahir').value = tgl || '';
        document.getElementById('edit-tgl_mulai_kerja').value = mulai || '';
        document.getElementById('edit-email').value = email || '';
        document.getElementById('edit-password').value = '';
        document.getElementById('edit-password_confirmation').value = '';
        document.getElementById('edit-password').type = 'password';
        document.getElementById('edit-password_confirmation').type = 'password';
        document.querySelectorAll('#edit-emp-form button[onclick^="togglePasswordField"] i').forEach(icon => icon.setAttribute('data-lucide', 'eye'));
        
        // Sinkronisasi Select Role
        document.getElementById('edit-role').value = role || 'staff';

        // Sinkronisasi Select Jabatan (Case Sensitive Handling)
        const selectJab = document.getElementById('edit-jab');
        const valJab = jab || '';
        selectJab.selectedIndex = 0; // Default ke "Pilih..."
        
        for (let i = 0; i < selectJab.options.length; i++) {
            // Kita bandingkan dengan lowercase agar lebih aman
            if (selectJab.options[i].value.toLowerCase() === valJab.toLowerCase()) {
                selectJab.selectedIndex = i;
                break;
            }
        }

        // Trigger Animasi Buka Modal (Fungsi bawaan template kamu)
        openPanel(document.getElementById('edit-emp-backdrop'), document.getElementById('edit-emp-panel'));
    }

    // ---- Detail slide panel ----
    function safeText(value, fallback = '—') {
        return (value === null || value === undefined || value === '') ? fallback : value;
    }

    function escapeHtml(value) {
        return String(safeText(value, ''))
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function openDetailPanelById(id) {
        openDetailPanel(employeeDetails[id] || {});
    }

    function renderRecentActions(actions) {
        const container = document.getElementById('dp-recent-actions');
        const list = Array.isArray(actions) ? actions.slice(0, 3) : [];

        if (!list.length) {
            container.innerHTML = `
                <div class="flex items-center justify-between px-5 py-4 hover:bg-zinc-50/60 transition-colors cursor-pointer">
                    <div>
                        <p class="text-zinc-900 text-sm font-bold font-inter leading-5">Belum ada aktivitas terbaru</p>
                        <p class="text-gray-400 text-xs font-inter leading-4 mt-0.5">—</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 flex-shrink-0"></i>
                </div>`;
            return;
        }

        container.innerHTML = list.map((action, index) => {
            const border = index < list.length - 1 ? ' border-b border-zinc-100' : '';
            return `
                <div class="flex items-center justify-between px-5 py-4${border} hover:bg-zinc-50/60 transition-colors cursor-pointer">
                    <div>
                        <p class="text-zinc-900 text-sm font-bold font-inter leading-5">${escapeHtml(action.title)}</p>
                        <p class="text-gray-400 text-xs font-inter leading-4 mt-0.5">${escapeHtml(action.date)}</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 flex-shrink-0"></i>
                </div>`;
        }).join('');
    }

    function openDetailPanel(data) {
        const role = safeText(data.role, '-');

        // Populate personal information
        document.getElementById('dp-initial').textContent       = safeText(data.initial, 'K');
        document.getElementById('dp-name').textContent          = safeText(data.name, 'Nama Staf');
        document.getElementById('dp-pob').textContent           = safeText(data.pob);
        document.getElementById('dp-dob').textContent           = safeText(data.dob);
        document.getElementById('dp-joindate').textContent      = safeText(data.join_date);

        // Populate operational stats
        document.getElementById('dp-rating').textContent        = safeText(data.rating, '-');
        document.getElementById('dp-rating-note').textContent   = safeText(data.rating_note, 'Belum Ada Data');
        document.getElementById('dp-kasbon').textContent        = safeText(data.kasbon, 'Rp 0');
        document.getElementById('dp-kasbon-note').textContent   = safeText(data.kasbon_note, 'Pinjaman Aktif');
        document.getElementById('dp-overtime').textContent      = safeText(data.overtime, '0');
        document.getElementById('dp-overtime-note').textContent = safeText(data.overtime_note, 'Total Periode Ini');
        document.getElementById('dp-status').textContent        = safeText(data.status, 'AKTIF');
        document.getElementById('dp-status-note').textContent   = safeText(data.status_note, 'Kepegawaian');
        renderRecentActions(data.recent_actions);

        // Role badge color
        const roleEl = document.getElementById('dp-role');
        roleEl.textContent = role;
        if (role.toLowerCase() === 'owner')            roleEl.className = 'text-purple-600 text-[10px] font-black font-inter uppercase leading-3 tracking-widest mt-1.5';
        else if (role.toLowerCase() === 'game master') roleEl.className = 'text-emerald-600 text-[10px] font-black font-inter uppercase leading-3 tracking-widest mt-1.5';
        else if (role.toLowerCase() === 'barista')     roleEl.className = 'text-amber-500 text-[10px] font-black font-inter uppercase leading-3 tracking-widest mt-1.5';
        else                                           roleEl.className = 'text-gray-500 text-[10px] font-black font-inter uppercase leading-3 tracking-widest mt-1.5';

        const statusEl = document.getElementById('dp-status');
        const statusText = statusEl.textContent.toLowerCase();
        if (statusText.includes('non') || statusText.includes('tidak')) {
            statusEl.className = 'text-rose-500 text-2xl font-bold font-mono leading-7 mb-1';
        } else {
            statusEl.className = 'text-emerald-500 text-2xl font-bold font-mono leading-7 mb-1';
        }

        // Show overlay
        const overlay = document.getElementById('detail-overlay');
        overlay.classList.remove('hidden');
        requestAnimationFrame(() => requestAnimationFrame(() => overlay.classList.replace('opacity-0','opacity-100')));

        // Slide panel in
        const panel = document.getElementById('detail-panel');
        panel.classList.replace('translate-x-full','translate-x-0');

        // Lock body scroll
        document.body.style.overflow = 'hidden';
        lucide.createIcons();
    }

    function closeDetailPanel() {
        const panel   = document.getElementById('detail-panel');
        const overlay = document.getElementById('detail-overlay');
        panel.classList.replace('translate-x-0','translate-x-full');
        overlay.classList.replace('opacity-100','opacity-0');
        setTimeout(() => {
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    // ESC closes detail panel too
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetailPanel(); });

    // ---- Toggle password visibility ----
    function togglePasswordField(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
    // ---- Delete modal ----
    function confirmDeleteEmp(id, name) {
        const backdrop = document.getElementById('del-emp-backdrop');
        const panel = document.getElementById('del-emp-panel');
        const form = document.getElementById('del-emp-form');
        const nameSpan = document.getElementById('del-emp-name');

        nameSpan.innerText = name || 'staf ini';
        form.action = `/owner/management-karyawan/${id}`;

        openPanel(backdrop, panel);
    }

    // ---- Notification modal ----
    function openNotifyModal(type, title, message) {
        const isError = type === 'error';
        const backdrop = document.getElementById('notify-backdrop');
        const panel = document.getElementById('notify-panel');
        const iconWrap = document.getElementById('notify-icon-wrap');
        const icon = document.getElementById('notify-icon');
        const kickerIcon = document.getElementById('notify-kicker-icon');
        const kicker = document.getElementById('notify-kicker');
        const button = panel.querySelector('button');

        document.getElementById('notify-title').innerText = title || (isError ? 'Gagal' : 'Berhasil');
        document.getElementById('notify-message').innerText = message || (isError ? 'Data belum bisa disimpan.' : 'Perubahan berhasil disimpan.');

        icon.setAttribute('data-lucide', isError ? 'alert-circle' : 'check-circle');
        kickerIcon.setAttribute('data-lucide', isError ? 'alert-triangle' : 'bell');
        kicker.innerText = isError ? 'Periksa Kembali' : 'Pemberitahuan Sistem';

        iconWrap.className = isError
            ? 'w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center shadow-[0px_8px_30px_rgba(244,63,94,0.12)]'
            : 'w-14 h-14 bg-purple-600/10 rounded-2xl flex items-center justify-center shadow-[0px_8px_30px_rgba(147,51,234,0.12)]';
        icon.className = isError ? 'w-6 h-6 text-rose-500' : 'w-6 h-6 text-purple-600';
        kickerIcon.className = isError ? 'w-3.5 h-3.5 text-rose-500' : 'w-3.5 h-3.5 text-purple-600';
        kicker.className = isError
            ? 'text-rose-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wider'
            : 'text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider';
        button.className = isError
            ? 'w-full h-11 bg-rose-500 rounded-2xl text-white text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-rose-600 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(244,63,94,0.20)]'
            : 'w-full h-11 bg-purple-600 rounded-2xl text-white text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.25)]';

        openPanel(backdrop, panel);
    }

    document.addEventListener('DOMContentLoaded', () => {
        @if(session('success'))
            openNotifyModal('success', 'Berhasil', @json(session('success')));
        @endif

        @if($errors->any())
            openNotifyModal('error', 'Data belum bisa disimpan', @json($errors->first()));
        @endif
    });
</script>
@endpush