@extends('layouts.app')

@section('title', 'Evaluasi Staf - Poly Games Cafe')

@php
/**
 * Semua data di bawah ini dinormalisasi dari controller:
 * $metrics, $ownersList, $staffsList, $staffData, $systemIndex,
 * $responseVolume, $activeAuditOwner, $activeAuditStaff,
 * $alreadySubmitted, $myEvaluationScores, $currentKaryawanId, $canManageAudit
 */

$ratingSteps = [10,20,30,40,50,60,70,80,90,100];

$staffData = collect($staffData ?? [])->map(function ($s) {
    $name = data_get($s, 'n_kry') ?? data_get($s, 'name') ?? '-';
    $responses = data_get($s, 'responses') ?? '0 Respon';
    $responsesCount = is_numeric($responses)
        ? (int) $responses
        : (int) preg_replace('/[^0-9]/', '', (string) $responses);

    return [
        'id_kry'          => data_get($s, 'id_kry'),
        'n_kry'           => $name,
        'name'            => $name,
        'role'            => data_get($s, 'role') ?? '-',
        'initial'         => data_get($s, 'initial') ?? strtoupper(substr($name, 0, 1)),
        'score'           => (float) (data_get($s, 'score') ?? 0),
        'responses'       => is_numeric($responses) ? $responses . ' Respon' : $responses,
        'responses_count' => $responsesCount,
        'metrics'         => collect(data_get($s, 'metrics', []))->map(function ($m) {
            return [
                'id_soal'   => data_get($m, 'id_soal'),
                'metric'    => data_get($m, 'metric') ?? data_get($m, 'pertanyaan') ?? data_get($m, 'question') ?? '-',
                'kategori'  => data_get($m, 'kategori') ?? data_get($m, 'tag') ?? 'Umum',
                'score'     => data_get($m, 'score'),
                'responses' => data_get($m, 'responses', 0),
            ];
        })->values(),
    ];
})->values();

$metricsView = collect($metrics ?? [])->map(function ($m, $i) {
    $id         = data_get($m, 'id_soal') ?? data_get($m, 'id') ?? $i + 1;
    $kategori   = data_get($m, 'kategori') ?? data_get($m, 'tag') ?? 'Umum';
    $question   = data_get($m, 'pertanyaan') ?? data_get($m, 'question') ?? '-';
    $targetRole = data_get($m, 'target_role') ?? data_get($m, 'role') ?? 'staff';

    return [
        'id'          => $id,
        'tag'         => $kategori,
        'question'    => $question,
        'target_role' => $targetRole,
        'role'        => $targetRole,
        'label'       => 'Metrik #' . ($i + 1) . ' — ' . $kategori,
        'desc'        => '"' . $question . '"',
    ];
})->values();

$normalizePerson = function ($k) {
    $name = data_get($k, 'n_kry') ?? data_get($k, 'name') ?? '-';

    return [
        'id_kry'  => data_get($k, 'id_kry'),
        'name'    => $name,
        'role'    => data_get($k, 'role') ?? '-',
        'initial' => strtoupper(substr($name, 0, 1)),
    ];
};

$ownersForJs = collect($ownersList ?? [])->map($normalizePerson)->values();
$staffForJs  = collect($staffsList ?? [])->map($normalizePerson)->values();

$systemIndex    = (float) ($systemIndex ?? 0);
$responseVolume = (int) ($responseVolume ?? 0);

$alreadySubmitted = array_merge([
    'owner' => false,
    'staff' => false,
], $alreadySubmitted ?? []);

$myEvaluationScores = array_merge([
    'owner' => [],
    'staff' => [],
], $myEvaluationScores ?? []);

$currentKaryawanId = $currentKaryawanId ?? optional(auth()->user())->id_kry;
$canManageAudit = (bool) ($canManageAudit ?? false);
@endphp

