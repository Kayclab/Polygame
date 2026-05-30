@extends('layouts.app')
@section('title', 'Evaluasi Staf – Poly Games Cafe')

@section('content')
    <div class="p-6 lg:p-8 w-full min-w-0">
        {{-- PAGE HEADER --}}
        <div class="mb-7">
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="5.33" r="2.67" stroke="#9333ea" stroke-width="1.33" fill="none" /><path d="M2.67 13.33a5.33 5.33 0 0110.67 0" stroke="#9333ea" stroke-width="1.33" stroke-linecap="round" fill="none" /></svg>
                <span class="text-[10px] font-bold uppercase tracking-widest text-purple-600">Portal Evaluasi</span>
            </div>
            <h1 class="font-mono font-bold text-3xl text-zinc-900 leading-tight">Evaluasi Staff</h1>
            <p class="font-mono text-sm text-gray-400 mt-1 max-w-lg leading-relaxed">Penilaian kinerja anonim pada skala 10-100. Mode evaluasi kolektif memastikan konsistensi di seluruh tim.</p>
        </div>

        {{-- HERO ROW --}}
        <div class="flex flex-col lg:flex-row rounded-3xl overflow-hidden mb-6" style="box-shadow:0 16px 40px -10px rgba(147,51,234,.22);">
            <div class="flex-1 bg-purple-600 flex items-center justify-between px-8 py-8 gap-6 min-w-0">
                <div class="min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-3 flex items-center gap-2" style="color:rgba(255,255,255,.65);">Indeks Kinerja Anda <svg width="12" height="12" viewBox="0 0 13 13" fill="none" class="shrink-0"><path d="M6.5 1l1.18 2.38 2.63.38-1.9 1.85.45 2.62L6.5 7l-2.36 1.23.45-2.62L2.7 3.76l2.63-.38L6.5 1z" stroke="#fbbf24" stroke-width="1.1" fill="none" /></svg></p>
                    <div class="flex items-baseline gap-2"><span class="font-mono font-black text-white leading-none text-5xl sm:text-6xl">{{ $receivedScore }}</span><span class="text-sm font-bold" style="color:rgba(255,255,255,.55);">/ 100</span></div>
                </div>
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl flex items-center justify-center shrink-0" style="background:rgba(255,255,255,.12);"><svg width="36" height="36" viewBox="0 0 38 38" fill="none"><circle cx="19" cy="19" r="16" stroke="white" stroke-width="2.2" fill="none" /><circle cx="19" cy="19" r="10" stroke="white" stroke-width="2.2" fill="none" /><circle cx="19" cy="19" r="4" stroke="white" stroke-width="2.2" fill="none" /></svg></div>
            </div>
            <div class="lg:w-96 shrink-0 bg-amber-50 border-t lg:border-t-0 lg:border-l border-amber-100 flex items-center justify-between px-8 py-8 gap-4">
                <div class="min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:rgba(217,119,6,.60);">Persyaratan Siklus</p>
                    <p class="text-xl sm:text-2xl font-black uppercase text-amber-600 leading-tight mb-2">Tindakan Wajib</p>
                    <p class="text-[10px] font-semibold leading-5 max-w-xs" style="color:rgba(217,119,6,.75);">Selesaikan semua penilaian sebelum siklus penggajian Feb 2026 berakhir.</p>
                </div>
                <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center shrink-0"><svg width="26" height="26" viewBox="0 0 28 28" fill="none"><rect x="4" y="10" width="20" height="14" rx="2" stroke="#d97706" stroke-width="2.2" fill="none" /><path d="M9 10V7a5 5 0 0110 0v3" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" fill="none" /><circle cx="14" cy="17" r="2.2" fill="#d97706" /></svg></div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div id="mainFilterBox" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 bg-white rounded-3xl border border-zinc-100 shadow-sm mb-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-purple-50 rounded-xl flex items-center justify-center shrink-0"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M1.5 3.75h12M3.5 7.5h8M5.5 11.25h4" stroke="#9333ea" stroke-width="1.4" stroke-linecap="round" /></svg></div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-zinc-900">Tampilan Filter</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Isolasi kinerja berdasarkan jabatan organisasi.</p>
                </div>
            </div>
            <div class="flex p-1 bg-gray-100 rounded-2xl gap-0.5 self-start sm:self-auto">
                <button onclick="setFilter(this,'all')" class="filter-tab is-active px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border-0 cursor-pointer transition-all">Semua</button>
                <button onclick="setFilter(this,'owner')" class="filter-tab px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border-0 cursor-pointer transition-all">Owner</button>
                <button onclick="setFilter(this,'staff')" class="filter-tab px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border-0 cursor-pointer transition-all">Staff</button>
            </div>
        </div>

        {{-- SCOREBOARD --}}
        <div id="viewScoreboard" class="space-y-6">
            {{-- TABEL 1: AKSI EVALUASI --}}
            <div class="bg-white rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 sm:px-8 h-16 border-b border-zinc-100 bg-gray-50/40">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Pusat Evaluasi Staff</span>
                    <div class="flex items-center gap-2 px-3 h-7 bg-white rounded-xl border border-zinc-100">
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 hidden sm:block">Pelindung Identitas Aktif</span>
                    </div>
                </div>

                {{-- KOTAK KERJAKAN EVAL --}}
                @if($canDoEvaluation)
                <div class="px-6 py-5 border-b border-zinc-100 bg-white flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-black text-zinc-900">Evaluasi yang Harus Dikerjakan</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">Isi penilaian untuk karyawan lain pada periode evaluasi aktif.</p>
                    </div>
                    <button type="button" onclick="startEvaluation()" class="w-56 flex items-center justify-center h-12 bg-purple-600 rounded-2xl text-white text-xs font-black uppercase tracking-widest shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.25)] hover:bg-purple-700 active:scale-95 transition-all duration-150 shrink-0 self-start whitespace-nowrap">Kerjakan Evaluasi</button>
                </div>
                @endif
                {{-- KOTAK LIHAT EVALUASI PRIBADI --}}
                <div class="px-6 py-5 bg-white flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-black text-zinc-900">Ringkasan Evaluasi Pribadi</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">Lihat nilai yang diberikan orang lain kepada Anda.</p>
                    </div>
                    <button type="button" onclick="openSelfPerf()" class="w-56 flex items-center justify-center h-12 bg-purple-600 rounded-2xl text-white text-xs font-black uppercase tracking-widest shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.25)] hover:bg-purple-700 active:scale-95 transition-all duration-150 shrink-0 self-start whitespace-nowrap">Lihat Ringkasan</button>
                </div>
            </div>

            {{-- TABEL 2: NILAI YANG ANDA BERIKAN --}}
            <div class="bg-white rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 sm:px-8 h-16 border-b border-zinc-100 bg-gray-50/40">
                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Riwayat Nilai yang Anda Berikan</span>
                </div>

                {{-- HEADER TABEL --}}
                <div class="hidden md:grid border-b border-zinc-100 bg-gray-50/10" style="grid-template-columns:2fr 1fr 1fr;">
                    <div class="px-8 py-4 text-[9px] font-black uppercase tracking-widest text-gray-400">Nama</div>
                    <div class="px-4 py-4 text-[9px] font-black uppercase tracking-widest text-gray-400">Jabatan</div>
                    <div class="px-8 py-4 text-[9px] font-black uppercase tracking-widest text-gray-400 text-right">Rata-rata Nilai Diberikan</div>
                </div>

                {{-- TABEL NILAI YANG KITA BERIKAN --}}
                @forelse($staffList as $p)
                    @php $group = $p['role'] === 'owner' ? 'owner' : 'staff'; $img = $p['img'] ?? null; @endphp
                    <div class="staff-row hover:bg-purple-50/20 transition-colors duration-150 border-b border-zinc-100/50 last:border-b-0" data-group="{{ $group }}">
                        {{-- Mobile --}}
                        <div class="flex items-center justify-between px-5 py-4 gap-3 md:hidden">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl shrink-0 bg-purple-50 flex items-center justify-center text-purple-600 text-xs font-black">
                                    {!! $img ? '<img src="' . $img . '" alt="" class="w-full h-full object-cover rounded-xl">' : e($p['initial']) !!}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-zinc-900 truncate">{{ $p['name'] }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">{{ $p['role'] }}</p>
                                </div>
                            </div>
                            <span class="text-lg font-black text-purple-600 tabular-nums">{{ $p['score'] }}</span>
                        </div>

                        {{-- Desktop --}}
                        <div class="hidden md:grid items-center min-h-20" style="grid-template-columns:2fr 1fr 1fr;">
                            <div class="px-8 flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl shrink-0 bg-purple-50 flex items-center justify-center text-purple-600 text-xs font-black">
                                    {!! $img ? '<img src="' . $img . '" alt="" class="w-full h-full object-cover rounded-xl">' : e($p['initial']) !!}
                                </div>
                                <span class="text-sm font-bold text-zinc-900 truncate">{{ $p['name'] }}</span>
                            </div>
                            <div class="px-4"><span class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-xl text-[9px] font-black uppercase tracking-widest text-gray-500 whitespace-nowrap">{{ $p['role'] }}</span></div>
                            <div class="px-8 flex items-center justify-end gap-3">
                                <span class="text-lg font-black text-purple-600 w-9 tabular-nums leading-none shrink-0">{{ $p['score'] }}</span>
                                <div class="flex-1 max-w-32 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-purple-600 rounded-full" @style(["width: {$p['score']}%"])></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-8 py-6 text-center">
                        <p class="text-sm font-black text-zinc-900">Belum ada nilai yang Anda berikan</p>
                        <p class="text-[10px] text-gray-400 mt-1">Data akan muncul setelah Anda mengirim evaluasi.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- AUDIT VIEW --}}
        <div id="viewAudit" style="display:none;">
            <div id="auditQuestionFilter" style="display:none;" class="items-center justify-between px-6 sm:px-8 py-4 mb-6 bg-white rounded-3xl border border-zinc-100 shadow-sm">
                <div>
                    <p class="text-sm font-black text-zinc-900">Pilih Jenis Soal Evaluasi</p>
                    <p class="text-[10px] font-bold text-gray-400 mt-1">Gunakan soal staff untuk menilai staff and soal owner untuk menilai owner.</p>
                </div>
                <div class="flex items-center gap-2 bg-gray-100 rounded-2xl p-1">
                    <button type="button" onclick="filterAuditQuestions('staff')" id="btnAuditStaff" class="audit-filter-btn px-5 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest">Soal Staff</button>
                    <button type="button" onclick="filterAuditQuestions('owner')" id="btnAuditOwner" class="audit-filter-btn px-5 h-10 rounded-xl text-[10px] font-black uppercase tracking-widest">Soal Owner</button>
                </div>
            </div>

            <form id="auditForm" method="POST" action="{{ route('staff.evaluations.submit') }}">
                @csrf <input type="hidden" name="scores" id="scoresInput">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 bg-purple-50 rounded-3xl border border-purple-100 mb-6">
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="closeAudit()" class="w-9 h-9 bg-white rounded-xl border border-purple-100 flex items-center justify-center hover:bg-purple-100 transition-colors cursor-pointer shrink-0">←</button>
                        <div>
                            <p id="auditName" class="text-base font-black text-purple-600 leading-5">Evaluasi Karyawan Aktif</p>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-purple-400 mt-0.5">Ulasan anonim · Skala: 10-100</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 px-3 h-7 bg-white rounded-xl border border-purple-100 self-start sm:self-auto shrink-0">
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Pelindung Identitas Aktif</span>
                    </div>
                </div>

                @foreach($metrics->groupBy(function ($item) { return strtolower(trim($item->target_role ?? 'staff')); }) as $role => $roleMetrics)
                    @foreach($roleMetrics->values() as $mi => $m)
                        @php $questionRole = strtolower(trim($m->target_role ?? 'staff')); @endphp
                        <div class="mb-6 audit-question" data-role="{{ $questionRole }}">
                            <p class="text-[10px] font-black uppercase tracking-widest text-purple-500 mb-1">Pertanyaan {{ $mi + 1 }} · {{ $m->kategori ?? 'Evaluasi' }}</p>
                            <p class="text-sm font-black text-zinc-900 italic mb-4">"{{ $m->pertanyaan }}"</p>

                            <div class="bg-white rounded-3xl border border-zinc-100 shadow-sm overflow-hidden">
                                @foreach($auditTargets as $target)
                                    @php $targetRole = strtolower(trim($target->role)) === 'owner' ? 'owner' : 'staff'; $fieldId = $m->id_soal . '-' . $target->id_kry; $initial = strtoupper(substr($target->n_kry, 0, 1)); @endphp
                                    <div class="audit-target flex flex-col lg:flex-row lg:items-center gap-4 px-6 py-5 border-b border-zinc-100 last:border-b-0" data-role="{{ $targetRole }}">
                                        <div class="flex items-center gap-3 lg:w-48 shrink-0">
                                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xs font-black">{{ $initial }}</div>
                                            <div>
                                                <p class="text-sm font-bold text-zinc-900 leading-4">{{ $target->n_kry }}</p>
                                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-0.5">{{ $target->role }}</p>
                                            </div>
                                        </div>

                                        <div class="flex-1">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-2">Beri Penilaian (10-100)</p>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                @foreach($scale as $step)
                                                    <button type="button" id="btn_{{ $fieldId }}_{{ $step }}" data-field="{{ $fieldId }}" data-step="{{ $step }}" onclick="setRating(this.dataset.field, parseInt(this.dataset.step))" class="w-9 h-9 rounded-xl text-[10px] font-black transition-all cursor-pointer border-0 bg-gray-100 text-gray-400 hover:bg-purple-50 hover:text-purple-600">{{ $step }}</button>
                                                @endforeach
                                            </div>
                                            <input type="hidden" id="val_{{ $fieldId }}" value="">
                                        </div>
                                        <div class="lg:w-12 flex lg:justify-end shrink-0"><span id="disp_{{ $fieldId }}" class="text-xl font-black text-purple-600 tabular-nums">-</span></div>
                                    </div>
                                				@endforeach
                            </div>
                        </div>
                    @endforeach
                @endforeach
                <div class="flex justify-end mb-8">
                    <button type="button" onclick="openSubmitConfirm()" class="px-8 py-4 bg-purple-600 hover:bg-purple-700 rounded-2xl text-[11px] font-black uppercase tracking-widest text-white transition-colors border-0 cursor-pointer">Kirim Evaluasi</button>
                </div>
            </form>
        </div>

        {{-- MODAL: Self Performance --}}
        <div id="modalSelfPerf" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.45);backdrop-filter:blur(5px);">
            <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden">
                <div class="flex items-start justify-between px-6 pt-6 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-600 rounded-2xl flex items-center justify-center shrink-0"><svg width="18" height="18" viewBox="0 0 18 18" fill="none"><polyline points="2,13 6,7 9,10 12,5 16,13" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none" /></svg></div>
                        <div>
                            <p class="font-mono text-sm font-black text-zinc-900">Indeks Kinerja Diri</p>
                            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400 mt-0.5">Rata-rata Kategori Agregat Luas</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeSelfPerf()" style="background:transparent;border:0;cursor:pointer;" class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-400 hover:bg-purple-50 hover:text-purple-600 hover:scale-110 active:scale-95 transition-all duration-200 shrink-0"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><line x1="2" y1="2" x2="12" y2="12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /><line x1="12" y1="2" x2="2" y2="12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /></svg></button>
                </div>
                <div class="px-6 pb-6">
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        @forelse($selfSummary as $item)
                            <div class="bg-white rounded-2xl p-4 border border-zinc-100 shadow-sm">
                                <p class="text-[8px] font-black uppercase tracking-widest text-gray-400 leading-3 mb-2 truncate">{{ $item['kategori'] }}</p>
                                <p class="text-2xl font-black text-purple-600 leading-none mb-2 tabular-nums">{{ $item['nilai'] }}</p>
                                <div class="w-full h-1 bg-gray-200 rounded-full overflow-hidden"><div class="h-full bg-purple-600 rounded-full" style="width: {{ $item['nilai'] }}%"></div></div>
                            </div>
                        @empty
                            <div class="col-span-3 bg-gray-50 rounded-2xl p-5 border border-zinc-100 text-center">
                                <p class="text-sm font-black text-zinc-900">Belum ada nilai evaluasi masuk</p>
                                <p class="text-[10px] text-gray-400 mt-1">Ringkasan muncul setelah orang lain menilai Anda.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl border border-zinc-100">
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-black text-zinc-900 leading-4 mb-1">Introspeksi DIri Anda!</p>
                            <p class="text-[10px] text-gray-500 leading-4">Nilai ini dirangkum dari evaluasi yang diberikan orang lain kepada Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: Confidential Analyze --}}
    <div id="modalConfidential" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.45);backdrop-filter:blur(5px);">
        <div class="bg-white rounded-[28px] w-full max-w-lg shadow-2xl overflow-hidden">
            <div class="flex items-center gap-4 px-6 py-5 border-b border-zinc-100">
                <div id="confAvatar" class="w-12 h-12 bg-zinc-100 rounded-2xl flex items-center justify-center text-lg font-black text-gray-500 shrink-0">AC</div>
                <div class="flex-1 min-w-0">
                    <p id="confName" class="text-xl font-black text-zinc-900 truncate">Alex Chen</p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-0.5">Analitik</p>
                </div>
                <button onclick="closeConfidential()" style="background:transparent;border:0;cursor:pointer;" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 text-gray-400 shrink-0"><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><line x1="2.5" y1="2.5" x2="12.5" y2="12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /><line x1="12.5" y1="2.5" x2="2.5" y2="12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" /></svg></button>
            </div>
            <div class="p-6">
                <div class="flex flex-col sm:flex-row gap-4 mb-5">
                    <div class="flex-1 rounded-[20px] p-5" style="background:#f3f0ff;">
                        <p class="text-[10px] font-black uppercase tracking-widest mb-3" style="color:#a78bfa;">Indeks Kumulatif</p>
                        <div class="flex items-end gap-3 mb-2">
                            <span id="confScore" class="font-mono font-black leading-none tabular-nums" style="font-size:4rem;color:#7c3aed;">88</span>
                            <div class="pb-1">
                                <p class="text-[9px] font-black uppercase tracking-widest" style="color:#c4b5fd;">Skala</p>
                                <p class="text-xl font-black" style="color:#c4b5fd;">/ 100</p>
                            </div>
                        </div>
                        <p id="confCycles" class="text-sm font-medium" style="color:#a78bfa;">Dirangkum dari 18 siklus</p>
                    </div>
                    <div class="flex-1 bg-white rounded-[20px] border border-zinc-100 p-5 flex flex-col gap-3">
                        <div class="flex items-center gap-2"><svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="6.5" cy="4.5" r="2.5" stroke="#9ca3af" stroke-width="1.2" fill="none" /><path d="M2 11.5a4.5 4.5 0 019 0" stroke="#9ca3af" stroke-width="1.2" stroke-linecap="round" fill="none" /></svg><span class="text-[9px] font-black uppercase tracking-widest text-gray-400">Umpan Real-time</span></div>
                        <p class="text-[10px] text-gray-500 leading-5 italic flex-1">"Data kinerja dirangkum secara real-time. Identitas staf tetap terenkripsi."</p>
                        <div class="flex gap-2">
                            <div class="flex-1 bg-zinc-50 rounded-xl px-3 py-2 border border-zinc-100">
                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Penilaian</p>
                                <p id="confHits" class="text-xl font-black text-zinc-900 tabular-nums">6</p>
                            </div>
                            <div class="flex-1 rounded-xl px-3 py-2 border" style="background:#f0fdf4;border-color:#bbf7d0;">
                                <p class="text-[9px] font-black uppercase tracking-widest mb-1" style="color:#16a34a;">Status</p>
                                <p class="text-xl font-black" style="color:#16a34a;">Sync</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="confDetails" class="flex flex-col gap-3 mb-5"></div>
                <button onclick="closeConfidential()" style="background:#9333ea;border:0;cursor:pointer;" class="w-full py-3.5 hover:bg-purple-700 rounded-2xl text-[11px] font-black uppercase tracking-widest text-white transition-colors">Tutup Analisis</button>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI SUBMIT --}}
    <div id="modalSubmitConfirm" style="display:none;position:fixed;inset:0;z-index:10000;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.45);backdrop-filter:blur(5px);">
        <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden p-6">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 font-black mb-4">!</div>
                <p class="text-lg font-black text-zinc-900 mb-2">Kirim Evaluasi?</p>
            </div>
            <p class="text-sm text-gray-500 leading-6 mb-6">Pastikan semua nilai sudah benar. Setelah dikirim, hasil evaluasi akan masuk ke tabel nilai yang Anda berikan.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeSubmitConfirm()" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-zinc-600 border-0 cursor-pointer">Batal</button>
                <button type="button" onclick="submitAudit()" class="flex-1 px-4 py-3 bg-purple-600 hover:bg-purple-700 rounded-2xl text-[10px] font-black uppercase tracking-widest text-white border-0 cursor-pointer">Ya, Kirim</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        #modalSelfPerf>div, #modalConfidential>div { animation: slideUp .2s ease; }
        .filter-tab { background: transparent; color: #9ca3af; }
        .filter-tab.is-active { background: #9333ea !important; color: #fff !important; box-shadow: 0 2px 8px rgba(147, 51, 234, .35) !important; }
        .filter-tab:not(.is-active):hover { background: rgba(147, 51, 234, .08) !important; color: #9333ea !important; }
        .rating-active { background: #9333ea !important; color: white !important; }
    </style>
@endpush

@push('scripts')
    <script>
        const getEl = id => document.getElementById(id); const handleAnalyze = data => openAuditModal(data);
        function setFilter(btn, filter) { document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('is-active')); btn.classList.add('is-active'); document.querySelectorAll('.staff-row').forEach(row => row.style.display = (filter === 'all' || row.dataset.group === filter) ? '' : 'none'); }
        function openAudit(data) { getEl('auditName').textContent = data.name + ' — Audit Manajemen Aktif'; getEl('viewScoreboard').style.display = 'none'; getEl('viewAudit').style.display = 'block'; window.scrollTo({ top: 0, behavior: 'smooth' }); }
        
        function filterAuditQuestions(role) {
            role = String(role).toLowerCase().trim();
            document.querySelectorAll('.audit-question').forEach(item => { let itemRole = String(item.dataset.role || '').toLowerCase().trim(); item.style.display = itemRole === role ? '' : 'none'; });
            document.querySelectorAll('.audit-target').forEach(item => { let itemRole = String(item.dataset.role || '').toLowerCase().trim(); item.style.display = itemRole === role ? '' : 'none'; });
            document.querySelectorAll('.audit-filter-btn').forEach(btn => { btn.classList.remove('bg-purple-600', 'text-white', 'shadow-md'); btn.classList.add('text-gray-400'); });
            const activeBtn = role === 'staff' ? document.getElementById('btnAuditStaff') : document.getElementById('btnAuditOwner');
            if (activeBtn) { activeBtn.classList.add('bg-purple-600', 'text-white', 'shadow-md'); activeBtn.classList.remove('text-gray-400'); }
        }
        
        const closeAudit = () => { document.getElementById('viewAudit').style.display = 'none'; document.getElementById('viewScoreboard').style.display = 'block'; document.getElementById('mainFilterBox').style.display = ''; document.getElementById('auditQuestionFilter').style.display = 'none'; };
        function setRating(fieldId, value) { getEl('val_' + fieldId).value = value; getEl('disp_' + fieldId).textContent = value; [10, 20, 30, 40, 50, 60, 70, 80, 90, 100].forEach(step => { let b = getEl('btn_' + fieldId + '_' + step); if (!b) return; if (step === value) { b.classList.add('rating-active'); b.classList.remove('bg-gray-100', 'text-gray-400'); } else { b.classList.remove('rating-active'); b.classList.add('bg-gray-100', 'text-gray-400'); } }); }
        function startEvaluation() { document.getElementById('viewScoreboard').style.display = 'none'; document.getElementById('mainFilterBox').style.display = 'none'; document.getElementById('viewAudit').style.display = 'block'; document.getElementById('auditQuestionFilter').style.display = 'flex'; filterAuditQuestions('staff'); window.scrollTo({ top: 0, behavior: 'smooth' }); }
        function openSubmitConfirm() { document.getElementById('modalSubmitConfirm').style.display = 'flex'; }
        function closeSubmitConfirm() { document.getElementById('modalSubmitConfirm').style.display = 'none'; }
        function openSelfPerf() { document.getElementById('modalSelfPerf').style.display = 'flex'; }
        function closeSelfPerf() { document.getElementById('modalSelfPerf').style.display = 'none'; }

        function submitAudit() {
            const scores = {}; document.querySelectorAll('input[id^="val_"]').forEach(function(input) { if (input.value !== '') { const key = input.id.replace('val_', ''); const parts = key.split('-'); scores[key] = { personId: parts[1], score: parseInt(input.value) }; } });
            if (Object.keys(scores).length === 0) { closeSubmitConfirm(); alert('Isi minimal satu penilaian dulu.'); return; }
            document.getElementById('scoresInput').value = JSON.stringify(scores); document.getElementById('auditForm').submit();
        }

        function openAuditModal(data) {
            let initials = data.name.split(' ').slice(0, 2).map(w => w.charAt(0).toUpperCase()).join('');
            getEl('confName').textContent = data.name; getEl('confAvatar').textContent = initials; getEl('confScore').textContent = data.score; getEl('confHits').textContent = data.hits; getEl('confCycles').textContent = 'Dirangkum dari ' + data.hits + ' penilaian';
            let box = getEl('confDetails'), html = '', received = @json($receivedEvaluations), given = @json($givenEvaluations);
            let selected = Number(data.id_kry) === Number({{ $login->id_kry }}) ? received : given.filter(evl => Number(evl.id_kry) === Number(data.id_kry));
            if (selected.length === 0) { box.innerHTML = `<div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-100"><p class="text-xs font-bold text-gray-400">Belum ada detail soal evaluasi.</p></div>`; } 
            else {
                selected.forEach(evl => { evl.details.forEach(detail => {
                    let soal = detail.soal ? detail.soal.pertanyaan : '-', kategori = detail.soal ? detail.soal.kategori : 'Evaluasi', nilai = detail.jawaban ?? 0;
                    html += `<div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-100"><div class="flex items-center justify-between gap-3 mb-2"><p class="text-purple-600 text-[9px] font-black uppercase tracking-widest">${kategori}</p><p class="text-lg font-black text-purple-600 tabular-nums">${nilai}</p></div><p class="text-zinc-900 text-xs font-bold leading-5 mb-3">${soal}</p><div class="w-full h-1 bg-gray-200 rounded-full overflow-hidden"><div class="h-full bg-purple-600 rounded-full" style="width:${nilai}%"></div></div></div>`;
                }); });
                box.innerHTML = html;
            }
            getEl('modalConfidential').style.display = 'flex';
        }

        getEl('modalSelfPerf').addEventListener('click', function (e) { if (e.target === this) closeSelfPerf(); });
        getEl('modalConfidential').addEventListener('click', function (e) { if (e.target === this) closeConfidential(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeSelfPerf(); closeConfidential(); } });
    </script>
@endpush