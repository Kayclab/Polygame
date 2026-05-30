@extends('layouts.app')
@section('title', 'Permintaan Lembur')
@section('content')

    @if(session('success'))
        <div id="success-alert" style="margin-bottom:20px; background:#ecfdf5; border:1px solid #86efac; color:#065f46; padding:16px 18px; border-radius:16px; display:flex; align-items:center; gap:10px; font-weight:600; box-shadow:0 4px 12px rgba(0,0,0,.04);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17L4 12" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>{{ session('success') }}</div>
        <script>
            setTimeout(() => { const box = document.getElementById('success-alert'); if (box) { box.style.transition = 'all .4s ease'; box.style.opacity = '0'; setTimeout(() => box.remove(), 400); } }, 3000);
        </script>
    @endif

    <div class="px-4 sm:px-6 lg:px-8 py-7 w-full">
        {{-- ===== PAGE HEADER ===== --}}
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5 mb-8">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.33" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6.5" /><path d="M8 4.5v3.8l2.5 2.5" /></svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-purple-600">Portal Lembur</span>
                </div>
                <h1 class="font-mono font-bold text-2xl sm:text-3xl text-zinc-900 leading-tight">Pengajuan Lembur</h1>
                <p class="font-mono text-sm text-gray-400">Ajukan jam lembur dengan menyertakan detail pekerjaan.</p>
            </div>

            {{-- Tombol buka modal - onclick langsung, tidak butuh Alpine --}}
            <button type="button" onclick="OT.open()" class="flex items-center justify-center gap-2.5 px-6 h-12 bg-purple-600 rounded-2xl text-white text-xs font-black uppercase tracking-widest shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.25)] hover:bg-purple-700 active:scale-95 transition-all duration-150 shrink-0 self-start whitespace-nowrap">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 20 20"><path d="M10 4v12M4 10h12" stroke-linecap="round" /></svg>Ajukan Lembur
            </button>
        </div>

        {{-- ===== MAIN GRID ===== --}}
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            {{-- LEFT: Log Pengajuan --}}
            <div class="flex-1 min-w-0 bg-white rounded-[28px] border border-zinc-100 shadow-sm overflow-hidden">
                <div class="px-5 sm:px-8 h-16 border-b border-zinc-100 flex items-center justify-between gap-4">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Riwayat Pengajuan Lembur</span>

                    {{-- Filter Dropdown --}}
                    <div class="relative" id="dd-wrapper">
                        <button type="button" onclick="OT.toggleDd(event)" class="flex items-center gap-2 px-4 py-1.5 bg-white rounded-2xl border border-zinc-100 hover:border-purple-300 hover:bg-purple-50/30 transition-all duration-150">
                            <svg class="w-3.5 h-3.5 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 14 14"><circle cx="6" cy="6" r="4.5" /><path d="M10.5 10.5l2 2" /></svg>
                            <span id="dd-label" class="text-[10px] font-black uppercase tracking-widest text-purple-600 whitespace-nowrap">Aktivitas Terbaru</span>
                            <svg id="dd-chevron" class="w-3 h-3 text-purple-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12"><path d="M3 4.5l3 3 3-3" /></svg>
                        </button>

                        <div id="dd-panel" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl border border-zinc-100 shadow-[0px_10px_25px_-5px_rgba(0,0,0,0.12)] z-30" style="display:none;">
                            <div class="p-2 space-y-0.5">
                                <button type="button" onclick="OT.filter('all','Semua Pengajuan')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-zinc-700 text-left hover:bg-purple-50 hover:text-purple-700 transition-colors"><span class="w-2 h-2 rounded-full bg-zinc-300 shrink-0"></span>Semua Pengajuan</button>
                                <button type="button" onclick="OT.filter('pending','Menunggu')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-zinc-700 text-left hover:bg-purple-50 hover:text-purple-700 transition-colors"><span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>Menunggu</button>
                                <button type="button" onclick="OT.filter('approved','Disetujui')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-zinc-700 text-left hover:bg-purple-50 hover:text-purple-700 transition-colors"><span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>Disetujui</button>
                                <button type="button" onclick="OT.filter('rejected','Ditolak')" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-zinc-700 text-left hover:bg-purple-50 hover:text-purple-700 transition-colors"><span class="w-2 h-2 rounded-full bg-red-400 shrink-0"></span>Ditolak</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full" style="min-width:520px">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-gray-50/10">
                                <th class="px-5 sm:px-8 py-5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 w-44">Tanggal Kerja</th>
                                <th class="px-4 py-5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 w-28">Durasi</th>
                                <th class="px-4 py-5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">Deskripsi</th>
                                <th class="px-5 sm:px-8 py-5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400 w-24">Status</th>
                            </tr>
                        </thead>
                        <tbody id="overtime-table-body" class="divide-y divide-zinc-100/60">
                            @forelse ($overtimes as $ot)
                                @php $statusMap = ['menunggu' => 'pending', 'disetujui' => 'approved', 'ditolak' => 'rejected']; $status = $statusMap[$ot->sts_lbr] ?? 'pending'; @endphp
                                <tr class="ot-row hover:bg-purple-50/20 transition-colors duration-150" data-status="{{ $status }}">
                                    <td class="px-5 sm:px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-purple-600/5 rounded-xl flex items-center justify-center shrink-0">
                                                <svg class="w-4.5 h-4.5 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.67" viewBox="0 0 20 20"><rect x="2.5" y="3.33" width="15" height="13.33" rx="1.5" /><path d="M6.67 1.67v3.33M13.33 1.67v3.33" /><line x1="2.5" y1="8.33" x2="17.5" y2="8.33" /></svg>
                                            </div>
                                            <span class="text-sm font-bold text-zinc-900 whitespace-nowrap">{{ $ot->tgl_lbr }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-5"><span class="text-sm font-black text-zinc-900">{{ $ot->qty_jam }}h</span></td>
                                    <td class="px-4 py-5"><span class="text-xs text-gray-400 font-medium leading-relaxed">{{ $ot->keterangan }}</span></td>
                                    <td class="px-5 sm:px-8 py-5 text-right">
                                        @if ($ot->sts_lbr === 'disetujui')
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100 whitespace-nowrap">Disetujui</span>
                                        @elseif ($ot->sts_lbr === 'menunggu')
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100 whitespace-nowrap">Menunggu</span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-500 border border-red-100 whitespace-nowrap">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 bg-purple-600/5 rounded-2xl flex items-center justify-center"><svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg></div>
                                            <p class="text-sm font-bold text-gray-300">No overtime submissions yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div id="filter-empty-state" class="hidden px-8 py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 bg-purple-600/5 rounded-2xl flex items-center justify-center"><svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg></div>
                            <p class="text-sm font-bold text-gray-300">No submissions match this filter.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT Sidebar --}}
            <div class="flex flex-col gap-5 w-full lg:w-80 shrink-0">
                <div class="relative bg-purple-600 rounded-[28px] p-8 overflow-hidden shadow-[0px_20px_40px_-10px_rgba(147,51,234,0.30)]">
                    <div class="absolute -top-8 -right-8 w-36 h-36 bg-white/5 rounded-full pointer-events-none"></div>
                    <div class="absolute -bottom-10 -left-6 w-44 h-44 bg-white/5 rounded-full pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center mb-8"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17" /><polyline points="16 7 22 7 22 13" /></svg></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/60 mb-1">Lembur Bulan Ini</p>
                        <div class="flex items-baseline gap-2 mb-5">
                            <span class="text-5xl font-black text-white leading-none">{{ $thisMonthOT ?? '12.5' }}</span>
                            <span class="text-lg font-black text-white/40">Jam</span>
                        </div>
                        <p class="text-xs font-medium text-white/60 leading-5 italic">"Lembur yang disetujui akan otomatis diakumulasikan ke dalam slip gaji berikutnya."</p>
                    </div>
                </div>

                <div class="bg-white rounded-[28px] border border-zinc-100 shadow-sm px-7 pt-7 pb-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-5">Aturan Pengajuan</p>
                    <ul class="space-y-4">
                        @foreach (['Sertakan foto pekerjaan yang jelas', 'Jelaskan alasan lembur', 'Hanya lembur yang diterima yang akan terhitung di jumlah jam lembur'] as $rule)
                            <li class="flex items-center gap-3">
                                <div class="w-5 h-5 bg-emerald-50 rounded-full flex items-center justify-center shrink-0"><svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 12 12"><path d="M2 6.5l2.5 2.5 5.5-5" /></svg></div>
                                <span class="text-xs font-bold text-zinc-700">{{ $rule }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
                        MODAL
    ══════════════════════════════════════════ --}}
    <div id="ot-modal-overlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; background:rgba(0,0,0,0.5); backdrop-filter:blur(6px);" onclick="if(event.target===this) OT.close()">
        <div id="ot-modal-panel" style="position:absolute; bottom:0; left:0; right:0; background:#ffffff; border-radius:32px 32px 0 0; max-height:92vh; overflow-y:auto; transform:translateY(30px); opacity:0; transition:transform 0.28s cubic-bezier(.22,1,.36,1), opacity 0.2s ease;">
            {{-- Header --}}
            <div class="px-6 sm:px-10 pt-7 sm:pt-8 pb-0">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1"><svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.33" viewBox="0 0 16 16"><circle cx="8" cy="8" r="6.5" /><path d="M8 4.5v3.8l2.5 2.5" /></svg><span class="text-[10px] font-black uppercase tracking-widest text-purple-600">Pengajuan Baru</span></div>
                        <h2 class="font-mono font-bold text-2xl sm:text-3xl text-zinc-900 leading-tight">Ajukan Lembur</h2>
                        <p class="font-mono text-sm text-gray-400 mt-1">Harap berikan detail yang akurat untuk ditinjau oleh admin.</p>
                    </div>
                    <button type="button" onclick="OT.close()" aria-label="Close" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors shrink-0 mt-1"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" /></svg></button>
                </div>
            </div>

            {{-- Form --}}
            <form id="overtime-form" class="px-6 sm:px-10 pt-7 pb-8" onsubmit="OT.submit(event)">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-7 gap-y-5">
                    {{-- LEFT --}}
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 pl-1">Tanggal Kerja</label>
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"><svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.33" viewBox="0 0 16 16"><rect x="2" y="2.67" width="12" height="11.33" rx="1.5" /><path d="M5.33 1.33v2.67M10.67 1.33v2.67" /><line x1="2" y1="6.67" x2="14" y2="6.67" /></svg></div>
                                <input type="date" name="work_date" id="ot-work-date" required class="w-full py-3.5 pl-10 pr-4 bg-gray-50 border border-zinc-100 rounded-2xl text-sm font-medium text-zinc-900 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all duration-150">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 pl-1">Durasi (Jam)</label>
                            <input type="number" name="duration" id="ot-duration" step="0.5" min="0.5" max="24" placeholder="mis. 2.5" required class="w-full py-3.5 px-5 bg-gray-50 border border-zinc-100 rounded-2xl text-sm font-black text-zinc-900 placeholder:text-zinc-300 focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all duration-150">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 pl-1">Alasan Kerja</label>
                            <textarea name="reason" id="ot-reason" rows="5" placeholder="Jelaskan secara singkat mengapa Anda bekerja lembur..." required class="w-full px-5 py-4 bg-gray-50 border border-zinc-100 rounded-2xl text-sm font-medium text-zinc-900 placeholder:text-zinc-300 resize-none leading-relaxed focus:outline-none focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all duration-150"></textarea>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="flex flex-col gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 pl-1">Bukti Kerja (Foto)</label>
                            <label for="proof-upload" class="group flex flex-col items-center justify-center gap-4 p-6 bg-gray-50 border-2 border-dashed border-zinc-200 rounded-3xl cursor-pointer hover:border-purple-400 hover:bg-purple-50/20 transition-all duration-200 min-h-49">
                                <div class="w-14 h-14 bg-white rounded-[18px] shadow-sm flex items-center justify-center group-hover:shadow-md transition-shadow duration-200"><svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 28 28"><rect x="2.33" y="5.83" width="23.33" height="18.33" rx="2.5" /><circle cx="14" cy="14.5" r="4" /><path d="M9.33 5.83l1.67-3.5h6l1.67 3.5" /><circle cx="22" cy="8.5" r="1.2" fill="currentColor" stroke="none" /></svg></div>
                                <div class="text-center pointer-events-none">
                                    <p id="upload-name" class="text-sm font-black text-zinc-900">Unggah Bukti</p>
                                    <p id="upload-hint" class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-0.5">JPG, PNG &bull; Maks 5MB</p>
                                </div>
                                <input id="proof-upload" type="file" name="proof" accept="image/jpeg,image/png" class="hidden" onchange="OT.fileChange(this)">
                            </label>
                        </div>
                        <div class="flex items-start gap-3 px-5 py-4 bg-purple-600/5 rounded-2xl border border-purple-600/10">
                            <svg class="w-5 h-5 text-purple-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.67" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8.33" /><path d="M10 6.67v.01M10 10v5" /></svg>
                            <p class="text-[10px] font-bold text-purple-600 leading-relaxed">Apakah Anda yakin semua rincian sudah benar? Tindakan ini akan langsung mencatat data ke dalam sistem audit.</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-7 pt-6 border-t border-zinc-100 grid grid-cols-2 gap-4">
                    <button type="button" onclick="OT.close()" class="flex items-center justify-center py-3.5 bg-gray-100 rounded-2xl text-xs font-black uppercase tracking-widest text-zinc-700 hover:bg-gray-200 active:scale-95 transition-all duration-150">Batalkan</button>
                    <button type="submit" class="flex items-center justify-center gap-3 py-3.5 bg-purple-600 rounded-2xl text-xs font-black uppercase tracking-widest text-white shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.25)] hover:bg-purple-700 active:scale-95 transition-all duration-150"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 16 16"><path d="M14 2L2 7.5l5 2.5 2.5 5L14 2z" /></svg>Kirim untuk Ditinjau</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        @media (min-width: 640px) {
            #ot-modal-panel { position: absolute !important; top: 50% !important; left: 50% !important; bottom: auto !important; right: auto !important; transform: translate(-50%, -46px) !important; width: calc(100% - 2rem) !important; max-width: 700px !important; border-radius: 32px !important; opacity: 0 !important; transition: transform 0.28s cubic-bezier(.22, 1, .36, 1), opacity 0.2s ease !important; }
            #ot-modal-panel.is-open { transform: translate(-50%, -50%) !important; opacity: 1 !important; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        var OT = (function () {
            'use strict';
            var overlay = document.getElementById('ot-modal-overlay'), panel = document.getElementById('ot-modal-panel'), form = document.getElementById('overtime-form'), dateEl = document.getElementById('ot-work-date');

            /* Isi tanggal hari ini */
            if (dateEl) {
                var d = new Date();
                dateEl.value = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            }
            var successMessage = sessionStorage.getItem('successMessage');

            if (successMessage) {
                sessionStorage.removeItem('successMessage');
                var box = document.createElement('div');
                box.id = 'success-alert'; box.className = 'mb-5 px-5 py-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold'; box.textContent = successMessage;
                var content = document.querySelector('.px-4.sm\\:px-6.lg\\:px-8.py-7.w-full'); content.prepend(box);
                setTimeout(function () { box.style.transition = 'all .4s ease'; box.style.opacity = '0'; setTimeout(function () { box.remove(); }, 400); }, 3000);
            }

            /* ESC tutup modal */
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && overlay.style.display !== 'none') close(); });

            /* Tutup dropdown kalau klik di luar */
            document.addEventListener('click', function (e) {
                var wrapper = document.getElementById('dd-wrapper'), ddPanel = document.getElementById('dd-panel');
                if (wrapper && ddPanel && !wrapper.contains(e.target)) { ddPanel.style.display = 'none'; var chevron = document.getElementById('dd-chevron'); if (chevron) chevron.style.transform = ''; }
            });

            function open() {
                overlay.style.display = 'block'; document.body.style.overflow = 'hidden';
                /* Animasi masuk */
                requestAnimationFrame(function () { panel.style.transform = 'translateY(0)'; panel.style.opacity = '1'; panel.classList.add('is-open'); });
            }

            function close() {
                panel.style.opacity = '0'; panel.classList.remove('is-open');
                /* Mobile */
                if (window.innerWidth < 640) panel.style.transform = 'translateY(30px)';
                setTimeout(function () { overlay.style.display = 'none'; document.body.style.overflow = ''; }, 250);
            }

            function toggleDd(e) {
                if (e) e.stopPropagation();
                var ddPanel = document.getElementById('dd-panel'), chevron = document.getElementById('dd-chevron'), isOpen = ddPanel.style.display !== 'none';
                ddPanel.style.display = isOpen ? 'none' : 'block'; if (chevron) chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
            }

            function filter(value, label) {
                document.getElementById('dd-label').textContent = label; document.getElementById('dd-panel').style.display = 'none';
                var chevron = document.getElementById('dd-chevron'); if (chevron) chevron.style.transform = '';
                var rows = document.querySelectorAll('.ot-row'), visible = 0;
                rows.forEach(function (row) { var show = (value === 'all' || row.dataset.status === value); row.style.display = show ? '' : 'none'; if (show) visible++; });
                document.getElementById('filter-empty-state').classList.toggle('hidden', visible > 0);
            }

            function fileChange(input) {
                var nameEl = document.getElementById('upload-name'), hintEl = document.getElementById('upload-hint');
                if (input.files && input.files[0]) { nameEl.textContent = input.files[0].name; nameEl.style.color = '#059669'; hintEl.textContent = 'File terpilih ✓'; hintEl.style.color = '#6ee7b7'; }
            }

            function submit(e) {
                e.preventDefault();
                var formEl = document.getElementById('overtime-form'), fd = new FormData(formEl);
                fetch('{{ route("staff.overtime.store") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: fd })
                    .then(async response => {
                        const data = await response.json();
                        if (response.ok) {
                            sessionStorage.setItem('successMessage', 'Permintaan lembur berhasil dikirim!'); formEl.reset();
                            document.getElementById('upload-name').textContent = 'Unggah Bukti'; document.getElementById('upload-name').style.color = '';
                            document.getElementById('upload-hint').textContent = 'JPG, PNG • MAX 5MB'; document.getElementById('upload-hint').style.color = '';
                            close(); location.reload();
                        } else { alert('Gagal mengirim pengajuan'); console.log(data); }
                    })
                    .catch(error => { alert('Terjadi kesalahan'); console.log(error); });
            }

            return { open: open, close: close, toggleDd: toggleDd, filter: filter, fileChange: fileChange, submit: submit };
        })();
    </script>
@endpush