@section('content')
<div class="px-6 sm:px-10 lg:px-14 py-8 flex flex-col gap-5">

    {{-- ============================================================ --}}
    {{-- PAGE HEADER (always visible) --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-7">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="award" class="w-3.5 h-3.5 text-purple-600"></i>
                <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Portal Kinerja</span>
            </div>
            <h1 class="text-zinc-900 text-3xl font-bold font-mono leading-9 mb-1">Evaluasi Staf</h1>
            <p class="text-gray-500 text-sm font-normal font-mono leading-6 opacity-80 max-w-xl">
                Audit kinerja anonim dengan skala 10-100. Mode evaluasi kolektif menjaga konsistensi penilaian seluruh tim.
            </p>
        </div>
        {{-- Tab switcher --}}
        <div class="flex-shrink-0 self-start sm:self-auto flex items-center h-9 p-1 bg-gray-50 rounded-2xl outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
            <button id="tab-results" onclick="switchTab('results')"
                    class="h-7 px-4 rounded-xl bg-white shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide transition-all whitespace-nowrap">
                Hasil
            </button>
            <button id="tab-metrics" onclick="switchTab('metrics')"
                    class="h-7 px-4 rounded-xl text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide transition-all whitespace-nowrap">
                Edit Metrik
            </button>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- PANEL: RESULTS --}}
    {{-- ============================================================ --}}
    <div id="panel-results" style="display:flex" class="flex-col gap-6">

        {{-- STAT CARDS --}}
        <div id="stat-cards" class="flex flex-col sm:flex-row gap-3">
            {{-- Purple: System Performance Index — clickable --}}
            <div onclick="openAnalyzeModal('system')"
                 class="flex-1 flex items-center justify-between px-6 sm:px-8 py-6 sm:py-8 bg-purple-600 rounded-[32px] sm:rounded-[40px] shadow-[0px_20px_25px_-5px_rgba(147,51,234,0.20),0px_8px_10px_-6px_rgba(147,51,234,0.20)] outline outline-1 outline-purple-500/20 cursor-pointer hover:bg-purple-700 active:scale-[0.99] transition-all group">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <span class="text-white text-[9px] font-black font-inter uppercase leading-3 tracking-wide opacity-70 truncate">Indeks Kinerja Sistem</span>
                        <i data-lucide="alert-circle" class="w-2.5 h-2.5 text-amber-400 flex-shrink-0"></i>
                    </div>
                    <div class="flex items-end gap-1.5">
                        <span class="text-white text-4xl sm:text-5xl font-black font-inter leading-none">{{ number_format($systemIndex, 1) }}</span>
                        <span class="text-white text-xs font-bold font-inter opacity-60 mb-0.5">/ 100</span>
                    </div>
                    <p class="text-white/50 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide mt-2 group-hover:text-white/70 transition-colors">Klik untuk analisis →</p>
                </div>
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white/10 rounded-2xl sm:rounded-3xl flex items-center justify-center flex-shrink-0 ml-3 group-hover:bg-white/20 transition-colors">
                    <i data-lucide="target" class="w-6 h-6 sm:w-8 sm:h-8 text-white"></i>
                </div>
            </div>
            {{-- White: Cycle Response Volume --}}
            <div class="flex-1 flex items-center justify-between px-6 sm:px-8 py-6 sm:py-8 bg-white rounded-[32px] sm:rounded-[40px] outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
                <div class="min-w-0">
                    <p class="text-gray-500 text-[9px] font-black font-inter uppercase leading-3 tracking-wide opacity-60 mb-1.5 truncate">Volume Respon Siklus</p>
                    <div class="flex items-end gap-1.5 flex-wrap">
                        <span id="cycle-count" class="text-zinc-900 text-4xl sm:text-5xl font-black font-inter leading-none">{{ $responseVolume }}</span>
                        <span class="text-gray-500 text-xs font-bold font-inter uppercase tracking-wide mb-0.5 opacity-60">/ Entri</span>
                    </div>
                </div>
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-2xl sm:rounded-3xl flex items-center justify-center flex-shrink-0 ml-3">
                    <i data-lucide="bar-chart-2" class="w-6 h-6 sm:w-7 sm:h-7 text-zinc-900"></i>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div id="filter-bar" class="flex items-center justify-between gap-3 px-4 sm:px-6 h-14 bg-white rounded-2xl outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-7 h-7 bg-purple-600/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-purple-600"></i>
                </div>
                <p class="text-zinc-900/80 text-[10px] font-black font-inter uppercase leading-4 tracking-wider whitespace-nowrap">Filter Tampilan</p>
            </div>
            <div class="flex items-center h-9 p-1 bg-gray-50 rounded-2xl outline outline-1 outline-zinc-100 flex-shrink-0">
                <button onclick="filterView('all')" id="filter-all"
                        class="h-7 px-4 rounded-xl bg-white shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] text-purple-600 text-[10px] font-black font-inter uppercase leading-4 tracking-wide transition-all">Semua</button>
                <button onclick="filterView('owner')" id="filter-owner"
                        class="h-7 px-4 rounded-xl text-gray-500 text-[10px] font-black font-inter uppercase leading-4 tracking-wide transition-all {{ (!$canManageAudit && !$activeAuditOwner) ? 'hidden' : '' }}">Owner</button>
                <button onclick="filterView('staff')" id="filter-staff"
                        class="h-7 px-4 rounded-xl text-gray-500 text-[10px] font-black font-inter uppercase leading-4 tracking-wide transition-all {{ (!$canManageAudit && !$activeAuditStaff) ? 'hidden' : '' }}">Staf</button>
            </div>
        </div>

        {{-- Scoreboard table (shown on All filter) --}}
        <div id="scoreboard-table" class="bg-white rounded-[40px] outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-4 sm:py-5 bg-gray-50/20 border-b border-zinc-100">
                <span class="text-zinc-900 text-xs sm:text-sm font-bold font-inter uppercase leading-5 tracking-wider opacity-70">Papan Skor Rahasia</span>
                <div class="flex items-center gap-1.5 px-2.5 h-6 bg-white/50 rounded-xl outline outline-1 outline-zinc-100">
                    <i data-lucide="shield" class="w-3 h-3 text-gray-500 opacity-60"></i>
                    <span class="text-gray-500 text-[8px] font-black font-inter uppercase leading-4 tracking-wide opacity-60 hidden sm:inline">Pelindung Identitas Aktif</span>
                    <span class="text-gray-500 text-[8px] font-black font-inter uppercase leading-4 tracking-wide opacity-60 sm:hidden">Pelindung Aktif</span>
                </div>
            </div>
            <div class="hidden sm:grid grid-cols-[1fr_160px_240px_160px_160px] px-8 py-4 border-b border-zinc-100 bg-gray-50/10">
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Entitas</span>
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Peran</span>
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Rata-rata (100)</span>
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Respon</span>
                <span class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide text-right">Aksi</span>
            </div>
            <div class="divide-y divide-zinc-100/50">
                @foreach($staffData as $staff)
                @php
                    $scoreCol  = $staff['score'] >= 90 ? 'text-emerald-600' : ($staff['score'] >= 80 ? 'text-purple-600' : 'text-amber-500');
                    $barCol    = $staff['score'] >= 90 ? 'bg-emerald-500'   : ($staff['score'] >= 80 ? 'bg-purple-600'   : 'bg-amber-400');
                    $roleGroup = $staff['role'] === 'owner' ? 'owner' : 'staff';
                @endphp
                <div class="staff-row hover:bg-zinc-50/40 transition-colors" data-group="{{ $roleGroup }}">

                    {{-- Mobile layout: compact single row --}}
                    <div class="flex sm:hidden items-center gap-3 px-5 py-4">
                        <div class="w-9 h-9 bg-purple-600/10 rounded-xl outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-purple-600 text-xs font-bold font-inter">{{ $staff['initial'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-zinc-900 text-sm font-bold font-inter truncate">{{ $staff['name'] }}</span>
                                <span class="flex-shrink-0 px-1.5 py-0.5 bg-gray-100 rounded-lg text-gray-500 text-[8px] font-black font-inter uppercase tracking-wide">{{ $staff['role'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="{{ $scoreCol }} text-sm font-black font-inter w-6 flex-shrink-0">{{ $staff['score'] }}</span>
                                <div class="flex-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="{{ $barCol }} h-1 rounded-full" style="width:{{ $staff['score'] }}%"></div>
                                </div>
                                <span class="text-gray-400 text-[9px] font-bold font-inter flex-shrink-0">{{ $staff['responses'] }}</span>
                            </div>
                        </div>
                        <button onclick="openAnalyzeModal('{{ $staff['name'] }}','{{ $staff['initial'] }}','{{ $staff['role'] }}',{{ $staff['score'] }},'{{ $staff['responses'] }}')" class="flex-shrink-0 px-3 h-7 bg-gray-100 rounded-xl text-zinc-900 text-[9px] font-black font-inter uppercase tracking-wide hover:bg-purple-600 hover:text-white transition-all">
                            Analisis
                        </button>
                    </div>

                    {{-- Desktop layout: full grid --}}
                    <div class="hidden sm:grid grid-cols-[1fr_160px_240px_160px_160px] items-center px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-600/10 rounded-2xl outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-purple-600 text-xs font-bold font-inter">{{ $staff['initial'] }}</span>
                            </div>
                            <span class="text-zinc-900 text-sm font-bold font-inter leading-5">{{ $staff['name'] }}</span>
                        </div>
                        <div>
                            <span class="inline-flex px-2 py-1 bg-gray-100 rounded-xl text-gray-500 text-[9px] font-black font-inter uppercase leading-4 tracking-wide">{{ $staff['role'] }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="{{ $scoreCol }} text-lg font-black font-inter leading-7 w-8 flex-shrink-0">{{ $staff['score'] }}</span>
                            <div class="w-24 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="{{ $barCol }} h-1.5 rounded-full" style="width:{{ $staff['score'] }}%"></div>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-[10px] font-black font-inter uppercase leading-4 tracking-wide opacity-40">{{ $staff['responses'] }}</span>
                        </div>
                        <div class="flex justify-end">
                            <button onclick="openAnalyzeModal('{{ $staff['name'] }}','{{ $staff['initial'] }}','{{ $staff['role'] }}',{{ $staff['score'] }},'{{ $staff['responses'] }}')" class="px-6 h-9 bg-gray-100 rounded-2xl outline outline-1 outline-zinc-100 text-zinc-900 text-[10px] font-black font-inter uppercase leading-4 tracking-wide hover:bg-purple-600 hover:text-white hover:outline-purple-600 transition-all active:scale-[0.98]">
                                Analisis
                            </button>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>

        {{-- Evaluate Owner card (shown on Owner filter) --}}
        @if($canManageAudit || $activeAuditOwner)
        <div id="evaluate-owner-card" style="display:none"
             class="bg-purple-600/5 rounded-[40px] outline outline-1 outline-purple-600/10">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 px-8 py-8">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-purple-600/10 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shield" class="w-8 h-8 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-zinc-900 text-xl font-bold font-inter leading-7 mb-1">Evaluasi Semua Owner</p>
                        <p class="text-gray-500 text-xs font-semibold font-inter leading-4 opacity-70">
                            Status: <span class="font-black {{ $activeAuditOwner ? 'text-emerald-600' : 'text-rose-500' }}">{{ $activeAuditOwner ? 'Dibuka' : 'Dikunci' }}</span> · Staf hanya bisa mengisi saat owner membuka evaluasi.
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    @if($canManageAudit)
                        @if($activeAuditOwner)
                            <button type="button" onclick="toggleAuditLock('owner', 'lock')"
                                    class="flex-shrink-0 px-6 h-12 bg-rose-500 rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-rose-600 active:scale-[0.98] transition-all shadow-[0px_10px_15px_-3px_rgba(244,63,94,0.20)]">
                                Kunci Evaluasi
                            </button>
                        @else
                            <button type="button" onclick="toggleAuditLock('owner', 'unlock')"
                                    class="flex-shrink-0 px-6 h-12 bg-emerald-600 rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-emerald-700 active:scale-[0.98] transition-all shadow-[0px_10px_15px_-3px_rgba(16,185,129,0.20)]">
                                Buka Evaluasi
                            </button>
                        @endif
                    @endif

                    <button type="button" onclick="launchAudit('owner')"
                            class="flex-shrink-0 px-8 h-12 {{ $activeAuditOwner ? 'bg-purple-600 hover:bg-purple-700 active:scale-[0.98]' : 'bg-gray-300 cursor-not-allowed' }} rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-wide transition-all shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.20)]">
                        {{ $alreadySubmitted['owner'] ? 'Edit Evaluasi' : 'Mulai Evaluasi' }}
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- Evaluate Staff card (shown on Staff filter) --}}
        @if($canManageAudit || $activeAuditStaff)
        <div id="evaluate-staff-card" style="display:none"
             class="bg-purple-600/5 rounded-[40px] outline outline-1 outline-purple-600/10">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 px-8 py-8">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-white rounded-2xl shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] outline outline-1 outline-purple-600/10 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="users" class="w-8 h-8 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-zinc-900 text-xl font-bold font-inter leading-7 mb-1">Evaluasi Semua Staf</p>
                        <p class="text-gray-500 text-xs font-semibold font-inter leading-4 opacity-70">
                            Status: <span class="font-black {{ $activeAuditStaff ? 'text-emerald-600' : 'text-rose-500' }}">{{ $activeAuditStaff ? 'Dibuka' : 'Dikunci' }}</span> · Staf hanya bisa mengisi saat owner membuka evaluasi.
                        </p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-2">
                    @if($canManageAudit)
                        @if($activeAuditStaff)
                            <button type="button" onclick="toggleAuditLock('staff', 'lock')"
                                    class="flex-shrink-0 px-6 h-12 bg-rose-500 rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-rose-600 active:scale-[0.98] transition-all shadow-[0px_10px_15px_-3px_rgba(244,63,94,0.20)]">
                                Kunci Evaluasi
                            </button>
                        @else
                            <button type="button" onclick="toggleAuditLock('staff', 'unlock')"
                                    class="flex-shrink-0 px-6 h-12 bg-emerald-600 rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-wide hover:bg-emerald-700 active:scale-[0.98] transition-all shadow-[0px_10px_15px_-3px_rgba(16,185,129,0.20)]">
                                Buka Evaluasi
                            </button>
                        @endif
                    @endif

                    <button type="button" onclick="launchAudit('staff')"
                            class="flex-shrink-0 px-8 h-12 {{ $activeAuditStaff ? 'bg-purple-600 hover:bg-purple-700 active:scale-[0.98]' : 'bg-gray-300 cursor-not-allowed' }} rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-wide transition-all shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.20)]">
                        {{ $alreadySubmitted['staff'] ? 'Edit Evaluasi' : 'Mulai Evaluasi' }}
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ============================================================ --}}
    {{-- PANEL: EDIT METRICS --}}
    {{-- ============================================================ --}}
    <div id="panel-metrics" style="display:none" class="flex-col gap-6">

        {{-- Toolbar: Sub-toggle + Add Metric button --}}
        <div class="flex items-center justify-between gap-2">
            {{-- Staff / Owner sub-toggle --}}
            <div class="flex items-center h-9 p-1 bg-gray-100/50 rounded-2xl outline outline-1 outline-zinc-100 min-w-0">
                <button id="sub-staff" onclick="switchMetricTab('staff')"
                        class="h-7 px-3 sm:px-6 rounded-xl bg-white shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] text-purple-600 text-[9px] sm:text-[10px] font-bold font-inter uppercase leading-4 tracking-wide transition-all whitespace-nowrap">
                    Metrik Staf
                </button>
                <button id="sub-owner" onclick="switchMetricTab('owner')"
                        class="h-7 px-3 sm:px-6 rounded-xl text-gray-500 text-[9px] sm:text-[10px] font-bold font-inter uppercase leading-4 tracking-wide transition-all whitespace-nowrap">
                    Metrik Owner
                </button>
            </div>
            {{-- Add Metric --}}
            <button onclick="openMetricModal('new')"
                    class="flex-shrink-0 flex items-center gap-1.5 px-3 sm:px-5 h-9 bg-purple-600 rounded-2xl text-white text-[9px] sm:text-[10px] font-bold font-inter uppercase leading-4 tracking-wide shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20),0px_10px_15px_-3px_rgba(147,51,234,0.20)] hover:opacity-90 active:scale-[0.98] transition-all whitespace-nowrap">
                <i data-lucide="plus" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                Tambah Metrik
            </button>
        </div>

        {{-- Metric Cards Grid --}}
        <div id="metric-cards-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            {{-- Populated by JS --}}
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: New / Edit Metric (employees-style with warning) --}}
    {{-- ============================================================ --}}
    @push('modals')

    {{-- NEW METRIC MODAL --}}
    <div id="new-metric-backdrop" data-modal-backdrop data-modal-panel="new-metric-panel"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm hidden"
         onclick="if(event.target===this) closePanel(this, document.getElementById('new-metric-panel'))">
        <div id="new-metric-panel"
             class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl px-7 pt-7 pb-7 flex flex-col gap-5 opacity-0 scale-95 transition-all duration-200 max-h-[90vh] overflow-y-auto">

            {{-- Header --}}
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-1.5 mb-2">
                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-purple-600"></i>
                        <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Aksi Administratif</span>
                    </div>
                    <h2 class="text-zinc-900 text-2xl font-bold font-mono leading-7 mb-1">Pertanyaan Evaluasi Baru</h2>
                    <p class="text-gray-400 text-xs font-normal font-mono leading-5">Tentukan kriteria utama untuk penilaian staf.</p>
                </div>
                <button onclick="closePanel(document.getElementById('new-metric-backdrop'), document.getElementById('new-metric-panel'))"
                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 ml-4 mt-1">
                    <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
                </button>
            </div>

            {{-- Form --}}
            <div class="flex flex-col gap-3.5">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Pertanyaan Metrik</label>
                    <div class="relative">
                        <i data-lucide="message-square" class="absolute left-4 top-4 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <textarea id="new-metric-question" rows="4" placeholder="Masukkan kriteria kinerja..."
                                  class="w-full pl-11 pr-4 py-3.5 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all resize-none"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Kategori</label>
                        <div class="relative">
                            <i data-lucide="layout-grid" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                            <select id="new-metric-tag"
                                    class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                                <option value="Technical">Teknis</option>
                                <option value="Efficiency">Efisiensi</option>
                                <option value="Hygiene">Kebersihan</option>
                                <option value="Precision">Ketelitian</option>
                                <option value="Communication">Komunikasi</option>
                                <option value="Performance">Kinerja</option>
                                <option value="Knowledge">Pengetahuan</option>
                                <option value="Teamwork">Kerja Sama Tim</option>
                                <option value="Service">Layanan</option>
                                <option value="Leadership">Kepemimpinan</option>
                                <option value="Support">Dukungan</option>
                                <option value="Transparency">Transparansi</option>
                                <option value="Accountability">Akuntabilitas</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Target Peran</label>
                        <div class="relative">
                            <i data-lucide="shield" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                            <select id="new-metric-role"
                                    class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                                <option value="staff">Staf</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button onclick="saveMetric('new')"
                        class="w-full h-14 mt-1 bg-purple-600 rounded-2xl flex items-center justify-center gap-2 text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.35)]">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Simpan Metrik
                </button>
            </div>
        </div>
    </div>

    {{-- EDIT METRIC MODAL --}}
    <div id="edit-metric-backdrop" data-modal-backdrop data-modal-panel="edit-metric-panel"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm hidden"
         onclick="if(event.target===this) closePanel(this, document.getElementById('edit-metric-panel'))">
        <div id="edit-metric-panel"
             class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl px-7 pt-7 pb-7 flex flex-col gap-5 opacity-0 scale-95 transition-all duration-200 max-h-[90vh] overflow-y-auto">

            {{-- Header --}}
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-1.5 mb-2">
                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-purple-600"></i>
                        <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wider">Aksi Administratif</span>
                    </div>
                    <h2 class="text-zinc-900 text-2xl font-bold font-mono leading-7 mb-1">Edit Pertanyaan Evaluasi</h2>
                    <p class="text-gray-400 text-xs font-normal font-mono leading-5">Perbarui kriteria kinerja untuk penilaian staf.</p>
                </div>
                <button onclick="closePanel(document.getElementById('edit-metric-backdrop'), document.getElementById('edit-metric-panel'))"
                        class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 ml-4 mt-1">
                    <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
                </button>
            </div>

            {{-- Warning banner --}}
            <div class="flex items-start gap-3 px-4 py-3.5 bg-amber-50 rounded-2xl outline outline-1 outline-amber-100">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-amber-700 text-xs font-bold font-inter leading-4 mb-0.5">Mengedit metrik aktif</p>
                    <p class="text-amber-600 text-[11px] font-inter leading-4">Perubahan akan langsung memengaruhi semua evaluasi yang sedang berjalan pada siklus ini.</p>
                </div>
            </div>

            {{-- Form --}}
            <div class="flex flex-col gap-3.5">
                <div class="flex flex-col gap-1">
                    <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Pertanyaan Metrik</label>
                    <div class="relative">
                        <i data-lucide="message-square" class="absolute left-4 top-4 w-4 h-4 text-gray-300 pointer-events-none"></i>
                        <textarea id="edit-metric-question" rows="4" placeholder="Masukkan kriteria kinerja..."
                                  class="w-full pl-11 pr-4 py-3.5 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter placeholder:text-gray-300 focus:outline-2 focus:outline-purple-400 transition-all resize-none"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Kategori</label>
                        <div class="relative">
                            <i data-lucide="layout-grid" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                            <select id="edit-metric-tag"
                                    class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                                <option value="Technical">Teknis</option>
                                <option value="Efficiency">Efisiensi</option>
                                <option value="Hygiene">Kebersihan</option>
                                <option value="Precision">Ketelitian</option>
                                <option value="Communication">Komunikasi</option>
                                <option value="Performance">Kinerja</option>
                                <option value="Knowledge">Pengetahuan</option>
                                <option value="Teamwork">Kerja Sama Tim</option>
                                <option value="Service">Layanan</option>
                                <option value="Leadership">Kepemimpinan</option>
                                <option value="Support">Dukungan</option>
                                <option value="Transparency">Transparansi</option>
                                <option value="Accountability">Akuntabilitas</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-zinc-900 text-[10px] font-bold font-inter uppercase leading-4 tracking-wider">Target Peran</label>
                        <div class="relative">
                            <i data-lucide="shield" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"></i>
                            <select id="edit-metric-role"
                                    class="w-full h-12 pl-11 pr-4 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-sm font-inter appearance-none focus:outline-2 focus:outline-purple-400 transition-all">
                                <option value="staff">Staf</option>
                                <option value="owner">Owner</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button onclick="saveMetric('edit')"
                        class="w-full h-14 mt-1 bg-purple-600 rounded-2xl flex items-center justify-center gap-2 text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_8px_30px_rgba(147,51,234,0.35)]">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    {{-- DELETE METRIC CONFIRM MODAL --}}
    <div id="del-metric-backdrop" data-modal-backdrop data-modal-panel="del-metric-panel"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30 backdrop-blur-sm hidden"
         onclick="if(event.target===this) closePanel(this, document.getElementById('del-metric-panel'))">
        <div id="del-metric-panel"
             class="relative w-full max-w-xs bg-white rounded-[32px] shadow-2xl px-8 pt-8 pb-8 flex flex-col items-center gap-5 text-center opacity-0 scale-95 transition-all duration-200">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center">
                <i data-lucide="trash-2" class="w-6 h-6 text-rose-500"></i>
            </div>
            <div>
                <h3 class="text-zinc-900 text-lg font-bold font-mono mb-1">Hapus Metrik</h3>
                <p class="text-gray-500 text-sm font-inter leading-5">
                    Hapus <span id="del-metric-label" class="font-bold text-zinc-900"></span> dari siklus evaluasi?<br>Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="flex gap-3 w-full">
                <button onclick="closePanel(document.getElementById('del-metric-backdrop'), document.getElementById('del-metric-panel'))"
                        class="flex-1 h-11 bg-white rounded-2xl outline outline-1 outline-zinc-200 text-zinc-900 text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-zinc-50 transition-colors">
                    Batal
                </button>
                <button onclick="confirmDeleteMetric()"
                        class="flex-1 h-11 bg-rose-500 rounded-2xl text-white text-xs font-bold font-inter uppercase leading-4 tracking-wide hover:bg-rose-600 active:scale-[0.98] transition-all">
                    Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: CONFIDENTIAL ANALYSIS                                 --}}
    {{-- ============================================================ --}}
    <div id="analyze-backdrop"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-6 bg-black/40 backdrop-blur-sm hidden"
         onclick="if(event.target===this) closeAnalyzeModal()">
        <div id="analyze-panel"
             class="relative w-full sm:max-w-lg bg-white sm:rounded-[48px] rounded-t-[40px] shadow-2xl flex flex-col opacity-0 scale-95 sm:scale-95 translate-y-4 sm:translate-y-0 transition-all duration-200 max-h-[90vh] sm:max-h-[85vh] overflow-hidden">

            {{-- Drag handle (mobile only) --}}
            <div class="flex sm:hidden justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 bg-zinc-200 rounded-full"></div>
            </div>

            {{-- ── Sticky Header ── --}}
            <div class="flex items-start gap-4 sm:gap-6 px-6 sm:px-12 pt-5 sm:pt-12 pb-5 sm:pb-6 border-b border-zinc-100 flex-shrink-0">
                <div id="an-avatar" class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-600/10 rounded-2xl outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                    <span id="an-initial" class="text-purple-600 text-sm sm:text-lg font-bold font-inter">J</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p id="an-name" class="text-zinc-900 text-xl sm:text-3xl font-bold font-inter leading-tight truncate">John Martinez</p>
                    <p class="text-gray-500 text-[9px] sm:text-[10px] font-bold font-inter uppercase leading-4 tracking-widest mt-1 opacity-60">Analitik Agregat Rahasia</p>
                </div>
                <button onclick="closeAnalyzeModal()"
                        class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-zinc-100 transition-colors flex-shrink-0 mt-0.5">
                    <i data-lucide="x" class="w-4 h-4 text-gray-400"></i>
                </button>
            </div>

            {{-- ── Scrollable Body ── --}}
            <div class="overflow-y-auto flex-1 px-6 sm:px-12 py-6 sm:py-8 flex flex-col gap-4">

                {{-- Consolidated Rating Card --}}
                <div class="w-full bg-purple-600/5 rounded-[32px] sm:rounded-[40px] outline outline-1 outline-purple-600/10 px-8 sm:px-10 py-7 flex flex-col gap-3">
                    <p class="text-purple-600 text-[10px] font-black font-inter uppercase leading-4 tracking-widest">Rating Konsolidasi</p>
                    <div class="flex items-center gap-4">
                        <span id="an-score" class="text-purple-600 text-6xl sm:text-7xl font-black font-inter leading-none">80</span>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-600 rounded-2xl flex items-center justify-center">
                            <i data-lucide="star" class="w-5 h-5 sm:w-6 sm:h-6 text-white fill-white"></i>
                        </div>
                    </div>
                    <p id="an-entries" class="text-purple-600/60 text-xs font-bold font-inter leading-4">Berdasarkan 6 entri anonim</p>
                </div>

                {{-- Stats Row --}}
                <div class="w-full bg-gray-100/20 rounded-[32px] sm:rounded-[40px] outline outline-1 outline-zinc-100 px-8 sm:px-10 py-6 flex flex-col gap-5">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/70 rounded-2xl outline outline-1 outline-zinc-100 px-4 pt-4 pb-3">
                            <p class="text-gray-500 text-[10px] font-black font-inter uppercase leading-4 tracking-wide mb-1.5">Total Masukan</p>
                            <p id="an-hits" class="text-zinc-900 text-xl font-black font-inter leading-7">6</p>
                        </div>
                        <div class="bg-emerald-50 rounded-2xl outline outline-1 outline-emerald-100 px-4 pt-4 pb-3">
                            <p class="text-emerald-600 text-[10px] font-black font-inter uppercase leading-4 tracking-wide mb-1.5">Status</p>
                            <p id="an-status" class="text-emerald-700 text-xl font-black font-inter leading-7">Sinkron</p>
                        </div>
                    </div>

                    {{-- Per-Metric Breakdown --}}
                    <div id="an-metrics" class="flex flex-col gap-2">
                        {{-- Populated by JS --}}
                    </div>

                    {{-- Close button --}}
                    <button onclick="closeAnalyzeModal()"
                            class="w-full h-12 sm:h-14 bg-purple-600 rounded-3xl shadow-[0px_8px_10px_-6px_rgba(147,51,234,0.20),0px_20px_25px_-5px_rgba(147,51,234,0.20)] text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all">
                        Tutup Analisis Rahasia
                    </button>
                </div>

            </div>
        </div>
    </div>

    @endpush

    {{-- ============================================================ --}}
    {{-- PANEL: AUDIT FORM (hidden until Launch is clicked) --}}
    {{-- ============================================================ --}}
    <div id="panel-audit" style="display:none" class="flex-col gap-8">

        {{-- Active Audit Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-5 bg-purple-600/5 rounded-[40px] outline outline-1 outline-purple-600/10">
            <div class="flex items-center gap-4">
                <button onclick="closeAudit()"
                        class="w-10 h-10 bg-white rounded-2xl outline outline-1 outline-purple-600/10 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] flex items-center justify-center flex-shrink-0 hover:bg-purple-600/5 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-purple-600"></i>
                </button>
                <div>
                    <p id="audit-title" class="text-purple-600 text-base font-bold font-inter leading-6">Audit Manajemen Aktif</p>
                    <p class="text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide opacity-60">Anonim · Skala: 10-100</p>
                </div>
            </div>
            <div class="flex items-center gap-2 px-3 h-7 bg-white/70 rounded-2xl outline outline-1 outline-purple-600/10">
                <i data-lucide="shield" class="w-3.5 h-3.5 text-purple-600 opacity-50"></i>
                <span class="text-purple-600 text-[9px] font-black font-inter uppercase leading-4 tracking-wide opacity-60">Pelindung Identitas Aktif</span>
            </div>
        </div>

        {{-- Metrics Form --}}
        <form id="audit-form" method="POST" action="{{ route('owner.evaluasi.submit_audit') }}" class="flex flex-col gap-10">
            @csrf
            <input type="hidden" name="scores" id="scores-input" value="">

            @foreach($metricsView as $metric)
            <div class="audit-metric-block flex flex-col gap-4" data-target-role="{{ $metric['target_role'] }}">
                <div>
                    <p class="text-purple-600 text-[10px] font-black font-inter uppercase leading-4 tracking-wide mb-1">{{ $metric['label'] }}</p>
                    <p class="text-zinc-900 text-base font-semibold font-inter leading-6 italic">{{ $metric['desc'] }}</p>
                </div>

                {{-- Rating card — persons injected by JS based on group --}}
                <div class="bg-white rounded-[32px] outline outline-1 outline-zinc-100 shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] overflow-hidden">
                    <div class="divide-y divide-zinc-100" id="persons-{{ $metric['id'] }}">
                        {{-- Populated by JS --}}
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Submit bar --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-8 py-6 bg-purple-600/5 rounded-[32px] outline outline-1 outline-purple-600/10">
                <div>
                    <p class="text-zinc-900 text-base font-bold font-inter leading-6">Siap dikirim?</p>
                    <p class="text-gray-500 text-sm font-normal font-inter leading-5 opacity-70">Pastikan semua metrik sudah diberi nilai sebelum dikirim.</p>
                </div>
                <div class="flex items-center gap-4">
                    <span id="submit-progress" class="text-gray-400 text-[10px] font-black font-inter uppercase leading-4 tracking-wide whitespace-nowrap">
                        0 / 0 dinilai
                    </span>
                    <button type="submit"
                            class="flex items-center gap-2 px-8 h-12 bg-purple-600 rounded-2xl text-white text-xs font-black font-inter uppercase leading-4 tracking-widest hover:bg-purple-700 active:scale-[0.98] transition-all shadow-[0px_10px_15px_-3px_rgba(147,51,234,0.20)]">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span id="audit-submit-text">Kirim Evaluasi</span>
                    </button>
                </div>
            </div>
        </form>

    </div>

</div>
@endsection

@push('scripts')
<script>
// =====================================================================
// DATA DARI CONTROLLER
// =====================================================================
const CSRF_TOKEN = @json(csrf_token());

const OWNERS = @json($ownersForJs);
const STAFF  = @json($staffForJs);

const SCOREBOARD_DATA = @json($staffData);
const METRICS = @json($metricsView);

const SYSTEM_INDEX = Number(@json($systemIndex));
const RESPONSE_VOLUME = Number(@json($responseVolume));

let AUDIT_LOCK_STATE = {
    owner: Boolean(@json($activeAuditOwner ?? false)),
    staff: Boolean(@json($activeAuditStaff ?? false)),
};

const ALREADY_SUBMITTED = @json($alreadySubmitted ?? ['owner' => false, 'staff' => false]);
const EXISTING_EVALUATION_SCORES = @json($myEvaluationScores ?? ['owner' => [], 'staff' => []]);
const CURRENT_KARYAWAN_ID = @json($currentKaryawanId ?? null);
const CAN_MANAGE_AUDIT = Boolean(@json($canManageAudit ?? false));
const AUDIT_VISIBLE_FOR_USER = {
    owner: CAN_MANAGE_AUDIT || AUDIT_LOCK_STATE.owner,
    staff: CAN_MANAGE_AUDIT || AUDIT_LOCK_STATE.staff,
};

const LAUNCH_AUDIT_URL  = @json(route('owner.evaluasi.launch'));
const STORE_METRIC_URL  = @json(route('owner.evaluasi.metrics.store'));
const UPDATE_METRIC_URL = @json(route('owner.evaluasi.metrics.update', ['id' => '__ID__']));
const DELETE_METRIC_URL = @json(route('owner.evaluasi.metrics.destroy', ['id' => '__ID__']));

const RATING_STEPS = [10,20,30,40,50,60,70,80,90,100];

let currentGroup = null;
let ratedMap = {};
let totalRequired = 0;
let metricActiveTab = 'staff';
let editingMetricId = null;
let pendingDeleteMetricId = null;

// =====================================================================
// TAB SWITCHER
// =====================================================================
function switchTab(tab) {
    const isResults = tab === 'results';

    show('panel-results', isResults ? 'flex' : 'none');
    show('panel-metrics', isResults ? 'none' : 'flex');
    show('panel-audit', 'none');

    if (isResults) {
        show('stat-cards', 'flex');
        show('filter-bar', 'flex');
    }

    setTabActive('tab-results', isResults);
    setTabActive('tab-metrics', !isResults);

    if (!isResults) renderMetricCards();

    createLucideIcons();
}

// =====================================================================
// FILTER VIEW
// =====================================================================
function filterView(group) {
    ['all','owner','staff'].forEach(g => setFilterActive('filter-' + g, g === group));

    show('panel-audit', 'none');

    if (group === 'all') {
        show('scoreboard-table', '');
        show('evaluate-owner-card', 'none');
        show('evaluate-staff-card', 'none');
    } else if (group === 'owner') {
        if (!AUDIT_VISIBLE_FOR_USER.owner) {
            show('scoreboard-table', '');
            show('evaluate-owner-card', 'none');
            show('evaluate-staff-card', 'none');
            return;
        }
        show('scoreboard-table', 'none');
        show('evaluate-owner-card', 'block');
        show('evaluate-staff-card', 'none');
    } else {
        if (!AUDIT_VISIBLE_FOR_USER.staff) {
            show('scoreboard-table', '');
            show('evaluate-owner-card', 'none');
            show('evaluate-staff-card', 'none');
            return;
        }
        show('scoreboard-table', 'none');
        show('evaluate-owner-card', 'none');
        show('evaluate-staff-card', 'block');
    }

    createLucideIcons();
}

// =====================================================================
// EDIT METRICS — RENDER DARI DATABASE
// =====================================================================
function getMetricsForRole(role) {
    return METRICS.filter(m => String(m.target_role || m.role || 'staff').toLowerCase() === String(role).toLowerCase());
}

function renderMetricCards() {
    const grid = document.getElementById('metric-cards-grid');
    if (!grid) return;

    const visibleMetrics = getMetricsForRole(metricActiveTab);

    if (!visibleMetrics.length) {
        grid.innerHTML = `
            <div class="col-span-full bg-white rounded-[28px] outline outline-1 outline-zinc-100 px-6 py-8 text-center">
                <p class="text-zinc-900 text-sm font-bold font-inter">Belum ada metrik evaluasi untuk peran ${escapeHtml(metricActiveTab)}.</p>
                <p class="text-gray-400 text-xs font-inter mt-1">Klik Tambah Metrik untuk menambahkan pertanyaan evaluasi.</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = visibleMetrics.map(m => `
        <div class="bg-white rounded-[28px] outline outline-1 outline-zinc-100 overflow-hidden flex hover:shadow-[0_4px_20px_rgba(147,51,234,0.08)] transition-shadow">
            <div class="w-1 flex-shrink-0 bg-purple-600/20"></div>
            <div class="flex-1 px-6 py-6 flex flex-col gap-4">
                <span class="inline-flex px-2.5 py-1 bg-purple-600/5 border border-purple-600/10 rounded-xl text-purple-600 text-[9px] font-black font-inter uppercase leading-3 tracking-wide w-fit">${escapeHtml(m.tag)}</span>
                <p class="text-zinc-900 text-sm font-bold font-inter leading-5">"${escapeHtml(m.question)}"</p>
                <div class="flex gap-2 mt-1">
                    <button type="button" onclick="openMetricModal('edit', '${m.id}')"
                            class="flex items-center gap-1.5 px-3 h-8 rounded-xl bg-purple-600/5 hover:bg-purple-600/10 transition-colors">
                        <i data-lucide="pencil" class="w-3.5 h-3.5 text-purple-600"></i>
                        <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4">Edit</span>
                    </button>
                    <button type="button" onclick="openDeleteMetricModal('${m.id}', '${escapeJs(m.tag)}')"
                            class="flex items-center gap-1.5 px-3 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 transition-colors">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-rose-500"></i>
                        <span class="text-rose-500 text-[10px] font-bold font-inter uppercase leading-4">Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    createLucideIcons();
}

function switchMetricTab(tab) {
    metricActiveTab = tab;

    const onStaff = tab === 'staff';
    const sStaff = document.getElementById('sub-staff');
    const sOwner = document.getElementById('sub-owner');

    if (sStaff && sOwner) {
        const on = ['bg-white','text-purple-600','shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]'];
        const off = ['text-gray-500'];

        if (onStaff) {
            sStaff.classList.add(...on); sStaff.classList.remove(...off);
            sOwner.classList.remove(...on); sOwner.classList.add(...off);
        } else {
            sOwner.classList.add(...on); sOwner.classList.remove(...off);
            sStaff.classList.remove(...on); sStaff.classList.add(...off);
        }
    }

    renderMetricCards();
}

// =====================================================================
// METRIC MODAL + CRUD KE CONTROLLER
// =====================================================================
function openMetricModal(mode, id = null) {
    editingMetricId = id;

    if (mode === 'new') {
        document.getElementById('new-metric-question').value = '';
        document.getElementById('new-metric-tag').value = 'Technical';
        const roleEl = document.getElementById('new-metric-role');
        if (roleEl) roleEl.value = metricActiveTab || 'staff';

        openPanel(document.getElementById('new-metric-backdrop'), document.getElementById('new-metric-panel'));
    } else {
        const metric = METRICS.find(item => String(item.id) === String(id));
        if (!metric) return;

        document.getElementById('edit-metric-question').value = metric.question;
        document.getElementById('edit-metric-tag').value = metric.tag;
        const roleEl = document.getElementById('edit-metric-role');
        if (roleEl) roleEl.value = metric.target_role || metric.role || 'staff';

        openPanel(document.getElementById('edit-metric-backdrop'), document.getElementById('edit-metric-panel'));
    }

    createLucideIcons();
}

async function saveMetric(mode) {
    const questionEl = document.getElementById(mode === 'new' ? 'new-metric-question' : 'edit-metric-question');
    const tagEl = document.getElementById(mode === 'new' ? 'new-metric-tag' : 'edit-metric-tag');
    const roleEl = document.getElementById(mode === 'new' ? 'new-metric-role' : 'edit-metric-role');

    const pertanyaan = questionEl.value.trim();
    const kategori = tagEl.value;
    const targetRole = roleEl ? roleEl.value : 'staff';

    if (!pertanyaan) {
        alert('Pertanyaan metrik wajib diisi.');
        return;
    }

    const url = mode === 'new'
        ? STORE_METRIC_URL
        : UPDATE_METRIC_URL.replace('__ID__', editingMetricId);

    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('pertanyaan', pertanyaan);
    formData.append('kategori', kategori);
    formData.append('target_role', targetRole);

    if (mode !== 'new') {
        formData.append('_method', 'PUT');
    }

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });

        if (!res.ok) throw new Error('Gagal menyimpan metric.');

        window.location.reload();
    } catch (err) {
        alert(err.message || 'Gagal menyimpan metric.');
    }
}

