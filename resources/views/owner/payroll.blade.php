@extends('layouts.app')

@section('title', 'Penggajian – Poly Games Cafe')

@section('content')
<div class="px-6 sm:px-10 lg:px-14 py-8 space-y-6">

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 outline outline-1 outline-emerald-200 p-4 rounded-2xl text-sm font-bold font-inter flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
    <div class="bg-rose-50 text-rose-700 outline outline-1 outline-rose-200 p-4 rounded-2xl text-sm font-bold font-inter space-y-1">
        <p class="font-black">Terjadi Kendala Pengiriman:</p>
        <ul class="list-disc pl-5 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    {{-- Setup Periode Aktif Secara Dinamis Berdasarkan Bulan Saat Ini --}}
    @php
        $currentPeriod = now()->format('Y-m'); // Format pencarian database: "2026-05"
        
        // Generate daftar 12 bulan ke belakang secara dinamis untuk dropdown modal
        $periodOptions = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $periodOptions[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }
    @endphp

    {{-- ── Page Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-2">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <i data-lucide="dollar-sign" class="w-3.5 h-3.5 text-purple-600"></i>
                <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Pengawasan Keuangan</span>
            </div>
            <h1 class="text-zinc-900 text-3xl font-bold font-mono leading-9">Manajemen Slip Gaji</h1>
            <p class="text-gray-500 text-sm font-mono leading-6 opacity-80 max-w-xl">
                Pantau siklus pembayaran dan unggah slip gaji digital secara aman untuk setiap anggota tim.
            </p>
        </div>

        {{-- Badge Status Siklus Dinamis --}}
        <div class="flex items-center gap-4 px-5 py-4 bg-purple-600/5 rounded-[28px] outline outline-1 outline-purple-600/10 self-start sm:self-auto flex-shrink-0">
            <div class="w-9 h-9 bg-purple-600 rounded-2xl shadow-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar" class="w-4 h-4 text-white"></i>
            </div>
            <div>
                <p class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wide opacity-60">Status Siklus</p>
                <p class="text-zinc-900 text-sm font-black font-inter uppercase leading-5">
                    {{ now()->translatedFormat('F Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Hitung Status Otomatis Berdasarkan Bulan Berjalan --}}
    @php
        $paidCount = 0;
        $pendingCount = 0;

        foreach($employees as $emp) {
            // Cek apakah karyawan ini memiliki slip yang cocok dengan id_kry DAN periode aktif saat ini
            $hasSlip = $slips->where('id_kry', $emp->id_kry)->where('periode', $currentPeriod)->first();
            
            if($hasSlip && $hasSlip->status === 'terkirim') {
                $paidCount++;
            } else {
                $pendingCount++;
            }
        }
    @endphp

    {{-- ── Filter Bar ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-5 sm:px-6 py-4 sm:py-5 bg-white rounded-[28px] shadow-sm outline outline-1 outline-zinc-100">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-purple-600/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="filter" class="w-3.5 h-3.5 text-purple-600"></i>
            </div>
            <div>
                <p class="text-zinc-900/80 text-xs font-black font-inter uppercase leading-4 tracking-wider">Filter Staf</p>
                <p class="text-gray-500 text-[10px] font-medium font-inter leading-4 hidden sm:block">Kelompokkan karyawan berdasarkan peran operasional.</p>
            </div>
        </div>

        <div class="flex items-center gap-1 p-1 bg-gray-50 rounded-2xl outline outline-1 outline-zinc-100 self-start sm:self-auto">
            <button onclick="filterStaff('all')" id="filter-all"
                class="filter-btn px-4 sm:px-5 py-2 rounded-xl text-[10px] font-black font-inter uppercase leading-4 tracking-wide transition-all
                       bg-white shadow-sm text-purple-600 whitespace-nowrap">
                Semua Staf
            </button>
            <button onclick="filterStaff('barista')" id="filter-barista"
                class="filter-btn px-4 sm:px-5 py-2 rounded-xl text-[10px] font-black font-inter uppercase leading-4 tracking-wide transition-all text-gray-500 whitespace-nowrap">
                Barista
            </button>
            <button onclick="filterStaff('game-master')" id="filter-game-master"
                class="filter-btn px-4 sm:px-5 py-2 rounded-xl text-[10px] font-black font-inter uppercase leading-4 tracking-wide transition-all text-gray-500 whitespace-nowrap">
                Pemandu Game
            </button>
        </div>
    </div>

    {{-- ── Tabel Siklus Pembayaran ── --}}
    <div class="bg-white rounded-[40px] shadow-sm outline outline-1 outline-zinc-100 overflow-hidden">

        {{-- Header Tabel --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 sm:px-8 py-5 bg-gray-50/20 border-b border-zinc-100">
            <h2 class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider opacity-70">Log Siklus Pembayaran</h2>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-2xl shadow-sm outline outline-1 outline-zinc-100">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0"></span>
                        <span class="text-zinc-900 text-[10px] font-black font-inter uppercase leading-4 tracking-wide">Dibayar: {{ $paidCount }}</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-2xl shadow-sm outline outline-1 outline-zinc-100">
                        <span class="w-2 h-2 bg-amber-500 rounded-full flex-shrink-0"></span>
                        <span class="text-amber-600 text-[10px] font-black font-inter uppercase leading-4 tracking-wide">Menunggu: {{ $pendingCount }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── TABEL DESKTOP (lg+) ── --}}
        <div class="hidden lg:block overflow-x-auto">
            <div class="grid grid-cols-[1fr_180px_200px_260px] px-8 py-4 border-b border-zinc-100 bg-gray-50/10">
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Karyawan</span>
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Jabatan</span>
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Status Slip</span>
                <span class="text-right text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Aksi Penggajian</span>
            </div>

            <div id="staff-table-desktop">
                @foreach($employees as $employee)
                    @php 
                        // Kunci pencarian slip berdasarkan ID Karyawan DAN Periode bulan berjalan saat ini
                        $slip = $slips->where('id_kry', $employee->id)->where('periode', $currentPeriod)->first();
                    @endphp
                    <div class="staff-row {{ Str::slug($employee->jab) }} grid grid-cols-[1fr_180px_200px_260px] px-8 py-6 border-b border-zinc-100/50 items-center hover:bg-zinc-50/30 transition-colors"
                         data-id="{{ $employee->id }}"
                         data-name="{{ $employee->n_kry }}"
                         data-slip-id="{{ $slip ? $slip->id : '' }}">
                        
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 bg-gray-100 rounded-2xl shadow-sm outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-zinc-900 text-xs font-bold font-inter leading-4">
                                    {{ strtoupper(substr($employee->n_kry, 0, 1)) }}
                                </span>
                            </div>
                            <span class="text-zinc-900 text-base font-bold font-inter leading-6">{{ $employee->n_kry }}</span>
                        </div>
                        
                        <div>
                            <span class="px-3 py-1 bg-gray-100 rounded-xl text-gray-500 text-[10px] font-black font-inter uppercase leading-4 tracking-wide">
                                {{ $employee->jab }}
                            </span>
                        </div>
                        {{-- Ambil data slip milik karyawan untuk periode aktif saat ini --}}
                        @php
                            $currentSlip = $slips->where('id_kry', $employee->id_kry)->where('periode', $currentPeriod)->first();
                        @endphp
                         
                        <div class="flex items-center gap-2">
                            @if($currentSlip && $currentSlip->status == 'terkirim')
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                                <span class="text-emerald-600 text-[10px] font-black font-inter uppercase leading-4 tracking-wide">Dipublikasikan</span>
                            @else
                                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                                <span class="text-amber-600 text-[10px] font-black font-inter uppercase leading-4 tracking-wide">Menunggu</span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-end gap-2">
                            {{-- Cari tahu apakah karyawan ini sudah punya slip di periode aktif saat ini --}}
                            @php
                                $slip = $slips->where('id_kry', $employee->id_kry)->where('periode', $currentPeriod)->first();
                            @endphp

                            @if($slip)
                                {{-- JIKA SUDAH ADA FILE: Tombol berubah menjadi GANTI PDF (Warna Indigo/Custom sesuai UI kamu) --}}
                                <button onclick="openEditModal('{{ $slip->id_slip }}', '{{ $employee->id_kry }}', '{{ $employee->n_kry }}', '{{ $slip->total_gaji }}')"
                                    class="flex items-center gap-2 px-4 h-9 bg-purple-600/10 rounded-2xl text-purple-600 hover:bg-purple-600 hover:text-white transition-all duration-200">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                    <span class="text-[10px] font-black font-inter uppercase leading-4 tracking-wide">Ganti PDF</span>
                                </button>
                            @else
                                {{-- JIKA BELUM ADA FILE: Tombol tetap UNGGAH SLIP --}}
                                <button onclick="openUploadModal('{{ $employee->id_kry }}', '{{ $employee->n_kry }}', 'upload')"
                                    class="flex items-center gap-2 px-4 h-9 bg-purple-600 rounded-2xl text-white shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20)] hover:bg-purple-700 transition-colors">
                                    <i data-lucide="file-up" class="w-3.5 h-3.5"></i>
                                    <span class="text-[10px] font-black font-inter uppercase leading-4 tracking-wide">Unggah Slip</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── KARTU MOBILE & TABLET (< lg) ── --}}
        <div class="lg:hidden divide-y divide-zinc-100" id="staff-table-mobile">
            @foreach($employees as $employee)
                @php 
                    $slip = $slips->where('id_kry', $employee->id)->where('periode', $currentPeriod)->first();
                @endphp
                <div class="staff-row {{ Str::slug($employee->jab) }} px-5 py-5 hover:bg-zinc-50/40 transition-colors"
                     data-id="{{ $employee->id }}"
                     data-name="{{ $employee->n_kry }}"
                     data-slip-id="{{ $slip ? $slip->id : '' }}">
                    
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-2xl outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-zinc-900 text-xs font-bold font-inter">{{ strtoupper(substr($employee->n_kry, 0, 1)) }}</span>
                            </div>
                            <span class="text-zinc-900 text-sm font-bold font-inter truncate">{{ $employee->n_kry }}</span>
                        </div>
                        <span class="flex-shrink-0 px-2.5 py-1 bg-gray-100 rounded-xl text-gray-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wide">
                            {{ $employee->jab }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between gap-3 pl-13" style="padding-left:52px">
                        @if ($slip && $slip->status === 'terkirim')
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                                <span class="text-emerald-600 text-[10px] font-black font-inter uppercase leading-4">Dipublikasikan</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ asset('storage/' . $slip->file_slip) }}" target="_blank"
                                   class="flex items-center gap-1.5 px-3 h-8 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors">
                                    <i data-lucide="eye" class="w-3 h-3"></i>
                                    <span class="text-[9px] font-black font-inter uppercase leading-3">Lihat</span>
                                </a>
                                <button onclick="openDeleteFoodModal(this.closest('.staff-row'))"
                                    class="flex items-center gap-1.5 px-3 h-8 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-100 transition-colors">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                    <span class="text-[9px] font-black font-inter uppercase leading-3">Ganti</span>
                                </button>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5">
                                @php
                                    $slip = $slips->where('id_kry', $employee->id_kry)->where('periode', $currentPeriod)->first();
                                @endphp

                                @if($slip)
                                    <button onclick="openEditModal('{{ $slip->id_slip }}', '{{ $employee->id_kry }}', '{{ $employee->n_kry }}', '{{ $slip->total_gaji }}')"
                                        class="flex items-center gap-1.5 px-3 h-8 bg-purple-600/10 text-purple-600 rounded-xl hover:bg-purple-600 hover:text-white transition-all duration-200">
                                        <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                        <span class="text-[9px] font-black font-inter uppercase leading-3">Ganti</span>
                                    </button>
                                @else
                                    <button onclick="openUploadModal('{{ $employee->id_kry }}', '{{ $employee->n_kry }}', 'upload')"
                                        class="flex items-center gap-1.5 px-3 h-8 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">
                                        <i data-lucide="file-up" class="w-3 h-3"></i>
                                        <span class="text-[9px] font-black font-inter uppercase leading-3">Unggah</span>
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Form Hapus Tersembunyi --}}
<form id="form-delete-slip" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

{{-- ══════════════════════════════════════
     MODAL: Unggah / Ganti Slip (BERSIH & DIPERBAIKI)
══════════════════════════════════════ --}}
<div id="upload-backdrop" data-modal-backdrop data-modal-panel="upload-panel"
     class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeUploadModal()">
    
    <div id="upload-panel"
         class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-7 
                opacity-0 scale-95 transition-all duration-200 max-h-[90vh] overflow-y-auto"
         onclick="event.stopPropagation()">

        <form id="real-upload-form" action="{{ route('owner.payroll.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            
            {{-- Input Hidden --}}
            <input type="hidden" name="id_kry" id="upload-id-kry">

            {{-- Header --}}
            <div class="flex justify-between items-start">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2 mb-1.5">
                        <i data-lucide="file-up" class="w-3.5 h-3.5 text-purple-600"></i>
                        <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Unggah Dokumen</span>
                    </div>
                    <h3 id="upload-title" class="text-zinc-900 text-2xl font-bold font-mono leading-8">Unggah Slip</h3>
                    <p id="upload-subtitle" class="text-gray-500 text-sm font-inter leading-5">
                        Slip gaji untuk <span id="upload-emp-name" class="font-bold text-zinc-900"></span>
                    </p>
                </div>
                <button type="button" onclick="closeUploadModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 ml-3 mt-0.5">
                    <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
                </button>
            </div>

            {{-- Dropdown Periode --}}
            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Periode Pembayaran</label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <select id="upload-period" name="periode" required
                        class="w-full h-12 pl-11 pr-10 bg-white rounded-2xl outline outline-1 outline-zinc-200
                               text-zinc-900 text-sm font-bold font-inter leading-5 appearance-none cursor-pointer
                               focus:outline-2 focus:outline-purple-400 transition-colors">
                        @foreach($periodOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                </div>
            </div>

            {{-- Nominal Gaji --}}
            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Total Gaji (Nominal)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold font-inter text-gray-400">Rp</span>
                    <input type="number" name="total_gaji" required min="0" placeholder="cth. 3000000"
                        class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200
                               text-zinc-900 text-sm font-bold font-inter leading-5
                               focus:outline-2 focus:outline-purple-400 transition-colors">
                </div>
            </div>

            {{-- Area Unggah --}}
            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Berkas Slip Gaji</label>
                <label for="slip-file-input"
                       class="flex flex-col items-center justify-center gap-2 w-full h-36 rounded-2xl
                              outline-2 outline-dashed outline-zinc-200 bg-gray-50/50
                              cursor-pointer hover:bg-purple-600/5 hover:outline-purple-300 transition-all group">
                    <i data-lucide="upload" class="w-7 h-7 text-purple-400 group-hover:text-purple-600 transition-colors"></i>
                    <span class="text-zinc-900 text-sm font-bold font-inter leading-5">Klik untuk memilih slip gaji</span>
                    <span class="text-gray-500 text-[10px] font-medium font-inter uppercase leading-4 tracking-wide">Format PDF • Maks 10MB</span>
                    <input id="slip-file-input" name="file_slip" type="file" accept=".pdf" class="hidden" required onchange="onFileSelected(this)">
                </label>
            </div>

            {{-- Pratinjau file terpilih --}}
            <div id="file-preview" class="hidden flex items-center gap-3 px-4 py-3 bg-emerald-50 rounded-2xl outline outline-1 outline-emerald-100">
                <i data-lucide="file-check" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span id="file-name" class="text-emerald-700 text-xs font-bold font-inter leading-4 truncate flex-1"></span>
                <button type="button" onclick="clearFile()" class="text-emerald-400 hover:text-emerald-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Aksi --}}
            <div class="flex items-center gap-3 pt-1">
                <button type="button" onclick="closeUploadModal()"
                    class="flex-1 h-12 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-700 text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-zinc-50 transition-colors">
                    Batal
                </button>
                
                <button type="button" onclick="submitUploadForm()"
                    class="flex-1 h-12 bg-purple-600 rounded-2xl shadow-[0px_8px_30px_rgba(147,51,234,0.35)] text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-purple-700 transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Publikasikan Sekarang
                </button>
            </div>
        </form>

    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Edit / Ganti Slip (TEMA UNGU)
══════════════════════════════════════ --}}
<div id="edit-backdrop" data-modal-backdrop data-modal-panel="edit-panel"
     class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closeEditModal()">
    
    <div id="edit-panel"
         class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-7 
                opacity-0 scale-95 transition-all duration-200 max-h-[90vh] overflow-y-auto"
         onclick="event.stopPropagation()">

        <form id="real-edit-form" action="" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            
            {{-- Input Hidden --}}
            <input type="hidden" name="id_kry" id="edit-id-kry">
            <input type="hidden" name="periode" value="{{ $currentPeriod }}">

            {{-- Header Modal --}}
            <div class="flex justify-between items-start">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2 mb-1.5">
                        {{-- UBAH: Ikon & Teks Header menjadi Ungu --}}
                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-purple-600"></i>
                        <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Mode Penyesuaian</span>
                    </div>
                    <h3 class="text-zinc-900 text-2xl font-bold font-mono leading-8">Ganti Slip</h3>
                    <p class="text-gray-500 text-sm font-inter leading-5">
                        Ubah slip untuk <span id="edit-emp-name" class="font-bold text-zinc-900"></span>
                    </p>
                </div>
                <button type="button" onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors">
                    <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
                </button>
            </div>

            {{-- Input Gaji Bersih --}}
            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Total Gaji Bersih (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold font-inter text-gray-400">Rp</span>
                    {{-- UBAH: focus:outline-purple-400 saat diklik --}}
                    <input type="number" name="total_gaji" id="edit-total-gaji" required min="0"
                        class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200
                               text-zinc-900 text-sm font-bold font-inter leading-5 focus:outline-2 focus:outline-purple-400 transition-colors">
                </div>
            </div>

            {{-- Area Unggah File Slip Baru (Opsional) --}}
            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Unggah Berkas Slip Gaji Baru (Opsional)</label>
                {{-- UBAH: Efek hover group zone menjadi warna ungu soft (hover:bg-purple-600/5 hover:outline-purple-300) --}}
                <label for="edit-file-input"
                       class="flex flex-col items-center justify-center gap-2 w-full h-32 rounded-2xl
                              outline-2 outline-dashed outline-zinc-200 bg-gray-50/50
                              cursor-pointer hover:bg-purple-600/5 hover:outline-purple-300 transition-all group">
                    {{-- UBAH: Ikon upload berubah jadi ungu saat di-hover --}}
                    <i data-lucide="upload" class="w-7 h-7 text-purple-400 group-hover:text-purple-600 transition-colors"></i>
                    <span class="text-zinc-900 text-xs font-bold font-inter">Klik untuk mengganti file PDF</span>
                    <input id="edit-file-input" name="file_slip" type="file" accept=".pdf" class="hidden" onchange="onEditFileSelected(this)">
                </label>
            </div>

            {{-- File preview ketika file baru dipilih --}}
            <div id="edit-file-preview" class="hidden flex items-center gap-3 px-4 py-3 bg-emerald-50 rounded-2xl outline outline-1 outline-emerald-100">
                <i data-lucide="file-check" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                <span id="edit-file-name" class="text-emerald-700 text-xs font-bold font-inter truncate flex-1"></span>
                <button type="button" onclick="clearEditFile()" class="text-emerald-400 hover:text-emerald-700 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Bagian Tombol Aksi --}}
            <div class="flex items-center gap-3 pt-1">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 h-12 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-700 text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-zinc-50 transition-colors">
                    Batal
                </button>
                {{-- UBAH: Tombol utama menggunakan bg-purple-600 dan bayangan bayang ungu (shadow-[0px_8px_30px_rgba(147,51,234,0.35)]) --}}
                <button type="button" onclick="submitEditForm()"
                    class="flex-1 h-12 bg-purple-600 rounded-2xl shadow-[0px_8px_30px_rgba(147,51,234,0.35)] text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-purple-700 transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════
     MODAL: Konfirmasi Hapus/Ganti
══════════════════════════════════════ --}}
<div id="del-food-backdrop" data-modal-backdrop data-modal-panel="del-food-panel"
     class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closePanel(this, document.getElementById('del-food-panel'))">
    <div id="del-food-panel"
         class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-7 space-y-5
                opacity-0 scale-95 transition-all duration-200"
         onclick="event.stopPropagation()">
        
        <div class="flex justify-between items-start">
            <div class="space-y-0.5">
                <div class="flex items-center gap-2 mb-1.5">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-500"></i>
                    <span class="text-rose-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Zona Peringatan</span>
                </div>
                <h3 class="text-zinc-900 text-2xl font-bold font-mono leading-8">Ganti Slip</h3>
                <p class="text-gray-500 text-sm font-inter leading-5">
                    Apakah Anda yakin ingin mengganti/menghapus berkas slip gaji milik <span id="del-food-name-display" class="font-bold text-zinc-900"></span> untuk periode ini?
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button onclick="closePanel(document.getElementById('del-food-backdrop'), document.getElementById('del-food-panel'))"
                class="flex-1 h-12 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-700 text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-zinc-50 transition-colors">
                Batal
            </button>
            <button onclick="confirmDeleteFood()"
                class="flex-1 h-12 bg-rose-600 rounded-2xl shadow-lg text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-rose-700 transition-colors">
                Hapus & Ganti
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const filterBtns = document.querySelectorAll('.filter-btn');
    function filterStaff(role) {
        filterBtns.forEach(b => {
            b.classList.remove('bg-white', 'shadow-sm', 'text-purple-600');
            b.classList.add('text-gray-500');
        });
        const active = document.getElementById('filter-' + role);
        if (active) {
            active.classList.add('bg-white', 'shadow-sm', 'text-purple-600');
            active.classList.remove('text-gray-500');
        }
        document.querySelectorAll('.staff-row').forEach(row => {
            if (role === 'all') {
                row.classList.remove('hidden');
            } else {
                row.classList.toggle('hidden', !row.classList.contains(role));
            }
        });
    }

    function openUploadModal(idKry, name, mode) {
        // 1. ISI ID KARYAWAN KE INPUT HIDDEN FORM (Pastikan ID element-nya sesuai)
        const hiddenInput = document.getElementById('upload-id-kry');
        if (hiddenInput) {
            hiddenInput.value = idKry;
        } else {
            console.error("Elemen 'upload-id-kry' tidak ditemukan!");
        }

        // 2. Set teks nama karyawan dan title modal
        document.getElementById('upload-emp-name').textContent = name;
        document.getElementById('upload-title').textContent = mode === 'replace' ? 'Ganti Slip' : 'Unggah Slip';
        
        clearFile();
        openPanel(document.getElementById('upload-backdrop'), document.getElementById('upload-panel'));
    }
    
    function closeUploadModal() {
        closePanel(document.getElementById('upload-backdrop'), document.getElementById('upload-panel'));
    }
    
    function onFileSelected(input) {
        if (input.files.length) {
            document.getElementById('file-name').textContent = input.files[0].name;
            document.getElementById('file-preview').classList.remove('hidden');
        }
    }
    
    function clearFile() {
        document.getElementById('slip-file-input').value = '';
        document.getElementById('file-preview').classList.add('hidden');
    }

    let pendingDeleteRow = null;

    function openDeleteFoodModal(row) {
        pendingDeleteRow = row;
        const slipId = row.dataset.slipId;
        document.getElementById('del-food-name-display').textContent = row.dataset.name;
        
        // Mengarahkan action ke route delete slip asli
        document.getElementById('form-delete-slip').action = `/owner/payroll/${slipId}`;
        
        openPanel(document.getElementById('del-food-backdrop'), document.getElementById('del-food-panel'));
    }

    function confirmDeleteFood() {
        if (!pendingDeleteRow) return;
        document.getElementById('form-delete-slip').submit();
    }

    function clearFile() {
        document.getElementById('slip-file-input').value = '';
        document.getElementById('file-preview').classList.add('hidden');
    }

    // --- TAMBAHKAN FUNGSI BARU INI ---
    function submitUploadForm() {
        const form = document.getElementById('real-upload-form');
        const idKryInput = document.getElementById('upload-id-kry');
        const fileInput = document.getElementById('slip-file-input');
        const totalGaji = document.getElementsByName('total_gaji')[0];

        // PENGAMAN UTAMA: Cek apakah ID karyawan benar-benar terisi di hidden input
        if (!idKryInput.value) {
            alert('Gagal sistem: ID Karyawan tidak terdeteksi! Silakan tutup modal dan klik tombol Unggah Slip kembali.');
            return;
        }
        if (!totalGaji.value || totalGaji.value <= 0) {
            alert('Silakan isi nominal total gaji dengan benar!');
            return;
        }
        if (!fileInput.files.length) {
            alert('Silakan pilih berkas file PDF slip gaji terlebih dahulu!');
            return;
        }

        // Jika semua lolos, kirim ke Backend
        form.submit();
    }

    function openEditModal(idSlip, idKry, name, totalGaji) {
        // 1. Tembak URL Action Form ke route update sesuai id_slip
        document.getElementById('real-edit-form').action = `/owner/payroll/${idSlip}`;
        
        // 2. Isi data default ke dalam field modal edit
        document.getElementById('edit-id-kry').value = idKry;
        document.getElementById('edit-total-gaji').value = totalGaji;
        document.getElementById('edit-emp-name').textContent = name;
        
        clearEditFile();
        openPanel(document.getElementById('edit-backdrop'), document.getElementById('edit-panel'));
    }

    function closeEditModal() {
        closePanel(document.getElementById('edit-backdrop'), document.getElementById('edit-panel'));
    }

    function onEditFileSelected(input) {
        if (input.files.length) {
            document.getElementById('edit-file-name').textContent = input.files[0].name;
            document.getElementById('edit-file-preview').classList.remove('hidden');
        }
    }

    function clearEditFile() {
        document.getElementById('edit-file-input').value = '';
        document.getElementById('edit-file-preview').classList.add('hidden');
    }

    function submitEditForm() {
        const form = document.getElementById('real-edit-form');
        const totalGaji = document.getElementById('edit-total-gaji');

        if (!totalGaji.value || totalGaji.value <= 0) {
            alert('Silakan isi nominal total gaji modifikasi dengan benar!');
            return;
        }

        form.submit();
    }

    lucide.createIcons();
</script>
@endpush