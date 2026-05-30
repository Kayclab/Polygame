@extends('layouts.app')

@section('title', 'Tunjangan Makan - Poly Games Cafe')

@section('content')
<div class="flex flex-col h-full">

    {{-- ===================== PAGE HEADER ===================== --}}
    <div class="px-6 sm:px-10 lg:px-14 py-8 flex flex-col sm:flex-row sm:items-end justify-between gap-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="utensils" class="w-4 h-4 text-purple-600"></i>
                <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Kontrol Pengeluaran & Menu</span>
            </div>
            <h1 class="text-zinc-900 text-2xl sm:text-3xl font-bold font-mono leading-9 mb-1">Pengawasan Tunjangan Makan</h1>
            <p class="text-gray-500 text-sm font-normal font-mono leading-6 opacity-80">
                Kelola biaya menu dan pantau tren aktivitas kasbon staf.
            </p>
        </div>

        {{-- Tab Toggle --}}
        <div class="flex-shrink-0 w-full sm:w-auto">
            <div class="inline-flex items-center w-full sm:w-auto h-11 p-1 bg-gray-50 rounded-2xl outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
                <button id="tab-overview" onclick="switchTab('overview')"
                        class="flex-1 sm:flex-none h-9 px-6 bg-white rounded-xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide transition-all">
                    Ringkasan Staf
                </button>
                <button id="tab-food" onclick="switchTab('food')"
                        class="flex-1 sm:flex-none h-9 px-6 rounded-xl text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide transition-all hover:text-zinc-900">
                    Manajemen Menu
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== CONTENT AREA ===================== --}}
    <div class="flex-1 px-6 sm:px-10 lg:px-14 pb-8">

        {{-- ---- TAB: STAFF OVERVIEW ---- --}}
        <div id="panel-overview" class="h-full flex-col lg:flex-row gap-6" style="display:flex">

            {{-- LEFT: Staff Ranking --}}
            <div class="flex-1 min-w-0 flex flex-col gap-6">

                {{-- Section Header --}}
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="text-zinc-900 font-bold font-inter text-m uppercase tracking-wider">Peringkat Staf Berdasarkan Penggunaan</h3>
                    </div>
                    <span class="text-gray-400 text-xs font-bold font-inter">
                        BATAS: Rp {{ number_format($allowanceCap, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Ranking List --}}
                <div class="flex flex-col gap-3" id="ranking-list">

                    <div class="space-y-4">
                        @foreach($staffRanking as $index => $staff)
                            @php
                                // Hitung sisa limit secara real-time
                                $remaining = $allowanceCap - $staff->total_spent;
                                $initial = substr($staff->n_kry, 0, 1);

                                // Ambil riwayat dari model Pinjaman khusus untuk staff ini
                                $historyData = \App\Models\Pinjaman::where('karyawan_id', $staff->id_kry)
                                    ->where('status', 'approved')
                                    ->orderBy('tanggal', 'desc')
                                    ->get()
                                    ->map(function($item) {
                                        return [
                                            'date' => \Carbon\Carbon::parse($item->tanggal)->translatedFormat('M d'),
                                            'desc' => $item->keterangan ?? 'Kasbon '.ucfirst($item->type),
                                            'total' => number_format($item->total, 0, ',', '.')
                                        ];
                                    });
                            @endphp

                            {{-- Komponen Kartu Staff --}}
                            <div class="staff-rank-card group relative bg-white rounded-3xl outline outline-1 outline-zinc-100 px-6 py-7 flex items-center justify-between cursor-pointer transition-all duration-200 hover:outline-purple-200 hover:shadow-[0px_0px_0px_4px_rgba(147,51,234,0.05)]"
                                data-name="{{ $staff->n_kry }}" 
                                data-role="{{ $staff->jab ?? 'Barista' }}" 
                                data-initial="{{ $initial }}"
                                data-spent="{{ number_format($staff->total_spent, 0, ',', '.') }}" 
                                data-remaining="{{ number_format($remaining, 0, ',', '.') }}"
                                data-percent="{{ $allowanceCap > 0 ? ($staff->total_spent / $allowanceCap) * 100 : 0 }}"
                                data-history="{{ json_encode($historyData) }}"
                                onclick="selectStaff(this)">
                                
                                <div class="flex items-center gap-5">
                                    {{-- Peringkat & Avatar Inisial --}}
                                    <div class="relative flex-shrink-0">
                                        <div class="w-14 h-14 bg-gray-50 rounded-2xl outline outline-1 outline-zinc-100 flex items-center justify-center shadow-sm">
                                            <span class="text-zinc-900 text-sm font-bold font-inter uppercase">{{ $initial }}</span>
                                        </div>
                                        <div class="absolute -top-2 -left-2 w-6 h-6 bg-purple-200 rounded-xl flex items-center justify-center shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
                                            <span class="text-purple-600 text-[9px] font-black font-inter leading-3">#{{ $index + 1 }}</span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <p class="text-zinc-900 text-base font-bold font-inter leading-6">{{ $staff->n_kry }}</p>
                                        <p class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide opacity-60">{{ $staff->jab ?? 'Barista' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-8">
                                    <div class="text-right">
                                        <p class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide opacity-60 mb-1">Total Kasbon</p>
                                        <p class="text-purple-600 text-base font-bold font-inter leading-6">
                                            {{ number_format($staff->total_spent, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-purple-600 opacity-0 group-hover:opacity-100 transition-all flex-shrink-0"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            {{-- RIGHT: Detail Card — DESKTOP ONLY (hidden on mobile/tablet) --}}
            <div class="hidden lg:block w-96 flex-shrink-0">

                {{-- Empty State (Default saat halaman baru dibuka) --}}
                <div id="detail-empty"
                    class="h-full min-h-[320px] bg-white rounded-[40px] outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] flex flex-col items-center justify-center gap-3 px-8 text-center">
                    <div class="w-12 h-12 bg-purple-600/5 rounded-2xl flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-purple-600 opacity-40"></i>
                    </div>
                    <p class="text-gray-400 text-[10px] font-bold font-inter uppercase leading-4 tracking-widest">Pilih Staf untuk Melihat Detail</p>
                </div>

                {{-- Filled Detail Card (Awalnya tersembunyi lewat class 'hidden') --}}
                <div id="detail-card"
                    class="hidden bg-white rounded-[40px] outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] overflow-hidden">

                    {{-- Avatar Header --}}
                    <div class="bg-gray-50/20 border-b border-zinc-100 flex flex-col items-center pt-8 pb-6 gap-3">
                        <div class="w-20 h-20 bg-gray-100 rounded-3xl outline outline-4 outline-white shadow-[0px_20px_25px_-5px_rgba(0,0,0,0.05)] flex items-center justify-center">
                            <span id="dc-initial" class="text-zinc-900 text-xl font-bold font-inter">E</span>
                        </div>
                        <div class="text-center">
                            <p id="dc-name" class="text-zinc-900 text-lg font-bold font-inter leading-7">Emily Taylor</p>
                            <p id="dc-role" class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-widest opacity-60 mt-0.5">Barista</p>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="px-8 pt-6 pb-5 grid grid-cols-2 gap-3">
                        <div class="bg-gray-100/30 rounded-2xl px-4 pt-4 pb-3">
                            <p class="text-gray-500 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide mb-2 text-center">Terpakai</p>
                            <p id="dc-spent" class="text-purple-600 text-lg font-bold font-inter leading-7 text-center">0</p>
                        </div>
                        <div class="bg-emerald-50/50 rounded-2xl px-4 pt-4 pb-3">
                            <p class="text-gray-500 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide mb-2 text-center">Sisa</p>
                            <p id="dc-remaining" class="text-emerald-600 text-lg font-bold font-inter leading-7 text-center">0</p>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="px-8 pb-5">
                        <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                            <div id="dc-progress" class="h-full bg-purple-600 rounded-full transition-all duration-500" style="width:0%"></div>
                        </div>
                        <div class="flex justify-between mt-1.5">
                            <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3">0</span>
                            <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3">Batas: {{ number_format($allowanceCap, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Riwayat Detail --}}
                    <div class="px-8 pb-7">
                        <div class="flex items-center gap-2 mb-3 opacity-70">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-zinc-900"></i>
                            <span class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Riwayat Detail</span>
                        </div>
                        
                        {{-- Beri ID 'dc-history-container' untuk tempat injeksi log transaksi baru --}}
                        <div id="dc-history-container" class="flex flex-col gap-2 max-h-[240px] overflow-y-auto pr-1">
                            </div>
                    </div>

                </div>
            </div>

        </div>{{-- end panel-overview --}}

        {{-- ---- TAB: FOOD MANAGEMENT ---- --}}
        <div id="panel-food" class="hidden flex-col gap-6">

            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-2">
                <div>
                    <p class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider opacity-70">Penetapan Harga Item Menu</p>
                    <p class="text-gray-500 text-[10px] font-medium font-mono uppercase leading-4 mt-0.5 opacity-60">Tentukan harga modal untuk semua item makanan dan minuman yang tersedia.</p>
                </div>
                <button onclick="openAddFoodModal()"
                        class="flex-shrink-0 flex items-center gap-2 px-5 h-9 bg-purple-600 rounded-2xl text-white text-[10px] font-bold font-inter uppercase leading-4 tracking-wider hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20),0px_10px_15px_-3px_rgba(147,51,234,0.20)]">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    Tambah Menu Baru
                </button>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-[40px] outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] overflow-hidden">

                {{-- Table Header --}}
                <div class="hidden sm:grid grid-cols-[1fr_160px_200px_120px] px-8 py-4 border-b border-zinc-100 bg-gray-50/20">
                    <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Nama Menu</span>
                    <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Kategori</span>
                    <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Harga Modal</span>
                    <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide text-right">Aksi</span>
                </div>

                {{-- Rows --}}
                <div class="divide-y divide-zinc-100">
                    
                    @forelse($menus as $menu)
                        {{-- Kita simpan ID asli untuk keperluan action JavaScript --}}
                        <div class="food-row group grid grid-cols-1 sm:grid-cols-[1fr_160px_200px_120px] items-center px-6 sm:px-8 py-5 hover:bg-zinc-50/60 transition-colors"
                            data-id="{{ $menu->id }}" 
                            data-name="{{ $menu->food_name }}" 
                            data-category="{{ $menu->category }}" 
                            data-price="{{ (int)$menu->cost_price }}">
                            
                            {{-- Nama Makanan & Icon Dinamis berdasarkan Kategori --}}
                            <div class="flex items-center gap-3 mb-2 sm:mb-0">
                                @if($menu->category == 'Makanan Berat')
                                    <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="utensils" class="w-3.5 h-3.5 text-amber-500"></i>
                                    </div>
                                @elseif($menu->category == 'Minuman')
                                    <div class="w-8 h-8 bg-purple-600/5 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="coffee" class="w-3.5 h-3.5 text-purple-500"></i>
                                    </div>
                                @else {{-- Cemilan --}}
                                    <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="cookie" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    </div>
                                @endif
                                <span class="text-zinc-900 text-sm font-bold font-inter leading-5">{{ $menu->food_name }}</span>
                            </div>

                            {{-- Badge Kategori Berwarna Dinamis --}}
                            <div class="flex items-center gap-2 mb-1 sm:mb-0 pl-11 sm:pl-0">
                                @if($menu->category == 'Makanan Berat')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 rounded-full text-amber-600 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide outline outline-1 outline-amber-100">Makanan Berat</span>
                                @elseif($menu->category == 'Minuman')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 rounded-full text-blue-500 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide outline outline-1 outline-blue-100">Minuman</span>
                                @else {{-- Cemilan --}}
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 rounded-full text-emerald-600 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide outline outline-1 outline-emerald-100">Cemilan</span>
                                @endif
                            </div>

                            {{-- Harga dengan format nomor ribuan --}}
                            <div class="pl-11 sm:pl-0">
                                <span class="text-purple-600 text-sm font-bold font-inter leading-5">
                                    Rp {{ number_format($menu->cost_price, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center justify-end gap-1 pl-11 sm:pl-0 mt-2 sm:mt-0 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                <button onclick="openEditFoodModal(this.closest('.food-row'))"
                                        class="flex items-center gap-1.5 px-3 h-8 rounded-xl bg-purple-600/5 hover:bg-purple-600/10 transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5 text-purple-600"></i>
                                    <span class="sm:hidden text-purple-600 text-[10px] font-bold font-inter uppercase">Edit</span>
                                </button>
                                <button onclick="openDeleteFoodModal(this.closest('.food-row'))" 
                                        class="flex items-center gap-1.5 px-3 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-500"></i>
                                    <span class="sm:hidden text-rose-500 text-[10px] font-bold font-inter uppercase">Hapus</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        {{-- Tampilan jika database kosong --}}
                        <div class="text-center py-12 text-zinc-400 text-sm font-medium">
                            Belum ada item menu yang terdaftar.
                        </div>
                    @endforelse

                </div>
            </div>
        </div>{{-- end panel-food --}}

    </div>
</div>
@endsection


{{-- ===================== MODALS ===================== --}}
@push('modals')

{{-- Mobile Staff Detail Bottom Sheet --}}
<div id="mobile-detail-overlay"
     class="lg:hidden fixed inset-0 z-40 bg-black/30 backdrop-blur-[2px] hidden opacity-0 transition-opacity duration-300"
     onclick="closeMobileDetail()"></div>

<div id="mobile-detail-sheet"
     class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white rounded-t-[32px] shadow-[0px_-8px_40px_rgba(0,0,0,0.10)] translate-y-full transition-transform duration-300 ease-out max-h-[85vh] overflow-y-auto">

    {{-- Handle --}}
    <div class="flex justify-center pt-3 pb-1">
        <div class="w-10 h-1 bg-zinc-200 rounded-full"></div>
    </div>

    {{-- Avatar Header --}}
    <div class="relative bg-gray-50/20 border-b border-zinc-100 flex flex-col items-center pt-5 pb-5 gap-2">
        <div id="mdc-avatar" class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
            <span id="mdc-initial" class="text-zinc-900 text-lg font-bold font-inter">E</span>
        </div>
        <p id="mdc-name" class="text-zinc-900 text-base font-bold font-inter leading-6">Emily Taylor</p>
        <p id="mdc-role" class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-widest opacity-60">Barista</p>
    </div>

    {{-- Stats --}}
    <div class="px-6 pt-5 pb-4 grid grid-cols-2 gap-3">
        <div class="bg-gray-100/30 rounded-2xl px-4 pt-3 pb-3 text-center">
            <p class="text-gray-500 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide mb-1.5">Terpakai</p>
            <p id="mdc-spent" class="text-purple-600 text-base font-bold font-inter">90.000</p>
        </div>
        <div class="bg-emerald-50/50 rounded-2xl px-4 pt-3 pb-3 text-center">
            <p class="text-gray-500 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide mb-1.5">Sisa</p>
            <p id="mdc-remaining" class="text-emerald-600 text-base font-bold font-inter">210.000</p>
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="px-6 pb-4">
        <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
            <div id="mdc-progress" class="h-full bg-purple-600 rounded-full transition-all duration-500" style="width:30%"></div>
        </div>
        <div class="flex justify-between mt-1.5">
            <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3">0</span>
            <span class="text-gray-400 text-[9px] font-bold font-inter uppercase leading-3">Batas: 300.000</span>
        </div>
    </div>

    {{-- History --}}
    <div class="px-6 pb-8">
        <div class="flex items-center gap-2 mb-3 opacity-70">
            <i data-lucide="clock" class="w-3.5 h-3.5 text-zinc-900"></i>
            <span class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Riwayat Detail</span>
        </div>
        
        {{-- BERI ID 'mdc-history-container' UNTUK TEMPAT INJEKSI LIST TRANSAKSI MOBILE --}}
        <div class="flex flex-col gap-2 max-h-[200px] overflow-y-auto pr-1" id="mdc-history-container">
            </div>
    </div>
</div>


{{-- ADD FOOD MODAL --}}
<div id="add-food-backdrop" data-modal-backdrop data-modal-panel="add-food-panel"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closePanel(this, document.getElementById('add-food-panel'))">
    <div id="add-food-panel" class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl px-7 pt-7 pb-7 flex flex-col gap-5 opacity-0 scale-95 transition-all duration-200">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-zinc-900 text-xl font-bold font-mono leading-7 mb-1">Item Menu Baru</h2>
                <p class="text-gray-400 text-xs font-normal font-mono leading-5">Atur nama dan harga sistem.</p>
            </div>
            <button onclick="closePanel(document.getElementById('add-food-backdrop'), document.getElementById('add-food-panel'))"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 ml-4">
                <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('owner.allowance.store') }}" class="flex flex-col gap-4">
            @csrf
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Nama Menu</label>
                <div class="relative">
                    <i data-lucide="utensils" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="text" name="food_name" placeholder="Contoh: Avocado Toast"
                           class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all" required />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Kategori</label>
                    <div class="relative">
                        <i data-lucide="tag" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <select name="category"
                                class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                            <option value="Makanan Berat">Makanan Berat</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Cemilan">Cemilan</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Harga Modal (Rp)</label>
                    <div class="relative">
                        <i data-lucide="dollar-sign" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="number" name="cost_price" placeholder="0.000"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all text-right" required />
                    </div>
                </div>
            </div>
            <button type="submit"
                    class="w-full h-14 mt-1 bg-purple-600 rounded-2xl flex items-center justify-center gap-2 text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.35)]">
                Tambahkan ke Menu
            </button>
        </form>
    </div>
</div>

{{-- EDIT FOOD MODAL --}}
<div id="edit-food-backdrop" data-modal-backdrop data-modal-panel="edit-food-panel"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closePanel(this, document.getElementById('edit-food-panel'))">
    <div id="edit-food-panel" class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl px-7 pt-7 pb-7 flex flex-col gap-5 opacity-0 scale-95 transition-all duration-200">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-zinc-900 text-xl font-bold font-mono leading-7 mb-1">Edit Item Menu</h2>
                <p class="text-gray-400 text-xs font-normal font-mono leading-5">Atur nama dan harga sistem.</p>
            </div>
            <button onclick="closePanel(document.getElementById('edit-food-backdrop'), document.getElementById('edit-food-panel'))"
                    class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 ml-4">
                <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
            </button>
        </div>
        <form id="form-edit-food" method="POST" action="#" class="flex flex-col gap-4">
            @csrf @method('PUT')
            <div class="flex flex-col gap-1">
                <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Nama Menu</label>
                <div class="relative">
                    <i data-lucide="utensils" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                    <input type="text" id="edit-food-name" name="food_name"
                           class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter focus:outline-2 focus:outline-purple-400 transition-all" required />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Kategori</label>
                    <div class="relative">
                        <i data-lucide="tag" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <select id="edit-food-category" name="category"
                                class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                            <option value="Makanan Berat">Makanan Berat</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Cemilan">Cemilan</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Harga Modal (Rp)</label>
                    <div class="relative">
                        <i data-lucide="dollar-sign" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <input type="number" id="edit-food-price" name="cost_price"
                               class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter focus:outline-2 focus:outline-purple-400 transition-all text-right" required />
                    </div>
                </div>
            </div>
            <button type="submit"
                    class="w-full h-14 mt-1 bg-purple-600 rounded-2xl flex items-center justify-center gap-2 text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.35)]">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

{{-- DELETE FOOD MODAL --}}
<div id="del-food-backdrop" data-modal-backdrop data-modal-panel="del-food-panel"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closePanel(this, document.getElementById('del-food-panel'))">
    <div id="del-food-panel"
         class="relative w-full max-w-xs bg-white rounded-[32px] shadow-2xl px-8 pt-8 pb-8 flex flex-col items-center gap-5 text-center opacity-0 scale-95 transition-all duration-200">

        {{-- Icon --}}
        <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center">
            <i data-lucide="trash-2" class="w-6 h-6 text-rose-500"></i>
        </div>

        {{-- Copy --}}
        <div class="space-y-1">
            <h3 class="text-zinc-900 text-lg font-bold font-mono leading-7">Hapus Item?</h3>
            <p class="text-gray-500 text-sm font-normal font-inter leading-5">
                <span id="del-food-name-display" class="font-bold text-zinc-900"></span> akan dihapus permanen dari menu.
            </p>
        </div>

        {{-- Warning note --}}
        <div class="w-full flex items-start gap-2.5 px-4 py-3 bg-rose-50/60 rounded-2xl outline outline-1 outline-rose-100 text-left">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5"></i>
            <p class="text-rose-700 text-[11px] font-medium font-inter leading-4">
                Item ini tidak akan tersedia lagi untuk pilihan kasbon dan akan dihapus dari semua catatan.
            </p>
        </div>

        {{-- Aksi --}}
        <div class="flex items-center gap-3 w-full">
            <button onclick="closePanel(document.getElementById('del-food-backdrop'), document.getElementById('del-food-panel'))"
                    class="flex-1 h-11 rounded-2xl outline outline-1 outline-zinc-200 text-zinc-700 text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-zinc-50 transition-colors">
                Batal
            </button>
            <form id="form-delete-food" action="" method="POST" class="hidden">
                @csrf
                @method('DELETE') {{-- Wajib untuk proses Delete di Laravel --}}
            </form>
            <button onclick="confirmDeleteFood()"
                    class="flex-1 h-11 bg-rose-500 rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-rose-600 active:scale-[0.98] transition-all shadow-[0px_4px_6px_-4px_rgba(239,68,68,0.30),0px_10px_15px_-3px_rgba(239,68,68,0.20)]">
                Hapus Item
            </button>
        </div>
    </div>
</div>

@endpush


{{-- ===================== SCRIPTS ===================== --}}
@push('scripts')
<script>

    // ---- Fallback modal helper: dipakai jika layout belum menyediakan openPanel/closePanel ----
    if (typeof window.openPanel !== 'function') {
        window.openPanel = function(backdrop, panel) {
            if (!backdrop || !panel) return;
            backdrop.classList.remove('hidden');
            requestAnimationFrame(() => {
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            });
        };
    }

    if (typeof window.closePanel !== 'function') {
        window.closePanel = function(backdrop, panel) {
            if (!backdrop || !panel) return;
            panel.classList.add('opacity-0', 'scale-95');
            panel.classList.remove('opacity-100', 'scale-100');
            setTimeout(() => {
                backdrop.classList.add('hidden');
            }, 200);
        };
    }
    // ---- Tab switching ----
    function switchTab(tab) {
        const isOverview = tab === 'overview';
        const pOverview  = document.getElementById('panel-overview');
        const pFood      = document.getElementById('panel-food');
        pOverview.style.display = isOverview ? 'flex'  : 'none';
        pFood.style.display     = isOverview ? 'none'  : 'flex';

        const tabOverview = document.getElementById('tab-overview');
        const tabFood     = document.getElementById('tab-food');
        const activeClass  = ['bg-white','text-purple-600'];
        const inactiveClass = ['text-gray-500'];

        if (isOverview) {
            tabOverview.classList.add(...activeClass);
            tabOverview.classList.remove(...inactiveClass);
            tabFood.classList.remove(...activeClass);
            tabFood.classList.add(...inactiveClass);
        } else {
            tabFood.classList.add(...activeClass);
            tabFood.classList.remove(...inactiveClass);
            tabOverview.classList.remove(...activeClass);
            tabOverview.classList.add(...inactiveClass);
        }
        lucide.createIcons();
    }

    // ---- Format number with dots (Indonesian style) ----
    function formatRupiah(num) {
        return Number(num).toLocaleString('id-ID');
    }

    // ---- Select Staff (desktop detail card) ----
    function selectStaff(element) {
        // 1. Ekstrak data-attributes dari kartu ranking yang diklik
        const name = element.getAttribute('data-name');
        const role = element.getAttribute('data-role');
        const initial = element.getAttribute('data-initial');
        const spent = element.getAttribute('data-spent');
        const remaining = element.getAttribute('data-remaining');
        const percent = element.getAttribute('data-percent');
        const history = JSON.parse(element.getAttribute('data-history') || '[]');

        // 2. CEK SENSOR LAYAR: JIKA PENGGUNA MEMBUKA LEWAT MOBILE / TABLET (< 1024px)
        if (window.innerWidth < 1024) {
            // Inject data ke elemen Mobile Bottom Sheet
            document.getElementById('mdc-name').textContent = name;
            document.getElementById('mdc-role').textContent = role;
            document.getElementById('mdc-initial').textContent = initial;
            document.getElementById('mdc-spent').textContent = spent;
            document.getElementById('mdc-remaining').textContent = remaining;
            document.getElementById('mdc-progress').style.width = percent + '%';

            // Render riwayat transaksi versi Mobile
            const mobileContainer = document.getElementById('mdc-history-container');
            mobileContainer.innerHTML = '';

            if (history.length === 0) {
                mobileContainer.innerHTML = `<div class="text-center py-4 text-zinc-400 text-[11px] font-inter italic">Belum ada riwayat transaksi yang disetujui.</div>`;
            } else {
                history.forEach(function(item) {
                    mobileContainer.insertAdjacentHTML('beforeend', `
                        <div class="bg-white rounded-2xl outline outline-1 outline-zinc-100 px-4 py-3.5">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-zinc-900 text-xs font-bold font-inter">${item.date}</span>
                                <span class="text-purple-600 text-xs font-bold font-inter">${item.total}</span>
                            </div>
                            <div class="inline-flex items-center px-2 py-1 bg-gray-100 rounded-xl">
                                <span class="text-gray-500 text-[9px] font-normal font-inter leading-3">${item.desc}</span>
                            </div>
                        </div>
                    `);
                });
            }

            // Jalankan fungsi animasi bawaan untuk membuka Bottom Sheet Mobile
            openMobileDetail();

        } else {
            // 3. JIKA PENGGUNA MEMBUKA LEWAT DESKTOP (>= 1024px)
            document.getElementById('detail-empty').classList.add('hidden');
            document.getElementById('detail-card').classList.remove('hidden');

            document.getElementById('dc-name').textContent = name;
            document.getElementById('dc-role').textContent = role;
            document.getElementById('dc-initial').textContent = initial;
            document.getElementById('dc-spent').textContent = spent;
            document.getElementById('dc-remaining').textContent = remaining;
            document.getElementById('dc-progress').style.width = percent + '%';

            // Render riwayat transaksi versi Desktop
            const desktopContainer = document.getElementById('dc-history-container');
            desktopContainer.innerHTML = '';

            if (history.length === 0) {
                desktopContainer.innerHTML = `<div class="text-center py-6 text-zinc-400 text-[11px] font-inter italic">Belum ada riwayat transaksi yang disetujui.</div>`;
            } else {
                history.forEach(function(item) {
                    desktopContainer.insertAdjacentHTML('beforeend', `
                        <div class="bg-white rounded-2xl outline outline-1 outline-zinc-100 px-4 py-3.5">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-zinc-900 text-xs font-bold font-inter leading-4">${item.date}</span>
                                <span class="text-purple-600 text-xs font-bold font-inter leading-4">${item.total}</span>
                            </div>
                            <div class="inline-flex items-center px-2 py-1 bg-gray-100 rounded-xl">
                                <span class="text-gray-500 text-[9px] font-normal font-inter leading-3">${item.desc}</span>
                            </div>
                        </div>
                    `);
                });
            }

            // Efek highlight border ungu pada desktop card yang diklik
            document.querySelectorAll('.staff-rank-card').forEach(card => {
                card.classList.remove('outline-2', 'outline-purple-600', 'shadow-[0px_4px_20px_rgba(147,51,234,0.08)]');
                card.classList.add('outline-1', 'outline-zinc-100');
            });
            element.classList.remove('outline-1', 'outline-zinc-100');
            element.classList.add('outline-2', 'outline-purple-600', 'shadow-[0px_4px_20px_rgba(147,51,234,0.08)]');
        }
    }

    // ---- Mobile bottom sheet ----
    function openMobileDetail() {
        const overlay = document.getElementById('mobile-detail-overlay');
        const sheet = document.getElementById('mobile-detail-sheet');

        overlay.classList.remove('hidden');
        sheet.classList.remove('hidden');

        // Beri sedikit delay agar transisi CSS durasi opacity & translate bekerja mulus
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            sheet.classList.remove('translate-y-full');
        }, 20);
    }

    function closeMobileDetail() {
        const overlay = document.getElementById('mobile-detail-overlay');
        const sheet = document.getElementById('mobile-detail-sheet');

        overlay.classList.add('opacity-0');
        sheet.classList.add('translate-y-full');

        // Sembunyikan element total setelah animasi transisi selesai berjalan (300ms)
        setTimeout(() => {
            overlay.classList.add('hidden');
            sheet.classList.add('hidden');
        }, 300);
    }


    // ---- Manajemen Menu Modals ----
    function openAddFoodModal() {
        openPanel(document.getElementById('add-food-backdrop'), document.getElementById('add-food-panel'));
    }

    function openEditFoodModal(row) {
        // Ambil data-attributes menggunakan dataset (lebih rapi & sinkron dengan Blade)
        const id = row.dataset.id;
        const name = row.dataset.name;
        const category = row.dataset.category; 
        const price = row.dataset.price;

        // Masukkan data ke element input form di dalam modal
        document.getElementById('edit-food-name').value     = name;
        document.getElementById('edit-food-category').value = category;
        document.getElementById('edit-food-price').value    = price;
        
        // SESUAIKAN ACTION FORM: Arahkan action form edit ke route owner.allowance.update
        const updateUrl = @json(route('owner.allowance.update', ['id' => '__ID__']));
        document.getElementById('form-edit-food').action = updateUrl.replace('__ID__', encodeURIComponent(id));

        // Buka panel modal edit
        openPanel(document.getElementById('edit-food-backdrop'), document.getElementById('edit-food-panel'));
    }

    // ---- Delete Food Modal ----
    let pendingDeleteRow = null;

    function openDeleteFoodModal(row) {
        pendingDeleteRow = row;
        const id = row.dataset.id;
        
        document.getElementById('del-food-name-display').textContent = row.dataset.name;
        
        // SESUAIKAN ACTION FORM: Arahkan action form delete ke route owner.allowance.destroy
        const deleteUrl = @json(route('owner.allowance.destroy', ['id' => '__ID__']));
        document.getElementById('form-delete-food').action = deleteUrl.replace('__ID__', encodeURIComponent(id));
        
        openPanel(document.getElementById('del-food-backdrop'), document.getElementById('del-food-panel'));
    }

    // Fungsi confirm ini tidak perlu menghapus row via JS lagi, 
    // karena HTML form akan langsung melakukan submit (reload halaman) ke Backend Laravel
    function confirmDeleteFood() {
        if (!pendingDeleteRow) return;
        
        // Submit form hapus secara programmatic
        document.getElementById('form-delete-food').submit();
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMobileDetail(); });
</script>
@endpush