function openDeleteMetricModal(id, tag) {
    pendingDeleteMetricId = id;
    const label = document.getElementById('del-metric-label');
    if (label) label.textContent = tag;

    openPanel(document.getElementById('del-metric-backdrop'), document.getElementById('del-metric-panel'));
    createLucideIcons();
}

async function confirmDeleteMetric() {
    if (!pendingDeleteMetricId) return;

    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('_method', 'DELETE');

    try {
        const res = await fetch(DELETE_METRIC_URL.replace('__ID__', pendingDeleteMetricId), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });

        if (!res.ok) throw new Error('Gagal menghapus metric.');

        window.location.reload();
    } catch (err) {
        alert(err.message || 'Gagal menghapus metric.');
    }
}

// =====================================================================
// LOCK / UNLOCK AUDIT + BUILD FORM DARI CONTROLLER DATA
// =====================================================================
async function toggleAuditLock(group, action) {
    if (!CAN_MANAGE_AUDIT) {
        alert('Hanya owner yang bisa mengunci atau membuka evaluasi.');
        return;
    }

    try {
        const res = await fetch(LAUNCH_AUDIT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ role: group, action: action })
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok || data.success === false) {
            throw new Error(data.message || 'Gagal mengubah status evaluasi.');
        }

        if (typeof data.updated_rows !== 'undefined' && Number(data.updated_rows) === 0) {
            alert(data.message + '\n\nCatatan: 0 baris berubah. Biasanya karena status sudah sama, atau tidak ada soal dengan target_role tersebut.');
        }

        window.location.reload();
    } catch (err) {
        alert(err.message || 'Gagal mengubah status evaluasi.');
    }
}

