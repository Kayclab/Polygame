@extends('layouts.app')

@section('title', 'Beranda - Poly Games Cafe')

@section('content')
<div class="px-6 sm:px-10 lg:px-14 py-8">

    {{-- ---- Header Halaman ---- --}}
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-2">
            <i data-lucide="layout-grid" class="w-4 h-4 text-purple-600"></i>
            <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Ringkasan Ruang Kerja</span>
        </div>
        <h1 class="text-zinc-900 text-2xl sm:text-3xl font-bold font-mono leading-9 mb-1">Selamat Datang, {{ Auth::user()->n_kry ?? 'Staf'}}!</h1>
        <p class="text-gray-500 text-sm font-normal font-mono leading-5">Ini yang sedang terjadi di hub Anda hari ini.</p>
    </div>

    {{-- ---- Kartu Statistik (Versi Upsize) ---- --}}
    {{-- Grid tetap 2x2 di HP (grid-cols-2) dan 4 sejajar di Desktop (xl:grid-cols-4) --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-10">

        <div class="bg-white rounded-[24px] shadow-sm hover:shadow-md border border-zinc-100 p-6 flex flex-col justify-between min-h-[140px] transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 text-[10px] sm:text-[11px] font-bold font-inter uppercase tracking-widest opacity-70">Jumlah Karyawan</span>
                <div class="w-8 h-8 rounded-xl bg-zinc-50 flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-zinc-400"></i>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-baseline gap-2">
                    {{-- Tampilkan Total Karyawan Dinamis --}}
                    <span class="text-zinc-900 text-3xl sm:text-4xl font-bold font-inter tracking-tight">
                        {{ $totalEmployees }}
                    </span>
                    
                    {{-- Tampilkan Persentase Pertumbuhan Dinamis --}}
                    @if($growthPercentage >= 0)
                        <span class="px-2 py-0.5 bg-emerald-50 rounded-full text-emerald-600 text-[10px] font-bold font-inter">
                            +{{ number_format($growthPercentage, 1) }}%
                        </span>
                    @else
                        <span class="px-2 py-0.5 bg-rose-50 rounded-full text-rose-600 text-[10px] font-bold font-inter">
                            {{ number_format($growthPercentage, 1) }}%
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[24px] shadow-sm hover:shadow-md border border-zinc-100 p-6 flex flex-col justify-between min-h-[140px] transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 text-[10px] sm:text-[11px] font-bold font-inter uppercase tracking-widest opacity-70">Sedang Ditinjau</span>
                <div class="w-8 h-8 rounded-xl bg-zinc-50 flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-zinc-400"></i>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-baseline gap-2">
                    <span class="text-zinc-900 text-3xl sm:text-4xl font-bold font-inter tracking-tight">
                        {{ $pendingReviews }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[24px] shadow-sm hover:shadow-md border border-zinc-100 p-6 flex flex-col justify-between min-h-[140px] transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 text-[10px] sm:text-[11px] font-bold font-inter uppercase tracking-widest opacity-70">Gaji Bulanan</span>
                <div class="w-8 h-8 rounded-xl bg-zinc-50 flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-4 h-4 text-zinc-400"></i>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex flex-col gap-1">
                    <span class="text-zinc-900 text-2xl sm:text-3xl font-bold font-inter tracking-tight">
                        Rp {{ number_format($monthlyPayroll, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[24px] shadow-sm border border-zinc-100 p-6 flex flex-col justify-between min-h-[140px]">
            <div class="flex items-center justify-between mb-4">
                <span class="text-gray-500 text-[10px] sm:text-[11px] font-bold font-inter uppercase tracking-widest opacity-70">Operasional Sistem</span>
                <div class="w-8 h-8 rounded-xl bg-zinc-50 flex items-center justify-center">
                    <i data-lucide="activity" class="w-4 h-4 text-zinc-400"></i>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex flex-col gap-1">
                    <span class="text-zinc-900 text-3xl sm:text-4xl font-bold font-inter tracking-tight">
                        {{ $systemHealth }}%
                    </span>
                    
                    {{-- Warna badge berubah dinamis sesuai status kesehatan sistem --}}
                    <div>
                        @if($systemStatus === 'OPERATIONAL')
                            <span class="px-2 py-0.5 bg-emerald-50 rounded-full text-emerald-600 text-[10px] font-bold font-inter subpixel-antialiased uppercase">
                                OPERASIONAL
                            </span>
                        @else
                            <span class="px-2 py-0.5 bg-rose-50 rounded-full text-rose-600 text-[10px] font-bold font-inter subpixel-antialiased uppercase">
                                {{ $systemStatus }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ---- Grid Utama ---- --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_384px] gap-8">

        {{-- KOLOM KIRI --}}
        <div class="flex flex-col gap-8">

            {{-- Pengumuman Staf --}}
            <div>
                {{-- Header & Tombol Tambah --}}
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <i data-lucide="megaphone" class="w-4 h-4 text-purple-600"></i>
                        <span class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider">Pengumuman Staf</span>
                    </div>
                    <button onclick="openAnnouncementModal()" class="flex items-center gap-2 px-4 h-8 bg-purple-600/5 rounded-2xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] hover:bg-purple-600/10 transition-colors">
                        <i data-lucide="plus" class="w-3.5 h-3.5 text-purple-600"></i>
                        <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Unggahan Baru</span>
                    </button>
                </div>

                {{-- Notifikasi Error/Sukses --}}
                @if(session('success'))
                    <div class="bg-emerald-50 text-emerald-700 outline outline-1 outline-emerald-200 p-4 rounded-xl text-xs font-bold mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Kotak Wadah Row --}}
                <div class="bg-white rounded-2xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-zinc-100 overflow-hidden">

                    @forelse($announcements as $announcement)
                        {{-- Tambahkan border-b pada baris, kecuali untuk item terakhir agar desainnya rapi --}}
                        <div class="group flex items-center gap-5 px-8 py-6 {{ !$loop->last ? 'border-b border-zinc-100' : '' }} hover:bg-zinc-50/60 transition-colors">
                            <div class="w-10 h-10 bg-purple-600/5 rounded-2xl flex justify-center items-center flex-shrink-0">
                                <i data-lucide="bell" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-zinc-900 text-sm font-bold font-inter leading-5 mb-0.5 whitespace-normal break-words" style="overflow-wrap:anywhere; word-break:break-word;">{{ $announcement->title }}</p>
                                <p class="text-gray-500 text-xs font-normal font-inter leading-4 opacity-70 mb-1 whitespace-normal break-words" style="overflow-wrap:anywhere; word-break:break-word;">{{ $announcement->content }}</p>
                                <div class="flex flex-wrap items-center gap-3">
                                    {{-- Mengambil tanggal pendaftaran dengan format tahun-bulan-hari --}}
                                    <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">
                                        {{ $announcement->created_at->format('Y-m-d') }}
                                    </span>
                                    <span class="text-gray-500 text-[10px] font-normal font-inter leading-4 opacity-40">•</span>
                                    {{-- SEKARANG DIGANTI: Memanggil nama karyawan melalui fungsi relasi 'karyawan' di Model --}}
                                    <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">
                                        Oleh {{ $announcement->karyawan->n_kry ?? 'Admin Tidak Diketahui' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Tombol Aksi (Akan Mengirim Data ke JavaScript Modal Milikmu) --}}
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150 flex-shrink-0">
                                {{-- Tombol Edit melempar data Judul dan Isi asli dari DB --}}
                                <button onclick="openEditModal('{{ $announcement->id_announcement }}', '{{ addslashes($announcement->title) }}', '{{ addslashes($announcement->content) }}')" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-purple-600/10 transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5 text-gray-400 hover:text-purple-600 transition-colors"></i>
                                </button>
                                
                                {{-- Tombol Hapus memicu konfirmasi hapus --}}
                                <button onclick="confirmDelete('{{ $announcement->id_announcement }}', '{{ addslashes($announcement->title) }}')" 
                                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-rose-50 transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-gray-400 hover:text-rose-500 transition-colors"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-zinc-400 text-xs font-medium">
                            Belum ada pengumuman untuk staf saat ini.
                        </div>
                    @endforelse

                </div>
            </div>

            {{-- Tag Form Hidden Pendukung Aksi Hapus Murni Laravel --}}
            <form id="form-delete-announcement" action="" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>

                {{-- ---- Ringkasan Operasional Staf (Versi Dinamis) ---- --}}
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <i data-lucide="activity" class="w-5 h-5 text-purple-600"></i>
                        <span class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider">Ringkasan Operasional Staf</span>
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">

                        {{-- Pinjaman --}}
                        <div class="bg-white rounded-2xl shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] outline outline-1 outline-zinc-100 p-5 sm:p-6 flex flex-col justify-between min-h-[160px]">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 bg-amber-50 rounded-2xl flex items-center justify-center">
                                    <i data-lucide="dollar-sign" class="w-5 h-5 text-amber-600"></i>
                                </div>
                                <span class="px-2.5 py-1 bg-amber-50 rounded text-amber-600 text-[10px] font-black font-inter uppercase tracking-wide">Pinjaman</span>
                            </div>
                            <div>
                                <p class="text-zinc-900 text-2xl font-black font-inter leading-tight mb-1">Rp {{ number_format($pinjamans->sum('total'),0,',','.') }}</p>
                                <p class="text-gray-500 text-[11px] font-bold font-inter uppercase leading-4 mb-4">Kemajuan Terkini</p>
                            </div>
                            <div class="border-t border-zinc-100 pt-3">
                                <p class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-3">{{ $pinjamans->count() }} Staf yang Terlibat</p>
                            </div>
                        </div>

                        {{-- Evaluasi --}}
                        <div class="bg-white rounded-2xl shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] outline outline-1 outline-zinc-100 p-5 sm:p-6 flex flex-col justify-between min-h-[160px]">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 bg-purple-50 rounded-2xl flex items-center justify-center">
                                    <i data-lucide="bar-chart-2" class="w-5 h-5 text-purple-600"></i>
                                </div>
                                <span class="px-2.5 py-1 bg-purple-50 rounded text-purple-600 text-[10px] font-black font-inter uppercase tracking-wide">Evaluasi</span>
                            </div>
                            <div>
                                <p class="text-zinc-900 text-2xl font-black font-inter leading-tight mb-1">{{ number_format($avgEvaluasi,2) ?? 0 }}</p>
                                <p class="text-gray-500 text-[11px] font-bold font-inter uppercase leading-4 mb-4">Rata-rata Skor</p>
                            </div>
                            <div class="border-t border-zinc-100 pt-3">
                                <p class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-3">Data Evaluasi Staf</p>
                            </div>
                        </div>

                        {{-- Lembur --}}
                        <div class="bg-white rounded-2xl shadow-[0px_1px_3px_0px_rgba(0,0,0,0.1)] outline outline-1 outline-zinc-100 p-5 sm:p-6 flex flex-col justify-between min-h-[160px]">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 bg-green-50 rounded-2xl flex items-center justify-center">
                                    <i data-lucide="clock" class="w-5 h-5 text-green-600"></i>
                                </div>
                                <span class="px-2.5 py-1 bg-green-50 rounded text-green-600 text-[10px] font-black font-inter uppercase tracking-wide">Lembur</span>
                            </div>
                            <div>
                                <p class="text-zinc-900 text-2xl font-black font-inter leading-tight mb-1">{{ $totalLemburPending ?? 0 }} jam</p>
                                <p class="text-gray-500 text-[11px] font-bold font-inter uppercase leading-4 mb-4">Jam Belum ACC</p>
                            </div>
                            <div class="border-t border-zinc-100 pt-3">
                                <p class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-3">Staf yang Terlibat</p>
                            </div>
                        </div>

                    </div>
                </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="flex flex-col gap-8">

            {{-- Alat Instan --}}
            <div>
                <div class="flex items-center gap-2 mb-5">
                    <i data-lucide="layout-grid" class="w-4 h-4 text-purple-600"></i>
                    <span class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider">Alat Instan</span>
                </div>
                <div class="flex flex-col gap-2">
                    <button class="flex items-center justify-between px-6 py-4 bg-purple-600 rounded-2xl shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.10),0px_10px_15px_-3px_rgba(147,51,234,0.10)] outline outline-1 outline-purple-600 hover:bg-purple-700 transition-colors">
                        <a href="{{ route('owner.payroll.index') }}"><span class="text-white text-xs font-bold font-inter uppercase leading-4 tracking-wider">Proses Gaji</span></a>
                        <i data-lucide="dollar-sign" class="w-3.5 h-3.5 text-white opacity-70"></i>
                    </button>
                    <button class="flex items-center justify-between px-6 py-4 bg-white rounded-2xl outline outline-1 outline-zinc-100 hover:bg-zinc-50 transition-colors">
                        <a href="{{ route('owner.employees.index') }}"><span class="text-zinc-900 text-xs font-bold font-inter uppercase leading-4 tracking-wider">Tambah Staf</span></a>
                        <i data-lucide="user-plus" class="w-3.5 h-3.5 text-purple-600"></i>
                    </button>
                    <button class="flex items-center justify-between px-6 py-4 bg-white rounded-2xl outline outline-1 outline-zinc-100 hover:bg-zinc-50 transition-colors">
                        <a href="{{ route('owner.allowance.index') }}"><span class="text-zinc-900 text-xs font-bold font-inter uppercase leading-4 tracking-wider">Tinjau Permintaan Pinjaman</span></a>
                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-purple-600"></i>
                    </button>
                </div>
            </div>


        </div>
    </div>

</div>
@endsection


{{-- ===================== MODALS ===================== --}}
@push('modals')

{{-- ══════════════════════════════════════
     MODAL TAMBAH PENGUMUMAN (TEMA TETAP)
══════════════════════════════════════ --}}
<div id="announcement-backdrop" data-modal-backdrop data-modal-panel="announcement-panel"
     class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closePanel(this, document.getElementById('announcement-panel'))">
    
    <div id="announcement-panel" 
         class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-7 space-y-5
                opacity-0 scale-95 transition-all duration-200"
         onclick="event.stopPropagation()">
        
        {{-- Header Modal --}}
        <div class="flex justify-between items-start">
            <div class="space-y-0.5">
                <div class="flex items-center gap-2 mb-1">
                    <i data-lucide="megaphone" class="w-3.5 h-3.5 text-purple-600"></i>
                    <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Pusat Siaran</span>
                </div>
                <h2 class="text-zinc-900 text-2xl font-bold font-mono leading-8">Pengumuman Baru</h2>
            </div>
            <button type="button" onclick="closePanel(document.getElementById('announcement-backdrop'), document.getElementById('announcement-panel'))"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0">
                <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>

        {{-- Form Input --}}
        <form method="POST" action="{{ route('owner.announcements.store') }}" class="space-y-4">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Judul</label>
                <input id="ann-title" name="title" type="text" placeholder="Masukkan judul pengumuman..."
                       class="w-full h-12 px-4 bg-gray-50/50 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-medium font-inter focus:outline-2 focus:outline-purple-400 focus:bg-white transition-all" required />
            </div>

            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Isi</label>
                <textarea id="ann-content" name="content" rows="5" placeholder="Tulis pesan Anda untuk staf..."
                          class="w-full p-4 bg-gray-50/50 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-medium font-inter focus:outline-2 focus:outline-purple-400 focus:bg-white resize-none h-36 transition-all" required></textarea>
            </div>

            <button type="submit" 
                    class="w-full h-12 bg-purple-600 rounded-2xl shadow-[0px_8px_30px_rgba(147,51,234,0.35)] text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-purple-700 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i>
                Kirim Pengumuman
            </button>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════
     MODAL EDIT PENGUMUMAN (TEMA TETAP)
══════════════════════════════════════ --}}
<div id="edit-backdrop" data-modal-backdrop data-modal-panel="edit-panel"
     class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closePanel(this, document.getElementById('edit-panel'))">
    
    <div id="edit-panel" 
         class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-7 space-y-5
                opacity-0 scale-95 transition-all duration-200"
         onclick="event.stopPropagation()">
        
        {{-- Header Modal --}}
        <div class="flex justify-between items-start">
            <div class="space-y-0.5">
                <div class="flex items-center gap-2 mb-1">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5 text-purple-600"></i>
                    <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Mode Koreksi</span>
                </div>
                <h2 class="text-zinc-900 text-2xl font-bold font-mono leading-8">Edit Pengumuman</h2>
            </div>
            <button type="button" onclick="closePanel(document.getElementById('edit-backdrop'), document.getElementById('edit-panel'))"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0">
                <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>

        {{-- Form Input --}}
        <form id="edit-form" method="POST" action="#" class="space-y-4">
            @csrf 
            @method('PUT')
            
            <input type="hidden" id="edit-id" name="id" />

            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Judul</label>
                <input id="edit-title" name="title" type="text" placeholder="Masukkan judul pengumuman..."
                       class="w-full h-12 px-4 bg-gray-50/50 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-medium font-inter focus:outline-2 focus:outline-purple-400 focus:bg-white transition-all" required />
            </div>

            <div class="space-y-1.5">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Isi</label>
                <textarea id="edit-content" name="content" rows="5" placeholder="Tulis pesan Anda untuk staf..."
                          class="w-full p-4 bg-gray-50/50 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-medium font-inter focus:outline-2 focus:outline-purple-400 focus:bg-white resize-none h-36 transition-all" required></textarea>
            </div>

            <button type="submit" 
                    class="w-full h-12 bg-purple-600 rounded-2xl shadow-[0px_8px_30px_rgba(147,51,234,0.35)] text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-purple-700 active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div id="delete-backdrop" data-modal-backdrop data-modal-panel="delete-panel"
     class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     onclick="if(event.target===this) closePanel(this, document.getElementById('delete-panel'))">
    <div id="delete-panel" class="relative bg-white rounded-[32px] shadow-2xl w-full max-w-sm p-7 space-y-5
                opacity-0 scale-95 transition-all duration-200">
        <div class="flex flex-col items-center gap-4">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center">
                <i data-lucide="trash-2" class="w-6 h-6 text-rose-500"></i>
            </div>
            <div>
                <h3 class="text-zinc-900 text-lg font-bold font-mono leading-6 mb-1">Hapus Pengumuman</h3>
                <p class="text-gray-500 text-sm font-normal font-inter leading-5">Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="flex gap-3 w-full">
            <button onclick="closePanel(document.getElementById('delete-backdrop'), document.getElementById('delete-panel'))"
                    class="pgc-btn-ghost flex-1 h-11 rounded-2xl">Batal</button>
            <form id="delete-form" method="POST" action="#" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full h-11 bg-rose-500 rounded-2xl text-white text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-rose-600 active:scale-[0.98] transition-all">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@endpush


{{-- ===================== SCRIPTS ===================== --}}
@push('scripts')
<script>
    // 1. MODAL TAMBAH: Membuka panel pengumuman baru
    function openAnnouncementModal() {
        openPanel(
            document.getElementById('announcement-backdrop'),
            document.getElementById('announcement-panel')
        );
    }

    // 2. MODAL EDIT: Mengisi data lama ke input dan mengarahkan action form ke route UPDATE
    function openEditModal(id, title, content) {
        // PENTING: Arahkan URL action form edit ke route update di dalam prefix /owner/
        document.getElementById('edit-form').action = '/owner/' + id;

        document.getElementById('edit-id').value      = id;
        document.getElementById('edit-title').value   = title;
        document.getElementById('edit-content').value = content;
        
        openPanel(
            document.getElementById('edit-backdrop'),
            document.getElementById('edit-panel')
        );
    }

    // 3. MODAL DELETE: Mengarahkan action form ke route DESTROY dengan benar
    function confirmDelete(id) {
        // PENTING: Tambahkan prefix /owner/ agar route delete tidak memicu error 404
        document.getElementById('delete-form').action = '/owner/' + id;
        
        openPanel(
            document.getElementById('delete-backdrop'),
            document.getElementById('delete-panel')
        );
    }
</script>
@endpush