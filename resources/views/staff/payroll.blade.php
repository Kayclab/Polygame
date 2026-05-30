@extends('layouts.app')
@section('title', 'Slip Gaji')

@section('content')
    @php use Illuminate\Support\Facades\Storage; @endphp
    <div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 max-w-7xl mx-auto space-y-6 sm:space-y-10">
        {{-- ===== PAGE HEADER ===== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            {{-- Title Block --}}
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="6" />
                        <path d="M8 5v3l2 1" />
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-purple-600">Riwayat Penghasilan</span>
                </div>
                <h1 class="font-mono font-bold text-2xl sm:text-3xl text-zinc-900 leading-tight">Slip Gaji</h1>
                <p class="font-mono text-xs sm:text-sm text-gray-400">Lihat dan unduh laporan gaji bulanan resmi Anda.</p>
            </div>

            {{-- Jumlah Terakhir Card --}}
            <div
                class="flex items-center gap-4 px-5 py-4 sm:px-6 sm:py-5 bg-purple-50 rounded-3xl sm:rounded-3xl border border-purple-100/60 self-start">
                <div
                    class="w-11 h-11 sm:w-14 sm:h-14 bg-purple-600 rounded-2xl sm:rounded-2xl shadow-[0px_20px_25px_-5px_rgba(147,51,234,0.30)] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17" />
                        <polyline points="16 7 22 7 22 13" />
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-purple-400 mb-0.5">Gaji Bulan Ini</p>
                    <p class="text-xl sm:text-2xl font-black text-zinc-900 font-mono">
                        {{ isset($lastJumlah) ? number_format($lastJumlah, 2) : '0.000.000' }}</p>
                </div>
            </div>
        </div>

        {{-- ===== PAYMENT REGISTRY ===== --}}
        <div class="bg-white rounded-3xl sm:rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
            {{-- Table Header Bar --}}
            <div class="px-4 sm:px-8 h-14 sm:h-16 border-b border-zinc-100 flex items-center justify-between gap-4">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-zinc-400">Daftar Slip
                    Gaji</span>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-2xl border border-zinc-100 shadow-sm"><svg
                        class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 14 14">
                        <circle cx="6" cy="6" r="4.5" />
                        <path d="M6 4v2M6 8h.01" />
                    </svg><span class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-gray-400">Catatan
                        Resmi</span></div>
            </div>

            {{-- ── MOBILE CARD LIST (< sm) ── --}} <div class="block sm:hidden divide-y divide-zinc-100/70">
                @forelse ($payslips as $slip)
                    <div class="px-4 py-5 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 bg-purple-600/5 rounded-xl flex items-center justify-center shrink-0"><svg
                                    class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.67"
                                    viewBox="0 0 20 20">
                                    <path d="M4 2h8l4 4v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3a1 1 0 011-1z" />
                                    <path d="M12 2v4h4M7 9h6M7 12h4" />
                                </svg></div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-zinc-900 truncate">
                                    {{ \Carbon\Carbon::parse($slip->periode)->translatedFormat('F Y') }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $slip->created_at->format('Y-m-d')}}</p>
                                <span class="text-sm md:text-base font-black text-zinc-900">Rp
                                    {{ number_format($slip->total_gaji, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" title="View" data-slip='@json($slip)'
                                data-employee="{{ json_encode($employee) }}"
                                onclick="openModal(JSON.parse(this.dataset.slip), JSON.parse(this.dataset.employee))"
                                class="inline-flex w-9 h-9 items-center justify-center bg-gray-100 rounded-xl border border-zinc-200/60 hover:bg-gray-200 active:scale-95 transition-all duration-150"><svg
                                    class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 16 16">
                                    <ellipse cx="8" cy="8" rx="6" ry="3.8" />
                                    <circle cx="8" cy="8" r="1.5" fill="currentColor" stroke="none" />
                                </svg></button>
                            <a href="{{ asset('storage/' . $slip->file_slip) }}" download target="_blank" title="Unduh"
                                class="inline-flex w-9 h-9 items-center justify-center bg-purple-600 rounded-xl shadow-[0px_8px_12px_-3px_rgba(147,51,234,0.25)] hover:bg-purple-700 active:scale-95 transition-all duration-150"><svg
                                    class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                                    viewBox="0 0 16 16">
                                    <path d="M8 2v8M5 7.5l3 3 3-3M2 12.5h12" />
                                </svg></a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-14 text-center">
                        <p class="text-sm font-bold text-gray-300">No pay slips available yet.</p>
                    </div>
                @endforelse
        </div>

        {{-- ── TABLET & DESKTOP TABLE (≥ sm) ── --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full min-w-140">
                <thead>
                    <tr class="border-b border-zinc-100">
                        <th
                            class="px-6 md:px-10 py-5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 w-[300px]">
                            Siklus Gaji</th>
                        <th
                            class="px-4 md:px-10 py-5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 w-[300px]">
                            Tanggal Rilis</th>
                        <th
                            class="px-4 md:px-10 py-5 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">
                            Jumlah</th>
                        <th
                            class="px-4 md:px-10 py-5 text-right text-[10px] font-bold uppercase tracking-widest text-gray-400 pr-6 md:pr-10">
                            Slip Gaji</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100/70">
                    @forelse ($payslips as $slip)
                        <tr class="group hover:bg-purple-50/30 transition-colors duration-150">
                            <td class="px-6 md:px-10 py-5 md:py-7">
                                <div class="flex items-center gap-3 md:gap-4">
                                    <div
                                        class="w-9 h-9 md:w-10 md:h-10 bg-purple-600/5 rounded-xl md:rounded-2xl flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 md:w-5 md:h-5 text-purple-500" fill="none" stroke="currentColor"
                                            stroke-width="1.67" viewBox="0 0 20 20">
                                            <path d="M4 2h8l4 4v12a1 1 0 01-1 1H4a1 1 0 01-1-1V3a1 1 0 011-1z" />
                                            <path d="M12 2v4h4M7 9h6M7 12h4" />
                                        </svg></div>
                                    <span
                                        class="text-sm md:text-base font-bold text-zinc-900">{{ \Carbon\Carbon::parse($slip->periode)->translatedFormat('F Y') }}</span>
                                </div>
                            </td>
                            <td class="px-4 md:px-10 py-5 md:py-7"><span
                                    class="text-xs font-bold text-gray-400 tracking-wide">{{ $slip->created_at->format('Y-m-d')}}</span>
                            </td>
                            <td class="px-4 md:px-10 py-5 md:py-7"><span
                                    class="text-sm md:text-base font-black text-zinc-900">Rp
                                    {{ number_format($slip->total_gaji, 0, ',', '.') }}</span></td>
                            <td class="px-4 md:px-10 py-5 md:py-7 pr-6 md:pr-10">
                                <div class="flex items-center justify-end gap-2 md:gap-3">
                                    {{-- View Button --}}
                                    <button type="button" title="Lihat Pernyataan" data-slip='@json($slip)'
                                        data-employee='@json($employee)'
                                        onclick="openModal(JSON.parse(this.dataset.slip), JSON.parse(this.dataset.employee))"
                                        class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-gray-100 rounded-xl md:rounded-2xl border border-zinc-200/60 hover:bg-gray-200 active:scale-95 transition-all duration-150"><svg
                                            class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 16 16">
                                            <ellipse cx="8" cy="8" rx="6" ry="3.8" />
                                            <circle cx="8" cy="8" r="1.5" fill="currentColor" stroke="none" />
                                        </svg></button>
                                    {{-- Download Button --}}
                                    <a href="{{ asset('storage/' . $slip->file_slip) }}" download target="_blank" title="Unduh"
                                        class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center bg-purple-600 rounded-xl md:rounded-2xl shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.25)] hover:bg-purple-700 active:scale-95 transition-all duration-150"><svg
                                            class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                                            viewBox="0 0 16 16">
                                            <path d="M8 2v8M5 7.5l3 3 3-3M2 12.5h12" />
                                        </svg></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-16 text-center">
                                <p class="text-sm font-bold text-gray-300">No pay slips available yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>

    {{-- ===== MODAL PREVIEW DETAIL ===== --}}
    <div id="salary-modal" class="fixed inset-0 z-50 items-end sm:items-center justify-center p-0 sm:p-4 hidden"
        role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="absolute inset-0 bg-black/25 backdrop-blur-sm" onclick="closeModal()"></div>
        <div id="modal-panel"
            class="relative bg-white w-full sm:max-w-2xl rounded-t-4xl sm:rounded-4xl shadow-[0px_-10px_40px_-5px_rgba(0,0,0,0.12)] sm:shadow-[0px_25px_60px_-15px_rgba(0,0,0,0.18)] overflow-hidden overflow-y-auto max-h-[92vh] sm:max-h-[90vh] transform transition-all duration-200 translate-y-full sm:translate-y-0 sm:scale-95 opacity-0">
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 bg-zinc-200 rounded-full"></div>
            </div>
            <div class="px-5 sm:px-8 pt-4 sm:pt-8 pb-5 sm:pb-6 border-b border-zinc-100">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3 sm:gap-5">
                        <div
                            class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-50 rounded-[18px] sm:rounded-[22px] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-purple-500" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M12 2L4 5.5v6c0 5.2 3.5 9.8 8 11 4.5-1.2 8-5.8 8-11v-6L12 2z" />
                            </svg></div>
                        <div>
                            <h2 id="modal-title" class="font-mono font-bold text-lg sm:text-2xl text-zinc-900 leading-snug">
                                Pernyataan Gaji Resmi</h2>
                            <p id="modal-subtitle"
                                class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-1"></p>
                        </div>
                    </div>
                    <button onclick="closeModal()"
                        class="inline-flex w-9 h-9 items-center justify-center rounded-xl hover:bg-gray-100 transition-colors shrink-0 mt-0.5"><svg
                            class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 6 6 18M6 6l12 12" />
                        </svg></button>
                </div>
            </div>

            <div class="px-5 sm:px-8 py-5 sm:py-7 flex flex-col sm:grid sm:grid-cols-2 gap-4 sm:gap-5">
                <div
                    class="bg-white border border-zinc-100 rounded-[18px] sm:rounded-[20px] p-4 sm:p-5 shadow-sm space-y-3 sm:space-y-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Informasi Karyawan</p>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between"><span class="text-sm text-gray-400">Nama</span><span
                                id="modal-emp-name" class="text-sm font-black text-zinc-900">—</span></div>
                        <div class="flex items-center justify-between"><span
                                class="text-sm text-gray-400">Jabatan</span><span id="modal-emp-role"
                                class="text-[10px] font-black uppercase tracking-wider bg-zinc-100 text-zinc-700 px-3 py-1 rounded-lg">—</span>
                        </div>
                        <div class="flex items-center justify-between"><span class="text-sm text-gray-400">ID
                                Akun</span><span id="modal-emp-account"
                                class="text-sm font-bold text-zinc-900 tracking-widest">—</span></div>
                    </div>
                </div>
                <div
                    class="bg-white border border-zinc-100 rounded-[18px] sm:rounded-[20px] p-4 sm:p-5 shadow-sm space-y-3 sm:space-y-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Rincian Pembayaran</p>
                    <div class="flex items-center justify-between pt-1"><span class="text-sm font-black text-zinc-900">Gaji
                            Bersih</span><span id="modal-net-salary"
                            class="text-xl sm:text-xl font-black text-purple-600 font-mono">—</span></div>
                </div>
                <div
                    class="sm:col-span-2 bg-gray-100/80 rounded-[18px] sm:rounded-[20px] overflow-hidden border border-zinc-100">
                    <iframe 
                        id="modal-preview-pdf" 
                        src="" 
                        class="w-full h-[520px] bg-white" 
                        style="border:0;"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== JAVASCRIPT LOGIC ===== --}}
    <script>
        const modal = document.getElementById('salary-modal'); const modalPanel = document.getElementById('modal-panel'); const isMobile = () => window.innerWidth < 640;

        /* --- PREVIEW AkSI --- */
        function openModal(slip, employee) {
            const [year, month] = slip.periode.split('-'); const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('modal-subtitle').textContent = monthNames[parseInt(month) - 1] + ' ' + year;
            document.getElementById('modal-emp-name').textContent = employee.name;
            document.getElementById('modal-emp-role').textContent = employee.role.toUpperCase();
            document.getElementById('modal-emp-account').textContent = employee.account_id;
            const amount = parseFloat(slip.total_gaji); document.getElementById('modal-net-salary').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('modal-preview-pdf').src = '/storage/' + slip.file_slip + '#toolbar=0&navpanes=0&scrollbar=0&view=FitH';
            modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => {
                if (isMobile()) { modalPanel.classList.remove('translate-y-full', 'opacity-0'); modalPanel.classList.add('translate-y-0', 'opacity-100'); }
                else { modalPanel.classList.remove('scale-95', 'opacity-0', 'sm:scale-95'); modalPanel.classList.add('scale-100', 'opacity-100'); }
            });
        }

        function closeModal() {
            document.getElementById('modal-preview-pdf').src = '';

            if (isMobile()) {
                modalPanel.classList.remove('translate-y-0', 'opacity-100');
                modalPanel.classList.add('translate-y-full', 'opacity-0');
            } else {
                modalPanel.classList.remove('scale-100', 'opacity-100');
                modalPanel.classList.add('scale-95', 'opacity-0');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 200);
        }

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { if (!modal.classList.contains('hidden')) closeModal(); } });
    </script>
@endsection