function launchAudit(group) {
    if (!AUDIT_LOCK_STATE[group]) {
        alert('Evaluasi masih dikunci. Menu evaluasi hanya tampil ketika admin/owner membuka evaluasi.');
        return;
    }

    currentGroup = group;
    ratedMap = {};

    const rawPersons = group === 'owner' ? OWNERS : STAFF;
    const persons = rawPersons.filter(person => String(person.id_kry) !== String(CURRENT_KARYAWAN_ID));
    const metrics = getMetricsForRole(group);
    const existingScores = EXISTING_EVALUATION_SCORES[group] || {};

    if (!persons.length) {
        alert(group === 'owner' ? 'Tidak ada owner lain yang bisa dinilai.' : 'Data staff lain kosong.');
        return;
    }

    if (!metrics.length) {
        alert('Data metric evaluasi untuk role ' + group + ' kosong.');
        return;
    }

    totalRequired = metrics.length * persons.length;

    document.querySelectorAll('.audit-metric-block').forEach(block => {
        const targetRole = String(block.dataset.targetRole || 'staff').toLowerCase();
        block.style.display = targetRole === String(group).toLowerCase() ? 'flex' : 'none';
    });

    METRICS.forEach(metric => {
        const container = document.getElementById('persons-' + metric.id);
        if (container) container.innerHTML = '';
    });

    metrics.forEach(metric => {
        const container = document.getElementById('persons-' + metric.id);
        if (!container) return;

        persons.forEach(person => {
            const key = metric.id + '-' + person.id_kry;
            const existing = existingScores[key] || null;

            if (existing && existing.score !== undefined && existing.score !== null) {
                ratedMap[key] = {
                    personId: String(person.id_kry),
                    score: Number(existing.score)
                };
            }

            container.insertAdjacentHTML('beforeend', buildPersonRow(metric.id, person, existing ? existing.score : null));
        });
    });

    updateProgress();

    show('evaluate-owner-card', 'none');
    show('evaluate-staff-card', 'none');
    show('scoreboard-table', 'none');
    show('stat-cards', 'none');
    show('filter-bar', 'none');
    show('panel-audit', 'flex');

    document.getElementById('audit-title').textContent =
        group === 'owner' ? 'Audit Manajemen Aktif' : 'Audit Staf Aktif';

    const submitText = document.getElementById('audit-submit-text');
    if (submitText) submitText.textContent = ALREADY_SUBMITTED[group] ? 'Perbarui Evaluasi' : 'Kirim Evaluasi';

    document.getElementById('panel-audit').scrollIntoView({ behavior: 'smooth', block: 'start' });
    createLucideIcons();
}

