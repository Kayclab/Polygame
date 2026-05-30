@extends('layouts.app')

@section('title', 'Manajemen Lembur - Poly Games Cafe')

@section('content')
<div class="px-6 sm:px-10 lg:px-14 py-8 flex flex-col gap-10">

    {{-- ===================== PAGE HEADER ===================== --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="calendar-days" class="w-4 h-4 text-purple-600"></i>
                <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Audit Kehadiran</span>
            </div>
            <h1 class="text-zinc-900 text-2xl sm:text-3xl font-bold font-mono leading-9 mb-1">Manajemen Lembur</h1>
            <p class="text-gray-500 text-sm font-normal font-mono leading-6 opacity-80">
                Pemantauan lembur staf berbasis kalender. Pilih tanggal untuk meninjau dan menindaklanjuti pengajuan.
            </p>
        </div>

        {{-- Tanggal Dipilih Badge --}}
        <div class="flex-shrink-0 flex items-center gap-4 h-16 px-6 bg-white rounded-2xl outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
            <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide opacity-60 whitespace-nowrap">Tanggal Dipilih</span>
            <span id="selected-date-display" class="text-purple-600 text-lg font-bold font-inter leading-7">-</span>
        </div>
    </div>

    {{-- ===================== MAIN CONTENT: Calendar + Submissions ===================== --}}
    <div class="flex flex-col lg:flex-row gap-6 items-start">

        {{-- ---- LEFT: Calendar ---- --}}
        <div class="w-full lg:w-auto lg:flex-shrink-0 bg-white rounded-[40px] outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] px-8 pt-8 pb-0 flex flex-col gap-8 overflow-hidden">

            {{-- Calendar Header --}}
            <div class="flex items-center justify-between">
                <span id="cal-month-label" class="text-zinc-900 text-base font-black font-inter uppercase leading-6 tracking-widest opacity-70">Bulan Tahun</span>
                <div class="flex items-center gap-2">
                    <button onclick="changeMonth(-1)"
                            class="w-8 h-8 flex items-center justify-center rounded-2xl outline outline-1 outline-zinc-100 hover:bg-zinc-50 transition-colors">
                        <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-zinc-900"></i>
                    </button>
                    <button onclick="changeMonth(1)"
                            class="w-8 h-8 flex items-center justify-center rounded-2xl outline outline-1 outline-zinc-100 hover:bg-zinc-50 transition-colors">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-zinc-900"></i>
                    </button>
                </div>
            </div>

            {{-- Day Headers + Calendar Grid --}}
            <div class="w-full" style="min-width:300px; max-width:420px">
                <div class="grid grid-cols-7 mb-1">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $day)
                    <div class="flex justify-center py-2">
                        <span class="text-gray-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wide opacity-50">{{ $day }}</span>
                    </div>
                    @endforeach
                </div>
                <div id="cal-grid" class="grid grid-cols-7 gap-1"></div>
            </div>

            {{-- Legend --}}
            <div class="border-t border-zinc-100 py-4 flex flex-wrap items-center gap-x-5 gap-y-2">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-yellow-500 rounded-sm flex-shrink-0"></div>
                    <span class="text-gray-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wide">Menunggu Tindakan</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-emerald-500 rounded-sm flex-shrink-0"></div>
                    <span class="text-gray-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wide">Sudah Selesai</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-purple-600 rounded-sm flex-shrink-0"></div>
                    <span class="text-gray-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wide">Pilihan Aktif</span>
                </div>
            </div>
        </div>

        {{-- ---- RIGHT: Submissions Panel ---- --}}
        <div class="flex-1 min-w-0 flex flex-col gap-6">

            {{-- Panel Header --}}
            <div class="flex items-center justify-between h-11 border-b border-zinc-100">
                <div class="flex items-center gap-2 opacity-70">
                    <i data-lucide="file-text" class="w-4 h-4 text-zinc-900"></i>
                    <span id="submissions-label" class="text-zinc-900 text-sm font-black font-inter uppercase leading-5 tracking-wider">Pilih Tanggal</span>
                </div>
                <span id="submissions-total" class="text-zinc-900 text-[10px] font-black font-inter uppercase leading-4 tracking-wide opacity-40">Total: 0</span>
            </div>

            {{-- Empty State --}}
            <div id="submissions-empty"
                 class="flex-1 min-h-64 bg-gray-50/20 rounded-[48px] outline outline-2 outline-zinc-100 flex flex-col items-center justify-center gap-4 py-16">
                <div class="w-16 h-16 bg-white rounded-full shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-zinc-100 flex items-center justify-center">
                    <i data-lucide="file-x" class="w-8 h-8 text-gray-400 opacity-30"></i>
                </div>
                <p class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider opacity-40">Tidak Ada Pengajuan pada Tanggal Ini</p>
            </div>

            {{-- Submission Cards (rendered by JS) --}}
            <div id="submissions-list" class="hidden flex flex-col gap-4"></div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===================== STATE =====================
    const SUBMISSIONS = JSON.parse('{!! json_encode($submissionsStatus ?? []) !!}');
    const rawData = JSON.parse('{!! json_encode($allLemburs ?? []) !!}');
    
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedDate = new Date().toISOString().split('T')[0];

    const DATA_LEMBUR = {};
    rawData.forEach(item => {
        const namaKaryawan = item.karyawan ? item.karyawan.n_kry : 'Staf';
        const jabatanKaryawan = item.karyawan ? item.karyawan.jab : '-';
        
        if (!DATA_LEMBUR[item.tgl_lbr]) DATA_LEMBUR[item.tgl_lbr] = [];
        
        DATA_LEMBUR[item.tgl_lbr].push({
            id: item.id_lbr,
            name: namaKaryawan,
            role: jabatanKaryawan,
            duration: item.qty_jam + ' jam',
            status: item.sts_lbr,
            reason: item.keterangan,
            image: item.bukti_foto ? `/storage/${item.bukti_foto}` : null
        });
    });

    // ===================== CALENDAR =====================
    function renderCalendar() {
        const monthNames = ['Januari','Februari','Maret','April','Mei','Juni',
                            'Juli','Agustus','September','Oktober','November','Desember'];
        document.getElementById('cal-month-label').textContent = monthNames[currentMonth] + ' ' + currentYear;

        const grid = document.getElementById('cal-grid');
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) grid.innerHTML += '<div></div>';

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const isSelected = dateStr === selectedDate;
            const dotType = SUBMISSIONS[dateStr] ? SUBMISSIONS[dateStr].toLowerCase() : null;

            let cellBg = isSelected ? 'bg-purple-600 shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.20)]' : 'bg-gray-100/50 hover:bg-purple-600/5';
            let textColor = isSelected ? 'text-white' : 'text-zinc-900/70';

            let dotHtml = '';
            if (!isSelected && dotType) {
                let dotColor = '';
                
                if (dotType === 'menunggu' || dotType === 'pending') {
                    dotColor = 'bg-yellow-500'; // Oranye/Kuning
                } else if (dotType === 'ditolak') {
                    dotColor = 'bg-rose-500';   // Merah
                } else {
                    dotColor = 'bg-emerald-500'; // Default hijau jika resolved
                }

                dotHtml = `<div class="w-1.5 h-1.5 ${dotColor} rounded-full mt-0.5"></div>`;
            }

            grid.innerHTML += `
                <div class="flex justify-center py-0.5">
                    <button onclick="selectDate('${dateStr}')"
                            class="w-11 h-11 ${cellBg} rounded-2xl flex flex-col items-center justify-center transition-all duration-150 cursor-pointer">
                        <span class="${textColor} text-xs font-black font-inter leading-4">${d}</span>
                        ${dotHtml}
                    </button>
                </div>`;
        }
        lucide.createIcons();
    }

    function changeMonth(dir) {
        currentMonth += dir;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        else if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        renderCalendar();
    }

    function escapeAttribute(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // ===================== SUBMISSIONS =====================
    function selectDate(dateStr) {
        selectedDate = dateStr;
        renderCalendar();

        document.getElementById('selected-date-display').textContent = dateStr;
        document.getElementById('submissions-label').textContent = 'Pengajuan untuk ' + dateStr;

        const listContainer = document.getElementById('submissions-list');
        const emptyState = document.getElementById('submissions-empty');
        const totalLabel = document.getElementById('submissions-total');

        const items = DATA_LEMBUR[dateStr] || [];

        if (items.length > 0) {
            emptyState.classList.add('hidden');
            listContainer.classList.remove('hidden');
            listContainer.style.display = 'flex';
            totalLabel.textContent = 'Total: ' + items.length;

            listContainer.innerHTML = items.map(item => {
                // 1. Tentukan status dalam huruf kecil agar pengecekan akurat
                const status = item.status.toLowerCase();
                
                // 2. Deklarasi variabel style
                let badgeClass = '';
                let dotClass = '';
                let textClass = '';
                let isPending = false;

                // 3. Logika penentuan warna berdasarkan 3 kondisi
                if (status === 'menunggu' || status === 'pending') {
                    isPending = true;
                    badgeClass = 'bg-amber-50 outline-amber-100';
                    dotClass = 'bg-amber-500';
                    textClass = 'text-amber-600';
                } else if (status === 'ditolak') {
                    badgeClass = 'bg-rose-50 outline-rose-100';
                    dotClass = 'bg-rose-500';
                    textClass = 'text-rose-500';
                } else {
                    // Default untuk 'disetujui' atau 'resolved'
                    badgeClass = 'bg-emerald-50 outline-emerald-100';
                    dotClass = 'bg-emerald-500';
                    textClass = 'text-emerald-600';
                }

                return `
                    <div class="submission-card bg-white rounded-3xl outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] overflow-hidden">
                        <div class="flex flex-col sm:flex-row gap-0">
                            <div class="w-full sm:w-32 h-32 sm:h-auto bg-gray-100 flex-shrink-0 flex items-center justify-center relative overflow-hidden">
                                ${item.image 
                                    ? `<button type="button" data-proof-image="${escapeAttribute(item.image)}" class="group w-full h-full relative cursor-zoom-in focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2" title="Klik untuk melihat gambar penuh">
                                            <img src="${item.image}" class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-105" alt="Bukti overtime">
                                            <div class="absolute inset-0 bg-zinc-900/0 group-hover:bg-zinc-900/25 transition-colors duration-200 flex items-center justify-center">
                                                <div class="w-9 h-9 rounded-full bg-white/90 shadow-sm hidden group-hover:flex items-center justify-center">
                                                    <i data-lucide="zoom-in" class="w-4 h-4 text-zinc-900"></i>
                                                </div>
                                            </div>
                                       </button>` 
                                    : `<div class="w-16 h-16 bg-gray-200 rounded-2xl flex items-center justify-center"><i data-lucide="user" class="w-8 h-8 text-gray-400"></i></div>`
                                }
                            </div>
                            <div class="flex-1 px-6 py-5 flex flex-col gap-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-zinc-900 text-base font-bold font-inter leading-6">${item.name}</p>
                                        <p class="text-gray-400 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">${item.role} • ${item.duration}</p>
                                    </div>
                                    <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 ${badgeClass} rounded-full outline outline-1">
                                        <div class="w-1.5 h-1.5 ${dotClass} rounded-full"></div>
                                        <span class="${textClass} text-[9px] font-black font-inter uppercase leading-3 tracking-wide">${item.status}</span>
                                    </span>
                                </div>
                                <p class="text-gray-500 text-sm font-normal font-inter leading-5 italic">"${item.reason}"</p>
                                ${isPending ? `
                                    <div class="flex items-center gap-3 pt-1">
                                        <button onclick="updateStatus(${item.id}, 'disetujui')" class="flex items-center gap-2 px-5 h-10 bg-purple-600 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest hover:bg-purple-700 transition-all">Setujui</button>
                                        <button onclick="updateStatus(${item.id}, 'ditolak')" class="flex items-center gap-2 px-4 h-10 rounded-2xl text-rose-500 text-[10px] font-black uppercase tracking-widest hover:bg-rose-50 transition-colors">Tolak</button>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            lucide.createIcons();
        } else {
            emptyState.classList.remove('hidden');
            listContainer.classList.add('hidden');
            listContainer.style.display = 'none';
            totalLabel.textContent = 'Total: 0';
        }
    }

    // ===================== IMAGE PREVIEW MODAL =====================
    function ensureProofImageModal() {
        if (document.getElementById('proof-image-modal')) return;

        const modal = document.createElement('div');
        modal.id = 'proof-image-modal';
        modal.className = 'fixed inset-0 z-[10000] hidden items-center justify-center px-4 py-6';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-label', 'Preview gambar bukti overtime');

        modal.innerHTML = `
            <div data-proof-backdrop class="absolute inset-0 bg-zinc-950/70 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>
            <div data-proof-panel class="relative w-full max-w-5xl max-h-[90vh] bg-white rounded-[32px] outline outline-1 outline-white/20 shadow-[0px_24px_60px_-18px_rgba(0,0,0,0.45)] opacity-0 scale-95 transition-all duration-200 overflow-hidden">
                <div class="h-14 px-5 sm:px-6 border-b border-zinc-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-2 min-w-0">
                        <i data-lucide="image" class="w-4 h-4 text-purple-600 flex-shrink-0"></i>
                        <span class="text-zinc-900 text-xs font-black font-inter uppercase leading-4 tracking-widest truncate">Bukti Overtime</span>
                    </div>
                    <button type="button" data-proof-close class="w-9 h-9 rounded-2xl hover:bg-zinc-100 flex items-center justify-center transition-colors" aria-label="Tutup preview gambar">
                        <i data-lucide="x" class="w-4 h-4 text-zinc-900"></i>
                    </button>
                </div>
                <div class="bg-zinc-950/95 flex items-center justify-center p-3 sm:p-5 max-h-[calc(90vh-56px)] overflow-auto">
                    <img id="proof-image-full" src="" alt="Bukti overtime ukuran penuh" class="max-w-full max-h-[calc(90vh-96px)] object-contain rounded-2xl">
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        modal.querySelector('[data-proof-backdrop]').addEventListener('click', closeProofImageModal);
        modal.querySelector('[data-proof-close]').addEventListener('click', closeProofImageModal);

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeProofImageModal();
        });

        if (window.lucide) lucide.createIcons();
    }

    function openProofImageModal(imageUrl) {
        ensureProofImageModal();

        const modal = document.getElementById('proof-image-modal');
        const backdrop = modal.querySelector('[data-proof-backdrop]');
        const panel = modal.querySelector('[data-proof-panel]');
        const image = document.getElementById('proof-image-full');

        image.src = imageUrl;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        });
    }

    function closeProofImageModal() {
        const modal = document.getElementById('proof-image-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        const backdrop = modal.querySelector('[data-proof-backdrop]');
        const panel = modal.querySelector('[data-proof-panel]');
        const image = document.getElementById('proof-image-full');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            image.src = '';
            document.body.classList.remove('overflow-hidden');
        }, 180);
    }

    function bindProofImagePreview() {
        const listContainer = document.getElementById('submissions-list');
        if (!listContainer) return;

        listContainer.addEventListener('click', event => {
            const trigger = event.target.closest('[data-proof-image]');
            if (!trigger) return;

            openProofImageModal(trigger.getAttribute('data-proof-image'));
        });
    }

    // ===================== MODAL HELPERS =====================
    let appModalResolver = null;
    let appModalLastFocused = null;

    function ensureAppModal() {
        if (document.getElementById('app-status-modal')) return;

        const modal = document.createElement('div');
        modal.id = 'app-status-modal';
        modal.className = 'fixed inset-0 z-[9999] hidden items-center justify-center px-4';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'app-modal-title');
        modal.setAttribute('aria-describedby', 'app-modal-message');

        modal.innerHTML = `
            <div data-modal-backdrop class="absolute inset-0 bg-zinc-900/30 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>
            <div data-modal-panel class="relative w-full max-w-md bg-white rounded-[32px] outline outline-1 outline-zinc-100 shadow-[0px_20px_40px_-12px_rgba(0,0,0,0.18)] opacity-0 translate-y-3 scale-95 transition-all duration-200 overflow-hidden">
                <div class="px-7 pt-7 pb-5 flex flex-col gap-4">
                    <div class="flex items-start gap-4">
                        <div id="app-modal-icon-wrap" class="w-12 h-12 rounded-2xl bg-purple-50 outline outline-1 outline-purple-100 flex items-center justify-center flex-shrink-0">
                            <i id="app-modal-icon" data-lucide="info" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <div class="min-w-0">
                            <p id="app-modal-title" class="text-zinc-900 text-lg font-bold font-inter leading-7">Konfirmasi</p>
                            <p id="app-modal-message" class="text-gray-500 text-sm font-normal font-inter leading-6 mt-1"></p>
                        </div>
                    </div>
                </div>
                <div id="app-modal-actions" class="px-7 py-5 bg-gray-50/40 border-t border-zinc-100 flex items-center justify-end gap-3"></div>
            </div>
        `;

        document.body.appendChild(modal);

        modal.querySelector('[data-modal-backdrop]').addEventListener('click', () => {
            resolveAppModal(false);
        });

        document.addEventListener('keydown', handleAppModalKeydown);
    }

    function handleAppModalKeydown(event) {
        const modal = document.getElementById('app-status-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        if (event.key === 'Escape') {
            event.preventDefault();
            resolveAppModal(false);
        }
    }

    function setAppModalType(type) {
        const iconWrap = document.getElementById('app-modal-icon-wrap');
        const icon = document.getElementById('app-modal-icon');

        const types = {
            info: {
                wrap: 'w-12 h-12 rounded-2xl bg-purple-50 outline outline-1 outline-purple-100 flex items-center justify-center flex-shrink-0',
                icon: 'info',
                iconClass: 'w-6 h-6 text-purple-600'
            },
            success: {
                wrap: 'w-12 h-12 rounded-2xl bg-emerald-50 outline outline-1 outline-emerald-100 flex items-center justify-center flex-shrink-0',
                icon: 'check-circle',
                iconClass: 'w-6 h-6 text-emerald-600'
            },
            danger: {
                wrap: 'w-12 h-12 rounded-2xl bg-rose-50 outline outline-1 outline-rose-100 flex items-center justify-center flex-shrink-0',
                icon: 'alert-triangle',
                iconClass: 'w-6 h-6 text-rose-500'
            }
        };

        const selected = types[type] || types.info;
        iconWrap.className = selected.wrap;
        icon.setAttribute('data-lucide', selected.icon);
        icon.className = selected.iconClass;

        if (window.lucide) lucide.createIcons();
    }

    function openAppModal({ type = 'info', title = 'Pemberitahuan', message = '', confirmText = 'OK', cancelText = null } = {}) {
        ensureAppModal();

        const modal = document.getElementById('app-status-modal');
        const backdrop = modal.querySelector('[data-modal-backdrop]');
        const panel = modal.querySelector('[data-modal-panel]');
        const actions = document.getElementById('app-modal-actions');

        appModalLastFocused = document.activeElement;
        setAppModalType(type);

        document.getElementById('app-modal-title').textContent = title;
        document.getElementById('app-modal-message').textContent = message;

        const confirmButtonClass = type === 'danger'
            ? 'px-5 h-10 bg-rose-500 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all'
            : 'px-5 h-10 bg-purple-600 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest hover:bg-purple-700 transition-all';

        actions.innerHTML = `
            ${cancelText ? `<button type="button" data-modal-cancel class="px-4 h-10 rounded-2xl text-zinc-900/60 text-[10px] font-black uppercase tracking-widest hover:bg-zinc-100 transition-colors">${cancelText}</button>` : ''}
            <button type="button" data-modal-confirm class="${confirmButtonClass}">${confirmText}</button>
        `;

        return new Promise(resolve => {
            appModalResolver = resolve;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                panel.classList.remove('opacity-0', 'translate-y-3', 'scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            });

            const confirmButton = actions.querySelector('[data-modal-confirm]');
            const cancelButton = actions.querySelector('[data-modal-cancel]');

            confirmButton.addEventListener('click', () => resolveAppModal(true), { once: true });
            if (cancelButton) cancelButton.addEventListener('click', () => resolveAppModal(false), { once: true });

            confirmButton.focus();
        });
    }

    function resolveAppModal(value) {
        const modal = document.getElementById('app-status-modal');
        if (!modal || modal.classList.contains('hidden')) return;

        const backdrop = modal.querySelector('[data-modal-backdrop]');
        const panel = modal.querySelector('[data-modal-panel]');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
        panel.classList.add('opacity-0', 'translate-y-3', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');

            if (appModalLastFocused && typeof appModalLastFocused.focus === 'function') {
                appModalLastFocused.focus();
            }

            if (typeof appModalResolver === 'function') {
                const resolver = appModalResolver;
                appModalResolver = null;
                resolver(value);
            }
        }, 180);
    }

    // ===================== ACTIONS =====================
    async function updateStatus(id, status) {
        const isReject = status === 'ditolak';
        const confirmed = await openAppModal({
            type: isReject ? 'danger' : 'info',
            title: 'Konfirmasi Perubahan Status',
            message: `Apakah Anda yakin ingin mengubah pengajuan lembur ini menjadi ${status}?`,
            confirmText: isReject ? 'Tolak' : 'Setujui',
            cancelText: 'Batal'
        });

        if (!confirmed) return;

        try {
            // Pastikan URL di sini tidak dobel /owner/owner
            const response = await fetch(`/owner/lembur/update-status/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            });

            let data = {};
            try {
                data = await response.json();
            } catch (error) {
                data = {};
            }

            if (response.ok) {
                await openAppModal({
                    type: 'success',
                    title: 'Berhasil',
                    message: data.message || 'Status pengajuan lembur berhasil diperbarui.',
                    confirmText: 'OK'
                });
                location.reload();
            } else {
                await openAppModal({
                    type: 'danger',
                    title: 'Gagal',
                    message: data.message || 'Rute tidak ditemukan.',
                    confirmText: 'OK'
                });
            }
        } catch (error) {
            await openAppModal({
                type: 'danger',
                title: 'Kesalahan Jaringan',
                message: 'Terjadi kesalahan jaringan. Silakan periksa koneksi lalu coba lagi.',
                confirmText: 'OK'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        bindProofImagePreview();
        renderCalendar();
        selectDate(selectedDate);
    });
</script>
@endpush