function buildPersonRow(metricId, person, selectedScore = null) {
    const personId = person.id_kry;

    const buttons = RATING_STEPS.map(val => {
        const isSelected = Number(selectedScore) === Number(val);
        const selectedClass = 'bg-purple-600 text-white shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20)]';
        const normalClass = 'bg-gray-100 hover:bg-purple-600/10 text-zinc-900/60 hover:text-purple-600';

        return `
            <button type="button"
                onclick="selectRating(this, '${metricId}', '${personId}', ${val})"
                data-metric="${metricId}"
                data-person="${personId}"
                data-value="${val}"
                class="rating-btn h-10 rounded-2xl ${isSelected ? selectedClass : normalClass} text-xs font-black font-inter leading-4 transition-all active:scale-95 outline outline-1 outline-transparent hover:outline-purple-600/20">
                ${val}
            </button>
        `;
    }).join('');

    return `
        <div class="px-6 py-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-purple-600/10 rounded-2xl outline outline-1 outline-zinc-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-purple-600 text-xs font-bold font-inter">${escapeHtml(person.initial)}</span>
                </div>
                <div>
                    <p class="text-zinc-900 text-sm font-bold font-inter leading-5">${escapeHtml(person.name)}</p>
                    <p class="text-gray-400 text-[9px] font-black font-inter uppercase leading-3 tracking-wide">${escapeHtml(person.role)}</p>
                </div>
            </div>

            <p class="text-gray-400 text-[9px] font-black font-inter uppercase leading-3 tracking-wide mb-3 opacity-70">Beri Nilai (10-100)</p>

            <div class="grid grid-cols-5 sm:grid-cols-10 gap-1.5">
                ${buttons}
            </div>
        </div>
    `;
}

function selectRating(btn, metricId, personId, value) {
    const key = metricId + '-' + personId;

    document.querySelectorAll(`[data-metric="${metricId}"][data-person="${personId}"]`).forEach(b => {
        b.classList.remove('bg-purple-600','text-white','shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20)]');
        b.classList.add('bg-gray-100','text-zinc-900/60');
    });

    btn.classList.remove('bg-gray-100','text-zinc-900/60');
    btn.classList.add('bg-purple-600','text-white','shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20)]');

    ratedMap[key] = {
        personId: personId,
        score: Number(value)
    };

    updateProgress();
}

function updateProgress() {
    const count = Object.keys(ratedMap).length;
    const el = document.getElementById('submit-progress');
    if (el) el.textContent = count + ' / ' + totalRequired + ' dinilai';
}

function closeAudit() {
    show('panel-audit', 'none');
    show('stat-cards', 'flex');
    show('filter-bar', 'flex');

    if (currentGroup === 'owner') {
        show('evaluate-owner-card', 'block');
    } else {
        show('evaluate-staff-card', 'block');
    }

    createLucideIcons();
}

// =====================================================================
// SUBMIT KE CONTROLLER submitEvaluation()
// =====================================================================
const auditForm = document.getElementById('audit-form');

if (auditForm) {
    auditForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const count = Object.keys(ratedMap).length;

        if (count < totalRequired) {
            alert('Mohon beri nilai pada semua ' + totalRequired + ' item sebelum mengirim (' + count + ' / ' + totalRequired + ' selesai).');
            return;
        }

        document.getElementById('scores-input').value = JSON.stringify(ratedMap);
        this.submit();
    });
}

// =====================================================================
// ANALYZE MODAL
// =====================================================================
function openAnalyzeModal(nameOrMode, initial, role, score, responses) {
    const isSystem = nameOrMode === 'system';

    if (isSystem) {
        document.getElementById('an-name').textContent = 'Ringkasan Sistem';
        document.getElementById('an-initial').textContent = '★';
        document.getElementById('an-score').textContent = SYSTEM_INDEX.toFixed(1);
        document.getElementById('an-hits').textContent = RESPONSE_VOLUME;
        document.getElementById('an-entries').textContent = `Agregat dari semua ${RESPONSE_VOLUME} entri selesai`;

        setAnalyzeStatus(SYSTEM_INDEX);
        document.getElementById('an-metrics').innerHTML = buildRoleBreakdown();
    } else {
        const personScore = Number(score || 0);
        const staff = SCOREBOARD_DATA.find(s =>
            (String(s.name || s.n_kry || '') === String(nameOrMode || '')) &&
            (String(s.role || '') === String(role || ''))
        );

        document.getElementById('an-name').textContent = staff ? (staff.name || staff.n_kry || '-') : (nameOrMode || '-');
        document.getElementById('an-initial').textContent = staff ? (staff.initial || '-') : (initial || '-');
        document.getElementById('an-score').textContent = (staff ? Number(staff.score || 0) : personScore).toFixed(1).replace('.0', '');

        const responseText = staff ? (staff.responses || '0 Respon') : (responses || '0 Respon');
        const hits = staff ? Number(staff.responses_count || parseInt(String(responseText).replace(/\D/g, ''), 10) || 0) : (responses ? String(responses).split(' ')[0] : '0');

        document.getElementById('an-hits').textContent = hits;
        document.getElementById('an-entries').textContent = `Berdasarkan ${responseText}`;

        setAnalyzeStatus(staff ? Number(staff.score || 0) : personScore);
        document.getElementById('an-metrics').innerHTML = buildMetricBreakdown(staff, role);
    }

    const bd = document.getElementById('analyze-backdrop');
    const pn = document.getElementById('analyze-panel');

    bd.classList.remove('hidden');

    requestAnimationFrame(() => requestAnimationFrame(() => {
        bd.style.opacity = '1';
        pn.classList.remove('opacity-0', 'scale-95', 'translate-y-4');
        pn.classList.add('opacity-100', 'scale-100', 'translate-y-0');
    }));

    document.body.style.overflow = 'hidden';
    createLucideIcons();
}

function buildMetricBreakdown(staff, fallbackRole = '-') {
    const roleLabel = staff ? (staff.role || fallbackRole || '-') : (fallbackRole || '-');
    const metrics = staff && Array.isArray(staff.metrics) ? staff.metrics : [];

    if (!metrics.length) {
        return `
            <p class="text-zinc-900/40 text-[9px] font-black font-inter uppercase leading-3 tracking-widest mb-1">Rincian Metrik</p>
            <div class="text-gray-500 text-xs font-inter leading-5 bg-white/70 rounded-2xl outline outline-1 outline-zinc-100 px-4 py-3">
                Detail skor per metrik belum tersedia untuk karyawan ini. Pastikan controller mengirim field <b>metrics</b> di dalam <b>staffData</b>.
            </div>
        `;
    }

    const rows = metrics.map(m => {
        const rawScore = m.score;
        const hasScore = rawScore !== null && rawScore !== undefined && rawScore !== '';
        const val = hasScore ? Number(rawScore) : 0;
        const color = scoreBarClass(val);
        const text = scoreTextClass(val);
        const label = m.metric || m.question || '-';
        const responseInfo = m.responses !== undefined ? ` · ${m.responses} masukan` : '';

        return `
            <div class="flex items-center gap-3 py-1">
                <span class="text-zinc-900/50 text-[9px] font-black font-inter uppercase leading-3 tracking-wide w-24 flex-shrink-0 truncate" title="${escapeHtml(label)}">${escapeHtml(label)}</span>
                <div class="flex-1 h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                    <div class="${color} h-1.5 rounded-full" style="width:${Math.max(0, Math.min(100, val))}%"></div>
                </div>
                <span class="${text} text-[10px] font-black font-inter w-12 flex-shrink-0 text-right">${hasScore ? val.toFixed(1).replace('.0', '') : '-'}${responseInfo ? '' : ''}</span>
            </div>
        `;
    }).join('');

    return `
        <p class="text-zinc-900/40 text-[9px] font-black font-inter uppercase leading-3 tracking-widest mb-1">Rincian Metrik (${escapeHtml(roleLabel)})</p>
        ${rows}
    `;
}

function buildRoleBreakdown() {
    const groups = {};

    SCOREBOARD_DATA.forEach(item => {
        const role = item.role === 'owner' ? 'Owner' : 'Staf';
        if (!groups[role]) groups[role] = [];
        groups[role].push(Number(item.score || 0));
    });

    const rows = Object.entries(groups).map(([label, scores]) => {
        const avg = scores.length ? scores.reduce((a,b) => a + b, 0) / scores.length : 0;
        const val = Math.round(avg * 10) / 10;
        const color = scoreBarClass(val);
        const text = scoreTextClass(val);

        return `
            <div class="flex items-center gap-3 py-1">
                <span class="text-zinc-900/50 text-[9px] font-black font-inter uppercase leading-3 tracking-wide w-24 flex-shrink-0">${escapeHtml(label)}</span>
                <div class="flex-1 h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                    <div class="${color} h-1.5 rounded-full" style="width:${val}%"></div>
                </div>
                <span class="${text} text-[10px] font-black font-inter w-8 flex-shrink-0 text-right">${val}</span>
            </div>
        `;
    }).join('');

    return `
        <p class="text-zinc-900/40 text-[9px] font-black font-inter uppercase leading-3 tracking-widest mb-1">Rincian Grup Peran</p>
        ${rows || '<p class="text-gray-400 text-xs font-inter">Belum ada data evaluasi selesai.</p>'}
    `;
}

function setAnalyzeStatus(score) {
    const statusEl = document.getElementById('an-status');
    const status = score >= 85 ? 'Sinkron' : score >= 70 ? 'Perlu Ditinjau' : 'Berisiko';

    statusEl.textContent = status;

    statusEl.className = score >= 85
        ? 'text-emerald-700 text-xl font-black font-inter leading-7'
        : score >= 70
            ? 'text-amber-600 text-xl font-black font-inter leading-7'
            : 'text-rose-600 text-xl font-black font-inter leading-7';

    statusEl.parentElement.className = score >= 85
        ? 'bg-emerald-50 rounded-2xl outline outline-1 outline-emerald-100 px-4 pt-4 pb-3'
        : score >= 70
            ? 'bg-amber-50 rounded-2xl outline outline-1 outline-amber-100 px-4 pt-4 pb-3'
            : 'bg-rose-50 rounded-2xl outline outline-1 outline-rose-100 px-4 pt-4 pb-3';
}

function closeAnalyzeModal() {
    const bd = document.getElementById('analyze-backdrop');
    const pn = document.getElementById('analyze-panel');

    pn.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
    pn.classList.add('opacity-0', 'scale-95', 'translate-y-4');

    setTimeout(() => {
        bd.classList.add('hidden');
        bd.style.opacity = '';
        document.body.style.overflow = '';
    }, 200);
}

// =====================================================================
// HELPERS
// =====================================================================
function show(id, val) {
    const el = document.getElementById(id);
    if (el) el.style.display = val;
}

function setTabActive(id, active) {
    const btn = document.getElementById(id);
    if (!btn) return;

    const on = ['bg-white','text-purple-600','shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]'];
    const off = ['text-gray-500'];

    if (active) {
        btn.classList.add(...on);
        btn.classList.remove(...off);
    } else {
        btn.classList.remove(...on);
        btn.classList.add(...off);
    }
}

function setFilterActive(id, active) {
    const btn = document.getElementById(id);
    if (!btn) return;

    const on = ['bg-white','text-purple-600','shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]'];
    const off = ['text-gray-500'];

    if (active) {
        btn.classList.add(...on);
        btn.classList.remove(...off);
    } else {
        btn.classList.remove(...on);
        btn.classList.add(...off);
    }
}

function openPanel(backdrop, panel) {
    if (!backdrop || !panel) return;

    backdrop.classList.remove('hidden');

    requestAnimationFrame(() => requestAnimationFrame(() => {
        backdrop.style.opacity = '1';
        panel.classList.remove('opacity-0', 'scale-95');
        panel.classList.add('opacity-100', 'scale-100');
    }));

    document.body.style.overflow = 'hidden';
}

function closePanel(backdrop, panel) {
    if (!backdrop || !panel) return;

    panel.classList.remove('opacity-100', 'scale-100');
    panel.classList.add('opacity-0', 'scale-95');

    setTimeout(() => {
        backdrop.classList.add('hidden');
        backdrop.style.opacity = '';
        document.body.style.overflow = '';
    }, 200);
}

function scoreBarClass(score) {
    return score >= 90 ? 'bg-emerald-500' : score >= 80 ? 'bg-purple-600' : 'bg-amber-400';
}

function scoreTextClass(score) {
    return score >= 90 ? 'text-emerald-600' : score >= 80 ? 'text-purple-600' : 'text-amber-500';
}

function createLucideIcons() {
    if (window.lucide && typeof lucide.createIcons === 'function') {
        lucide.createIcons();
    }
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function escapeJs(value) {
    return String(value ?? '').replaceAll('\\', '\\\\').replaceAll("'", "\\'");
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeAnalyzeModal();

        document.querySelectorAll('[data-modal-backdrop]').forEach(backdrop => {
            const panel = document.getElementById(backdrop.dataset.modalPanel);
            closePanel(backdrop, panel);
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    createLucideIcons();
    renderMetricCards();
});
</script>
@endpush