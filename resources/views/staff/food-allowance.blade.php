@extends('layouts.app')
@section('title', 'Pinjaman')
@section('content')

    @push('styles')
        <style>
            /* ── Animations ── */
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .modal-inner {
                animation: slideUp .22s cubic-bezier(.22, 1, .36, 1);
            }

            /* ── Menu card states ── */
            .menu-card:hover {
                border-color: #e9d5ff;
                background: rgba(147, 51, 234, .03);
            }

            .menu-card.selected {
                border-color: #9333ea;
                background: rgba(147, 51, 234, .06);
            }

            .menu-card.selected .card-badge {
                background: #9333ea;
                color: white;
            }

            /* ── Scrollbar ── */
            input[type="date"]::-webkit-calendar-picker-indicator {
                opacity: 0.5;
            }

            .scroll-thin::-webkit-scrollbar {
                width: 4px;
            }

            .scroll-thin::-webkit-scrollbar-track {
                background: transparent;
            }

            .scroll-thin::-webkit-scrollbar-thumb {
                background: #e4e4e7;
                border-radius: 4px;
            }

            /* ── Button states ── */
            .btn-kasbon-disabled {
                opacity: .3;
                cursor: not-allowed;
                pointer-events: none;
            }

            .btn-kasbon-active {
                opacity: 1;
                cursor: pointer;
                pointer-events: auto;
                box-shadow: 0 10px 20px -4px rgba(147, 51, 234, .35);
            }

            .btn-kasbon-active:hover {
                filter: brightness(1.08);
            }

            .btn-cash-disabled {
                opacity: .35;
                cursor: not-allowed;
                pointer-events: none;
            }

            .btn-cash-active {
                opacity: 1;
                cursor: pointer;
                pointer-events: auto;
            }

            .btn-cash-active:hover {
                filter: brightness(1.08);
            }

            /* ── Food kasbon inner layout ── */
            .kasbon-body {
                display: flex;
                flex-direction: column;
            }

            .kasbon-menu-wrap {
                flex: 1;
                min-width: 0;
                padding: 20px;
                border-bottom: 1px solid #f4f4f5;
            }

            .kasbon-menu-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                max-height: 460px;
                overflow-y: auto;
                padding-right: 2px;
            }

            .kasbon-panel {
                width: 100%;
                flex-shrink: 0;
                padding: 20px;
                background: rgba(250, 250, 252, .7);
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            @media(min-width:640px) {
                .kasbon-menu-grid {
                    grid-template-columns: repeat(3, 1fr);
                }
            }

            @media(min-width:1024px) {
                .kasbon-body {
                    flex-direction: row;
                }

                .kasbon-menu-wrap {
                    border-bottom: 0;
                    border-right: 1px solid #f4f4f5;
                }

                .kasbon-menu-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .kasbon-panel {
                    width: 272px;
                }
            }

            @media(min-width:1280px) {
                .kasbon-menu-grid {
                    grid-template-columns: repeat(3, 1fr);
                }

                .kasbon-panel {
                    width: 300px;
                }
            }

            /* ── Cash row ── */
            .cash-row {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            @media(min-width:640px) {
                .cash-row {
                    flex-direction: row;
                }
            }

            /* ── Right column ── */
            .right-col {
                width: 100%;
                flex-shrink: 0;
            }

            @media(min-width:1280px) {
                .right-col {
                    width: 320px;
                    position: sticky;
                    top: 24px;
                    align-self: flex-start;
                }
            }

            /* ── Activity list ── */
            .activity-item {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 10px 12px;
                border-radius: 14px;
                cursor: pointer;
                transition: background .15s;
            }

            .activity-item:hover {
                background: rgba(0, 0, 0, .03);
            }

            .activity-dot {
                width: 14px;
                height: 14px;
                border-radius: 50%;
                flex-shrink: 0;
                margin-top: 4px;
            }

            .activity-dot-food {
                background: #fff;
                border: 3.5px solid #9333ea;
            }

            .activity-dot-cash {
                background: #d1fae5;
                border: 3.5px solid #059669;
            }

            .activity-content {
                flex: 1;
                min-width: 0;
            }

            .activity-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 8px;
                margin-bottom: 4px;
            }

            .activity-date {
                font-size: 12px;
                font-weight: 900;
                color: #18181b;
                line-height: 1.3;
            }

            .activity-type-food {
                font-size: 10px;
                font-weight: 700;
                color: #9ca3af;
                margin-top: 1px;
            }

            .activity-type-cash {
                font-size: 10px;
                font-weight: 700;
                color: #059669;
                margin-top: 1px;
            }

            .activity-right {
                text-align: right;
                flex-shrink: 0;
            }

            .activity-amount-food {
                font-size: 13px;
                font-weight: 900;
                color: #9333ea;
                white-space: nowrap;
            }

            .activity-amount-cash {
                font-size: 13px;
                font-weight: 900;
                color: #059669;
                white-space: nowrap;
            }

            .activity-status {
                display: inline-block;
                margin-top: 3px;
                padding: 2px 7px;
                background: #d1fae5;
                border-radius: 99px;
                font-size: 9px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: #059669;
            }

            .activity-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                margin-top: 5px;
            }

            .activity-tag {
                padding: 2px 8px;
                background: #f4f4f5;
                border-radius: 8px;
                font-size: 9px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: #9ca3af;
            }

            .activity-note {
                font-size: 10px;
                font-weight: 600;
                color: rgba(24, 24, 27, .6);
                font-style: italic;
                margin-top: 3px;
            }
        </style>
    @endpush

    <div class="min-h-screen bg-white">
        <div class="px-4 sm:px-6 lg:px-8 py-6 lg:py-8 max-w-screen-2xl mx-auto">

            {{-- ── PAGE HEADER ── --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M2 1.5h1.2v3A1.2 1.2 0 005.4 4.5V1.5H6.4" stroke="#9333ea" stroke-width="1.2"
                                stroke-linecap="round" />
                            <path d="M4.2 4.5v8" stroke="#9333ea" stroke-width="1.2" stroke-linecap="round" />
                            <path d="M9.5 1.5v11M9.5 1.5a3 3 0 010 5.5" stroke="#9333ea" stroke-width="1.2"
                                stroke-linecap="round" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-[0.12em] text-purple-600">Pinjaman
                            Staff</span>
                    </div>
                    <h1 class="font-mono font-bold text-2xl lg:text-[28px] leading-tight text-zinc-900">Pinjaman Makan dan
                        Pinjaman Tunai</h1>
                    <p class="font-mono text-sm text-gray-400 mt-1 leading-6">Catat pinjamanmu baik dalam bentuk menu
                        ataupun tunai.</p>
                </div>
                <div class="flex items-center gap-4 px-6 py-4 rounded-[36px] shrink-0 border-0"
                    style="background:rgba(147,51,234,.05); outline:1px solid rgba(147,51,234,.12); box-shadow:0 1px 3px rgba(0,0,0,.06)">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[.12em] text-purple-600/60 mb-0.5">
                            Total Pinjaman
                        </p>
                        <p class="text-[22px] font-black text-zinc-900 tabular-nums leading-tight">
                            {{ number_format($totalPinjaman, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-11 h-11 bg-purple-600 rounded-[16px] flex items-center justify-center shrink-0"
                        style="box-shadow:0 8px 16px -4px rgba(147,51,234,.35)">
                        <span
                            style="color:white; font-size:13px; font-weight:900; letter-spacing:-.5px; font-family:monospace">
                            Rp
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── MAIN LAYOUT ── --}}
            <div class="flex flex-col xl:flex-row gap-5">

                {{-- LEFT COLUMN --}}
                <div class="flex-1 min-w-0 flex flex-col gap-5">

                    {{-- ── FOOD KASBON CARD ── --}}
                    <div class="bg-white rounded-3xl border border-zinc-100 overflow-hidden"
                        style="box-shadow:0 1px 3px rgba(0,0,0,.05)">

                        {{-- Card Header --}}
                        <div class="flex items-center gap-3 px-6 py-4 border-b border-zinc-100">
                            <div class="w-9 h-9 bg-amber-50 rounded-2xl flex items-center justify-center shrink-0">
                                <svg width="15" height="15" viewBox="0 0 14 14" fill="none">
                                    <path d="M2 1.5h1.2v3A1.2 1.2 0 005.4 4.5V1.5H6.4" stroke="#d97706" stroke-width="1.3"
                                        stroke-linecap="round" />
                                    <path d="M4.2 4.5v8" stroke="#d97706" stroke-width="1.3" stroke-linecap="round" />
                                    <path d="M9.5 1.5v11M9.5 1.5a3 3 0 010 5.5" stroke="#d97706" stroke-width="1.3"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-wider text-zinc-900">Pinjaman Makan</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Pilih dari menu yang tersedia. Isi dengan jujur!
                                </p>
                            </div>
                        </div>

                        {{-- Card Body: Menu + Panel --}}
                        <div class="kasbon-body">

                            {{-- Menu Grid --}}
                            <div class="kasbon-menu-wrap">
                                <div class="kasbon-menu-grid scroll-thin">
                                    @foreach($menuItem as $item)
                                        @php
                                            $color = match ($item->category) {
                                                'Makanan Berat' => 'blue',
                                                'Minuman' => 'amber',
                                                'Cemilan' => 'purple',
                                                default => 'blue'
                                            };
                                            $palettes = [
                                                'blue' => ['icon_bg' => 'bg-blue-50', 'stroke' => '#2563eb'],
                                                'amber' => ['icon_bg' => 'bg-amber-50', 'stroke' => '#d97706'],
                                                'purple' => ['icon_bg' => 'bg-purple-50', 'stroke' => '#9333ea'],
                                            ];
                                            $pal = $palettes[$color];
                                        @endphp
                                        <div class="menu-card group relative bg-white rounded-[18px] border border-zinc-100 p-4 cursor-pointer transition-all duration-150 select-none"
                                            data-id="{{ $item->id }}" data-name="{{ $item->food_name }}"
                                            data-price="{{ $item->cost_price }}" data-cat="{{ $item->category }}"
                                            data-color="{{ $color }}" onclick="addToTray(this)">
                                            <div class="flex items-start justify-between mb-3">
                                                <div
                                                    class="w-9 h-9 {{ $pal['icon_bg'] }} rounded-xl flex items-center justify-center shrink-0">
                                                    @if($color === 'amber')
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                            <path d="M3 2.5v1.2M6 2.5v1.2M9 2.5v1.2" stroke="{{ $pal['stroke'] }}"
                                                                stroke-width="1.3" stroke-linecap="round" />
                                                            <rect x="1.5" y="4.5" width="9" height="7" rx="1.5"
                                                                stroke="{{ $pal['stroke'] }}" stroke-width="1.3" fill="none" />
                                                        </svg>
                                                    @elseif($color === 'purple')
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                            <rect x="1.5" y="7" width="11" height="5.5" rx="1.5"
                                                                stroke="{{ $pal['stroke'] }}" stroke-width="1.3" fill="none" />
                                                            <path d="M4.5 7V5a3 3 0 016 0v2" stroke="{{ $pal['stroke'] }}"
                                                                stroke-width="1.3" stroke-linecap="round" fill="none" />
                                                        </svg>
                                                    @else
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                            <circle cx="6.5" cy="6.5" r="4" stroke="{{ $pal['stroke'] }}"
                                                                stroke-width="1.3" fill="none" />
                                                            <circle cx="6.5" cy="6.5" r="1.5" stroke="{{ $pal['stroke'] }}"
                                                                stroke-width="1.3" fill="none" />
                                                            <path d="M10 10l2 2" stroke="{{ $pal['stroke'] }}" stroke-width="1.3"
                                                                stroke-linecap="round" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <span
                                                    class="card-badge hidden text-[9px] font-black px-2 py-0.5 bg-zinc-100 text-zinc-500 rounded-full uppercase tracking-wide"></span>
                                            </div>
                                            <p class="text-[13px] font-black text-zinc-900 leading-5 mb-1">
                                                {{ $item->food_name }}
                                            </p>
                                            <div class="flex items-center justify-between">
                                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                                    {{ $item->category }}
                                                </p>
                                                <p class="text-[12px] font-black text-zinc-500 tabular-nums">
                                                    {{ number_format($item->cost_price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Detail Entri Panel --}}
                            <div class="kasbon-panel">
                                <p class="text-[9px] font-black uppercase tracking-[.14em] text-zinc-400">Detail Entri</p>

                                {{-- Tanggal --}}
                                <div>
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-wide text-gray-400 mb-2">Pilih
                                        Tanggal</label>
                                    <div class="relative">
                                        <input type="date" id="kasbonTanggal"
                                            class="w-full px-4 pl-10 bg-white border border-zinc-100 rounded-2xl text-sm font-bold text-zinc-900 focus:outline-none focus:border-purple-300 focus:ring-2 focus:ring-purple-100 transition"
                                            style="height:48px">
                                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-purple-500 pointer-events-none"
                                            fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 16 16">
                                            <rect x="2" y="3" width="12" height="11" rx="1.5" fill="none" />
                                            <path d="M5 1.5v2M11 1.5v2M2 7h12" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Tray --}}
                                <div style="flex:1">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <label class="text-[10px] font-black uppercase tracking-wide text-gray-400">Baki
                                            Anda</label>
                                        <span id="trayBadge"
                                            class="px-2.5 py-1 bg-purple-600/10 rounded-full text-[9px] font-black uppercase tracking-wide text-purple-600">0
                                            Item</span>
                                    </div>
                                    <div id="trayList" class="flex flex-col gap-2" style="min-height:96px">
                                        <div id="trayEmptyState"
                                            class="flex flex-col items-center justify-center opacity-20 gap-2"
                                            style="height:96px">
                                            <svg width="34" height="34" viewBox="0 0 36 36" fill="none">
                                                <rect x="6" y="9" width="24" height="20" rx="2" stroke="#18181b"
                                                    stroke-width="2.5" fill="none" />
                                                <circle cx="18" cy="19" r="5" stroke="#18181b" stroke-width="2.5"
                                                    fill="none" />
                                            </svg>
                                            <p class="text-[9px] font-black uppercase tracking-widest text-zinc-900">Baki
                                                kosong</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Total + Button --}}
                                <div class="border-t border-zinc-100 pt-4 flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-black uppercase tracking-wide text-gray-400">Jumlah
                                            Dicatat</span>
                                        <span id="trayTotal"
                                            class="text-2xl font-black text-purple-600 tabular-nums">00.000</span>
                                    </div>

                                    <button id="btnProcessKasbon" type="button" onclick="processKasbon()"
                                        class="w-full flex items-center justify-center gap-2.5 rounded-3xl text-[11px] font-black uppercase tracking-widest text-white border-0 transition-all duration-200 btn-kasbon-disabled"
                                        style="height:50px;background:#9333ea;">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <rect x="2" y="6" width="12" height="9" rx="1.5" stroke="white"
                                                stroke-width="1.5" fill="none" />
                                            <path d="M5 6V5a3 3 0 016 0v1" stroke="white" stroke-width="1.5"
                                                stroke-linecap="round" fill="none" />
                                            <circle cx="8" cy="10.5" r="1.5" fill="white" />
                                        </svg>
                                        Proses Pinjaman
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>{{-- end food kasbon card --}}

                    {{-- ── CASH ADVANCE CARD ── --}}
                    <div class="bg-white rounded-3xl border border-zinc-100 overflow-hidden"
                        style="box-shadow:0 1px 3px rgba(0,0,0,.05)">

                        <div class="flex items-center gap-3 px-6 py-4 border-b border-zinc-100">
                            <div class="w-9 h-9 bg-emerald-50 rounded-2xl flex items-center justify-center shrink-0">
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                                    <rect x="1.5" y="5.5" width="12" height="8" rx="1.5" stroke="#059669" stroke-width="1.3"
                                        fill="none" />
                                    <path d="M5 5.5V4a2.5 2.5 0 015 0v1.5" stroke="#059669" stroke-width="1.3"
                                        stroke-linecap="round" fill="none" />
                                    <circle cx="7.5" cy="9.5" r="1.3" fill="#059669" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-wider text-zinc-900">Permintaan Pinjaman
                                    Tunai</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Ajukan Pinjaman tunai pribadi.</p>
                            </div>
                        </div>

                        <div class="p-6 flex flex-col gap-5">
                            <div class="cash-row">
                                <div style="flex:1">
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-wide text-gray-400 mb-2">Tujuan
                                        / Kebutuhan</label>
                                    <div class="relative">
                                        <input type="text" id="cashPurpose"
                                            placeholder="mis. Servis motor, Medis, Biaya sekolah"
                                            class="w-full pl-10 pr-4 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 placeholder-zinc-300 focus:outline-none focus:border-purple-300 focus:ring-2 focus:ring-purple-100 transition"
                                            style="height:48px">
                                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"
                                            fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 16 16">
                                            <path d="M2 13a1 1 0 011-1h10a1 1 0 011 1v1H2v-1z" fill="none" />
                                            <rect x="4" y="3" width="8" height="9" rx="1" fill="none" />
                                            <path d="M6 6h4M6 8h2" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div style="flex-shrink:0;width:192px">
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-wide text-gray-400 mb-2">Jumlah
                                        (Rp)</label>
                                    <div class="relative">
                                        <input type="number" id="cashJumlah" placeholder="500000"
                                            class="w-full pl-12 pr-4 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm font-black text-zinc-900 placeholder-zinc-300 focus:outline-none focus:border-purple-300 focus:ring-2 focus:ring-purple-100 transition"
                                            style="height:48px">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-[11px] font-black text-gray-300 pointer-events-none">Rp</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-wide text-gray-400 mb-2">Keterangan</label>
                                <div class="relative">
                                    <textarea id="cashDesc" rows="3"
                                        placeholder="Penjelasan singkat untuk permintaan Pinjaman tunai ini..."
                                        class="w-full pl-10 pr-4 py-3.5 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm text-zinc-900 placeholder-zinc-300 focus:outline-none focus:border-purple-300 focus:ring-2 focus:ring-purple-100 transition resize-none"></textarea>
                                    <svg class="absolute left-3.5 top-4 w-4 h-4 text-gray-300 pointer-events-none"
                                        fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 16 16">
                                        <rect x="3" y="2" width="7" height="9" rx="1" fill="none" />
                                        <path d="M5 5h3M5 7h5M5 9h2" stroke-linecap="round" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button id="btnSubmitCash" type="button" onclick="submitCashAdvance()"
                                    class="flex items-center gap-2 px-7 rounded-2xl text-[11px] font-black uppercase tracking-widest text-white border-0 transition-all duration-200 shrink-0 btn-cash-disabled"
                                    style="height:44px;background:#059669;">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M2 7l3.5 3.5L12 3" stroke="white" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    Kirim Permintaan
                                </button>
                            </div>
                        </div>
                    </div>{{-- end cash advance --}}

                </div>{{-- end left column --}}

                {{-- ── RIGHT COLUMN — Aktivitas Terbaru ── --}}
                <div class="right-col bg-white rounded-3xl border border-zinc-100"
                    style="box-shadow:0 1px 3px rgba(0,0,0,.05)">

                    <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100">
                        <p class="text-[10px] font-black uppercase tracking-[.14em] text-zinc-400/70">Pinjaman Terbaru Anda
                        </p>
                        <div class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                                <circle cx="6.5" cy="6.5" r="5" stroke="#9ca3af" stroke-width="1.2" fill="none" />
                                <circle cx="4" cy="4" r="1.3" stroke="#9ca3af" stroke-width="1.2" fill="none" />
                                <path d="M6.5 6.5l2 2" stroke="#9ca3af" stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                        </div>
                    </div>
                    <div style="padding:8px 8px 4px;">
                        @foreach($recentActivity as $act)
                            <div class="activity-item">
                                <div
                                    class="activity-dot {{ $act->type === 'tunai' ? 'activity-dot-cash' : 'activity-dot-food' }}">
                                </div>

                                <div class="activity-content">
                                    <div class="activity-header">
                                        <div>
                                            <p class="activity-date">
                                                {{ \Carbon\Carbon::parse($act->tanggal)->translatedFormat('d F Y') }}
                                            </p>
                                            <p
                                                class="{{ $act->type === 'tunai' ? 'activity-type-cash' : 'activity-type-food' }}">
                                                {{ $act->type === 'tunai' ? '⊡ Kasbon Tunai' : 'Kasbon Makan' }}
                                            </p>
                                        </div>
                                        <div class="activity-right">
                                            <p
                                                class="{{ $act->type === 'tunai' ? 'activity-amount-cash' : 'activity-amount-food' }}">
                                                Rp {{ number_format($act->total, 0, ',', '.') }}
                                            </p>
                                            <span class="activity-status">{{ $act->status }}</span>
                                        </div>
                                    </div>

                                    @if($act->keterangan)
                                        <p class="activity-note">"{{ $act->keterangan }}"</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="padding:8px 20px 20px;">
                        <div class="flex items-start gap-2.5 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <svg class="shrink-0 mt-0.5" width="13" height="13" viewBox="0 0 13 13" fill="none">
                                <circle cx="6.5" cy="6.5" r="5" stroke="#d97706" stroke-width="1.2" fill="none" />
                                <path d="M6.5 4.5v2.5M6.5 9v.2" stroke="#d97706" stroke-width="1.2"
                                    stroke-linecap="round" />
                            </svg>
                            <p class="text-[10px] font-bold text-amber-700 leading-normal">"Setiap pinjaman yang diajukan
                                akan diakumulasikan dan dipotong secara otomatis dari gaji pada akhir periode penggajian
                                setiap bulan."</p>
                        </div>
                    </div>
                </div>{{-- end right column --}}

            </div>{{-- end main flex --}}
        </div>
    </div>

    {{-- ══ MODAL 1: Kasbon Makan ══ --}}
    <div id="modalFoodKasbon"
        style="display:none;position:fixed;inset:0;z-index:9998;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.42);backdrop-filter:blur(7px);"
        onclick="if(event.target===this)closeFoodKasbon()">
        <div class="modal-inner bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">

            <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-zinc-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-600/10 rounded-2xl flex items-center justify-center shrink-0">
                        <svg width="17" height="17" viewBox="0 0 14 14" fill="none">
                            <path d="M2 1.5h1.2v3A1.2 1.2 0 005.4 4.5V1.5H6.4" stroke="#9333ea" stroke-width="1.3"
                                stroke-linecap="round" />
                            <path d="M4.2 4.5v8" stroke="#9333ea" stroke-width="1.3" stroke-linecap="round" />
                            <path d="M9.5 1.5v11M9.5 1.5a3 3 0 010 5.5" stroke="#9333ea" stroke-width="1.3"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Edit Entri</p>
                        <p class="text-[18px] font-bold text-zinc-900 leading-7">Pinjaman Makan</p>
                    </div>
                </div>
                <button type="button" onclick="closeFoodKasbon()" style="background:transparent;border:0;cursor:pointer"
                    class="w-9 h-9 flex items-center justify-center rounded-2xl hover:bg-zinc-100 text-gray-400 transition-colors">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <line x1="2.5" y1="2.5" x2="11.5" y2="11.5" stroke="currentColor" stroke-width="1.6"
                            stroke-linecap="round" />
                        <line x1="11.5" y1="2.5" x2="2.5" y2="11.5" stroke="currentColor" stroke-width="1.6"
                            stroke-linecap="round" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 flex flex-col gap-5">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wide text-gray-400 mb-2">Tanggal</label>
                    <div class="relative">
                        <input type="date" id="modalKasbonTanggal"
                            class="w-full pl-10 pr-4 bg-gray-50/50 border border-zinc-100 rounded-2xl text-sm font-bold text-zinc-900 focus:outline-none focus:border-purple-300 focus:ring-2 focus:ring-purple-100 transition"
                            style="height:46px">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-purple-400 opacity-70 pointer-events-none"
                            fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 16 16">
                            <rect x="2" y="3" width="12" height="11" rx="1.5" fill="none" />
                            <path d="M5 1.5v2M11 1.5v2M2 7h12" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wide text-gray-400 mb-3">Item</label>
                    <div id="modalTrayList" class="flex flex-col gap-2 mb-3 min-h-12">
                        <p id="modalEmptyCatatan" class="text-[11px] text-gray-300 italic text-center py-3">Belum ada item —
                            pilih dari menu di bawah.</p>
                    </div>
                    <div class="flex items-center justify-between px-4 rounded-2xl border border-purple-100"
                        style="height:46px;background:rgba(147,51,234,.04)">
                        <span class="text-[10px] font-black uppercase tracking-wide text-purple-600/60">Total Baru</span>
                        <span id="modalKasbonTotal" class="text-[15px] font-black text-purple-600 tabular-nums">0</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wide text-gray-400 mb-2">Tambah
                        Item</label>
                    <div class="flex flex-wrap gap-1.5 max-h-28 overflow-y-auto scroll-thin">
                        @foreach($menuItem as $item)
                            <button type="button" data-id="{{ $item->id }}" data-name="{{ $item->food_name }}"
                                data-price="{{ $item->cost_price }}"
                                onclick="modalAddItem(this.dataset.id, this.dataset.name, parseInt(this.dataset.price))"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-purple-50 hover:text-purple-700 rounded-xl text-[9px] font-black uppercase tracking-wide text-gray-500 border-0 cursor-pointer transition-colors">
                                {{ $item->food_name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeFoodKasbon()"
                        style="background:#f4f4f5;border:1px solid #e4e4e7;cursor:pointer;height:44px;padding:0 24px;border-radius:14px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:#18181b">Kembali</button>
                    <button type="button" onclick="saveKasbonModal()"
                        style="background:#9333ea;border:0;cursor:pointer;flex:1;height:44px;box-shadow:0 4px 12px -2px rgba(147,51,234,.3);border-radius:14px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:white"
                        class="flex items-center justify-center gap-2 hover:brightness-110 transition">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none">
                            <path d="M1.5 6.5l3.5 3.5 6.5-6.5" stroke="white" stroke-width="1.6" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MODAL 2: Edit Entri ══ --}}
    <div id="modalEditEntry"
        style="display:none;position:fixed;inset:0;z-index:9998;align-items:center;justify-content:center;padding:1rem;background:rgba(0,0,0,.42);backdrop-filter:blur(7px);"
        onclick="if(event.target===this)closeEditEntry()">
        <div class="modal-inner bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between px-6 pt-5 pb-4 border-b border-zinc-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-600/10 rounded-2xl flex items-center justify-center shrink-0">
                        <svg width="17" height="17" viewBox="0 0 14 14" fill="none">
                            <path d="M2 1.5h1.2v3A1.2 1.2 0 005.4 4.5V1.5H6.4" stroke="#9333ea" stroke-width="1.3"
                                stroke-linecap="round" />
                            <path d="M4.2 4.5v8" stroke="#9333ea" stroke-width="1.3" stroke-linecap="round" />
                            <path d="M9.5 1.5v11M9.5 1.5a3 3 0 010 5.5" stroke="#9333ea" stroke-width="1.3"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Edit Entri</p>
                        <p id="editModalTitle" class="text-[18px] font-bold text-zinc-900 leading-7">Pinjaman Makan</p>
                    </div>
                </div>
            </div>
            <div id="editModalBody" class="px-6 py-5"></div>
        </div>
    </div>

    {{-- ══ MODAL : Konfirmasi ══ --}}
    <div id="confirmModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:99999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px)">
        <div
            style="width:100%; max-width:360px; background:white; border-radius:28px; padding:24px; box-shadow:0 25px 50px rgba(0,0,0,.2)">

            <div class="flex items-center gap-3 mb-5">
                <div
                    style="width:52px; height:52px; border-radius:18px; background:#9333ea; display:flex; align-items:center; justify-content:center; color:white; font-size:24px; font-weight:900">
                    !
                </div>
                <div>
                    <p class="text-sm font-black text-zinc-900">Konfirmasi Pinjaman</p>
                    <p class="text-xs text-zinc-500 mt-1 leading-relaxed">Pastikan data pinjaman sudah benar.</p>
                </div>
            </div>

            <p id="confirmMessage" class="text-sm text-zinc-700 leading-relaxed mb-6"></p>

            <div class="flex gap-3">
                <button type="button" onclick="closeConfirmModal(false)" onmouseenter="this.style.background='#e4e4e7'"
                    onmouseleave="this.style.background='#f4f4f5'"
                    class="flex-1 h-12 rounded-2xl bg-zinc-100 text-zinc-700 text-xs font-black uppercase tracking-wide transition-all duration-200">
                    Batal
                </button>
                <button type="button" onclick="closeConfirmModal(true)"
                    onmouseenter="this.style.transform='translateY(-1px)';this.style.opacity='.92'"
                    onmouseleave="this.style.transform='translateY(0)';this.style.opacity='1'"
                    class="flex-1 h-12 rounded-2xl text-white text-xs font-black uppercase tracking-wide transition-all duration-200"
                    style="background:linear-gradient(135deg,#9333ea,#7c3aed)">
                    Ya, Lanjut
                </button>
            </div>
        </div>
    </div>

    {{-- ══ MODAL : Announce ══ --}}
    <div id="successModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:99999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px)">
        <div
            style="width:100%; max-width:340px; background:white; border-radius:30px; padding:28px 24px; box-shadow:0 25px 50px rgba(0,0,0,.2); text-align:center">

            <div
                style="width:74px; height:74px; border-radius:24px; background:linear-gradient(135deg,#9333ea,#7c3aed); display:flex; align-items:center; justify-content:center; margin:0 auto 18px; box-shadow:0 14px 30px rgba(147,51,234,.35)">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
                    <path d="M6 12.5l4 4 8-9" stroke="white" stroke-width="2.8" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>

            <h3 class="text-xl font-black text-zinc-900 mb-2">Berhasil!</h3>
            <p id="successMessage" class="text-sm text-zinc-500 leading-relaxed mb-6"></p>

            <button type="button" onclick="closeSuccessModal()"
                onmouseenter="this.style.transform='translateY(-1px)'; this.style.opacity='.92'"
                onmouseleave="this.style.transform='translateY(0)'; this.style.opacity='1'"
                class="w-full h-12 rounded-2xl text-white text-xs font-black uppercase tracking-wide transition-all duration-200"
                style="background:linear-gradient(135deg,#9333ea,#7c3aed)">
                Oke
            </button>

        </div>
    </div>
    <div id="warningModal"
        style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:999999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(4px)">
        <div
            style="width:100%; max-width:340px; background:white; border-radius:30px; padding:26px 24px; box-shadow:0 25px 50px rgba(0,0,0,.2); text-align:center">
            <div
                style="width:64px; height:64px; border-radius:22px; background:#fef3c7; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; color:#d97706; font-size:30px; font-weight:900">
                !
            </div>

            <p class="text-lg font-black text-zinc-900 mb-2">Perhatian</p>
            <p id="warningMessage" class="text-sm text-zinc-500 leading-relaxed mb-6"></p>

            <button type="button" onclick="closeWarningModal()"
                class="w-full h-12 rounded-2xl bg-purple-600 text-white text-xs font-black uppercase tracking-wide hover:bg-purple-700 transition">
                Mengerti
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let confirmResolve = null;

        function openConfirmModal(message) {

            document.getElementById('confirmMessage')
                .textContent = message;

            document.getElementById('confirmModal')
                .style.display = 'flex';

            return new Promise((resolve) => {
                confirmResolve = resolve;
            });
        }

        function closeConfirmModal(result) {

            document.getElementById('confirmModal')
                .style.display = 'none';

            if (confirmResolve) {
                confirmResolve(result);
            }
        }

        function openSuccessModal(message) {
            document.getElementById('successMessage')
                .textContent = message;
            document.getElementById('successModal')
                .style.display = 'flex';
        }

        function openWarningModal(message) {
            document.getElementById('warningMessage').textContent = message;
            document.getElementById('warningModal').style.display = 'flex';
        }

        function closeWarningModal() {
            document.getElementById('warningModal').style.display = 'none';
        }
        function closeSuccessModal() {
            document.getElementById('successModal')
                .style.display = 'none';
            location.reload();
        }

        function idr(n) { return Number(n).toLocaleString('id-ID'); }

        function toIndo(dateStr) {
            var days = { Mon: 'Senin', Tue: 'Selasa', Wed: 'Rabu', Thu: 'Kamis', Fri: 'Jumat', Sat: 'Sabtu', Sun: 'Minggu' };
            var months = { Jan: 'Jan', Feb: 'Feb', Mar: 'Mar', Apr: 'Apr', May: 'Mei', Jun: 'Jun', Jul: 'Jul', Aug: 'Agu', Sep: 'Sep', Oct: 'Okt', Nov: 'Nov', Dec: 'Des' };
            return dateStr.replace(/^(\w{3}),\s+(\w{3})\s+(\d+)/, function (_, d, m, n) {
                return (days[d] || d) + ', ' + n + ' ' + (months[m] || m);
            });
        }

        /* ══ TRAY ══ */
        var tray = {};

        function addToTray(el) {
            var id = el.dataset.id;
            var name = el.dataset.name;
            var price = parseInt(el.dataset.price);
            var color = el.dataset.color;
            if (tray[id]) { tray[id].qty++; }
            else { tray[id] = { name: name, price: price, qty: 1, color: color }; }
            renderTray();
            el.classList.add('selected');
            var badge = el.querySelector('.card-badge');
            badge.textContent = 'x' + tray[id].qty;
            badge.classList.remove('hidden');
        }

        function renderTray() {
            var keys = Object.keys(tray);
            var total = 0;
            var html = '';
            keys.forEach(function (id) {
                var it = tray[id];
                total += it.price * it.qty;
                html +=
                    '<div class="flex items-center gap-2 px-3 py-2 bg-white rounded-xl border border-zinc-100">' +
                    '<span class="text-xs font-bold text-zinc-900 flex-1 truncate">' + it.name + '</span>' +
                    '<button type="button" onclick="changeTrayQty(\'' + id + '\',-1)" style="background:#f4f4f5;border:0;cursor:pointer;width:24px;height:24px;border-radius:8px;font-weight:900;font-size:14px;line-height:1">&#8722;</button>' +
                    '<span class="text-xs font-black w-5 text-center tabular-nums">' + it.qty + '</span>' +
                    '<button type="button" onclick="changeTrayQty(\'' + id + '\',1)" style="background:#f4f4f5;border:0;cursor:pointer;width:24px;height:24px;border-radius:8px;font-weight:900;font-size:14px;line-height:1">+</button>' +
                    '<span class="text-xs font-black text-purple-600 w-16 text-right tabular-nums">' + idr(it.price * it.qty) + '</span>' +
                    '</div>';
            });

            var emEl = document.getElementById('trayEmptyState');
            var listEl = document.getElementById('trayList');
            var badge = document.getElementById('trayBadge');
            var totEl = document.getElementById('trayTotal');
            var btn = document.getElementById('btnProcessKasbon');

            if (keys.length) {
                listEl.innerHTML = html;
            } else {
                listEl.innerHTML = '';
                if (emEl) {
                    listEl.appendChild(emEl);
                }
                if (emEl) {
                    emEl.style.display = 'flex';
                }
            }

            var totalQty = keys.reduce(function (s, id) { return s + tray[id].qty; }, 0);
            badge.textContent = totalQty + ' Item' + (totalQty !== 1 ? 's' : '');
            totEl.textContent = idr(total);

            if (keys.length && total > 0) {
                btn.classList.remove('btn-kasbon-disabled');
                btn.classList.add('btn-kasbon-active');
            } else {
                btn.classList.remove('btn-kasbon-active');
                btn.classList.add('btn-kasbon-disabled');
            }
        }

        function changeTrayQty(id, delta) {
            if (!tray[id]) return;
            tray[id].qty += delta;
            if (tray[id].qty <= 0) {
                delete tray[id];
                var card = document.querySelector('[data-id="' + id + '"]');
                if (card) {
                    card.classList.remove('selected');
                    var b = card.querySelector('.card-badge');
                    if (b) b.classList.add('hidden');
                }
            } else {
                var card = document.querySelector('[data-id="' + id + '"]');
                if (card) {
                    var b = card.querySelector('.card-badge');
                    if (b) b.textContent = 'x' + tray[id].qty;
                }
            }
            renderTray();
        }

        async function processKasbon() {
            var date = document.getElementById('kasbonTanggal').value;
            if (!date) return openWarningModal('Harap pilih tanggal.');
            if (!Object.keys(tray).length) return openWarningModal('Menu masih kosong.');
            const confirmed = await openConfirmModal(
                'Yakin ingin mengajukan pinjaman menu ini?');
            if (!confirmed) return;

            let total = 0, items = [];
            Object.keys(tray).forEach(id => {
                total += tray[id].price * tray[id].qty;
                items.push(`${tray[id].qty}x ${tray[id].name}`);
            });

            try {
                const response = await fetch('/food-allowance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ type: 'makan', total: total, tanggal: date, keterangan: items.join(', ') })
                });

                const result = await response.json();
                if (!result.success) return openWarningModal('Gagal menyimpan data.');

                openSuccessModal('Pinjaman berhasil dikirim.');
                tray = {};

                document.querySelectorAll('.menu-card').forEach(c => {
                    c.classList.remove('selected');
                    var b = c.querySelector('.card-badge');
                    if (b) { b.classList.add('hidden'); b.textContent = ''; }
                });

                renderTray();
                document.getElementById('kasbonTanggal').value = '';
                setTimeout(() => location.reload(), 1000);

            } catch (error) {
                console.error(error);
                openWarningModal('Terjadi kesalahan server.');
            }
        }

        /* ══ CASH ADVANCE ══ */
        function _checkCash() {
            var ok = document.getElementById('cashPurpose').value.trim() &&
                document.getElementById('cashJumlah').value.trim();
            var btn = document.getElementById('btnSubmitCash');
            if (ok) { btn.classList.remove('btn-cash-disabled'); btn.classList.add('btn-cash-active'); }
            else { btn.classList.remove('btn-cash-active'); btn.classList.add('btn-cash-disabled'); }
        }
        document.getElementById('cashPurpose').addEventListener('input', _checkCash);
        document.getElementById('cashJumlah').addEventListener('input', _checkCash);

        async function submitCashAdvance() {
            var tujuan = document.getElementById('cashPurpose').value.trim();
            var jumlah = document.getElementById('cashJumlah').value.trim();
            var catatan = document.getElementById('cashDesc').value.trim();

            if (!tujuan || !jumlah) return openWarningModal('Harap lengkapi data pinjaman tunai.');
            const confirmed = await openConfirmModal(
                'Yakin ingin mengajukan pinjaman tunai ini?'
            );
            if (!confirmed) return;

            try {
                const response = await fetch('/food-allowance', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        type: 'tunai',
                        total: jumlah,
                        tanggal: new Date().toISOString().split('T')[0],
                        keterangan: tujuan + (catatan ? ' - ' + catatan : '')
                    })
                });

                const result = await response.json();
                if (!result.success) return openWarningModal('Gagal menyimpan pinjaman tunai.');

                openSuccessModal('Pinjaman tunai berhasil dikirim.');
                document.getElementById('cashPurpose').value = '';
                document.getElementById('cashJumlah').value = '';
                document.getElementById('cashDesc').value = '';

                _checkCash();
                setTimeout(() => location.reload(), 1000);

            } catch (error) {
                console.error(error);
                openWarningModal('Terjadi kesalahan server.');
            }
        }

        /* ══ MODAL 1 ══ */
        var mTray = {};

        function openFoodKasbon() {
            mTray = {};
            _renderModalTray();
            document.getElementById('modalKasbonTanggal').value = new Date().toISOString().slice(0, 10);
            document.getElementById('modalFoodKasbon').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeFoodKasbon() {
            document.getElementById('modalFoodKasbon').style.display = 'none';
            document.body.style.overflow = '';
        }

        function modalAddItem(id, name, price) {
            id = String(id);
            if (mTray[id]) { mTray[id].qty++; }
            else { mTray[id] = { name: name, price: price, qty: 1 }; }
            _renderModalTray();
        }

        function _renderModalTray() {
            var keys = Object.keys(mTray);
            var total = 0;
            var html = '';
            keys.forEach(function (id) {
                var it = mTray[id];
                total += it.price * it.qty;
                html +=
                    '<div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-xl border border-zinc-100">' +
                    '<span class="text-xs font-bold text-zinc-900 flex-1">' + it.name + '</span>' +
                    '<button type="button" onclick="changeModalQty(\'' + id + '\',-1)" style="background:#e5e7eb;border:0;cursor:pointer;width:22px;height:22px;border-radius:7px;font-weight:900;font-size:14px;line-height:1">&#8722;</button>' +
                    '<span class="text-xs font-black w-5 text-center">' + it.qty + '</span>' +
                    '<button type="button" onclick="changeModalQty(\'' + id + '\',1)" style="background:#e5e7eb;border:0;cursor:pointer;width:22px;height:22px;border-radius:7px;font-weight:900;font-size:14px;line-height:1">+</button>' +
                    '<span class="text-xs font-black text-purple-600 w-14 text-right tabular-nums">' + idr(it.price * it.qty) + '</span>' +
                    '<button type="button" onclick="changeModalQty(\'' + id + '\',-999)" style="background:transparent;border:0;cursor:pointer;color:#d1d5db;font-size:14px;line-height:1;margin-left:2px">&#x2715;</button>' +
                    '</div>';
            });

            var emCatatan = document.getElementById('modalEmptyCatatan');
            var list = document.getElementById('modalTrayList');
            emCatatan.style.display = keys.length ? 'none' : 'block';
            list.innerHTML = html;
            if (!keys.length && emCatatan) {
                list.appendChild(emCatatan);
            }
            document.getElementById('modalKasbonTotal').textContent = idr(total);
        }

        function changeModalQty(id, delta) {
            if (!mTray[id]) return;
            mTray[id].qty = Math.max(0, mTray[id].qty + delta);
            if (mTray[id].qty === 0) delete mTray[id];
            _renderModalTray();
        }

        function saveKasbonModal() {
            var date = document.getElementById('modalKasbonTanggal').value;
            if (!date) { openWarningModal('Harap pilih tanggal.'); return; }
            if (!Object.keys(mTray).length) { openWarningModal('Baki kosong.'); return; }
            openSuccessModal('Pinjaman tersimpan!');
            closeFoodKasbon();
        }

        /* ══ MODAL 2 ══ */
        function openEditEntry(data) {
            document.getElementById('editModalTitle').textContent =
                data.type === 'cash' ? 'Kasbon Tunai' : 'Kasbon Makan';

            var body = '<div class="flex flex-col gap-4">';
            body += '<div><p class="text-[10px] font-black uppercase tracking-wide text-gray-400 mb-1">Tanggal</p>' +
                '<p class="text-sm font-bold text-zinc-900">' + toIndo(data.date) + '</p></div>';

            if (data.type === 'food') {
                body += '<div><p class="text-[10px] font-black uppercase tracking-wide text-gray-400 mb-2">Item</p><div class="flex flex-col gap-2">';
                if (data.tags) {
                    data.tags.forEach(function (tag) {
                        body += '<div class="flex items-center px-3 py-2 bg-gray-50 rounded-xl border border-zinc-100">' +
                            '<span class="text-xs font-bold text-zinc-900">' + tag + '</span></div>';
                    });
                }
                body += '</div></div>';
                body += '<div class="flex items-center justify-between px-4 rounded-2xl border border-purple-100" style="height:46px;background:rgba(147,51,234,.04)">' +
                    '<span class="text-[10px] font-black uppercase tracking-wide text-purple-600/60">Total</span>' +
                    '<span class="text-[15px] font-black text-purple-600 tabular-nums">Rp ' + idr(data.amount) + '</span></div>';
            } else {
                if (data.note) {
                    body += '<div><p class="text-[10px] font-black uppercase tracking-wide text-gray-400 mb-1">Catatan</p>' +
                        '<p class="text-sm font-bold text-zinc-900">' + data.note + '</p></div>';
                }
                body += '<div><p class="text-[10px] font-black uppercase tracking-wide text-gray-400 mb-1">Jumlah</p>' +
                    '<p class="text-2xl font-black text-emerald-600 tabular-nums">Rp ' + idr(data.amount) + '</p></div>';
            }

            body += '<div class="pt-2">' +
                '<button type="button" onclick="closeEditEntry()" onmouseenter="this.style.background=\'#ede9fe\';this.style.color=\'#7c3aed\';this.style.borderColor=\'#c4b5fd\'" onmouseleave="this.style.background=\'#f4f4f5\';this.style.color=\'#18181b\';this.style.borderColor=\'#e4e4e7\'" onmousedown="this.style.background=\'#ddd6fe\'" onmouseup="this.style.background=\'#ede9fe\'" style="width:100%;background:#f4f4f5;border:1px solid #e4e4e7;cursor:pointer;height:44px;border-radius:14px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:#18181b;transition:background .15s,color .15s,border-color .15s;">Kembali</button>' +
                '</div></div>';

            document.getElementById('editModalBody').innerHTML = body;
            document.getElementById('modalEditEntry').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeEditEntry() {
            document.getElementById('modalEditEntry').style.display = 'none';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeFoodKasbon(); closeEditEntry(); }
        });
    </script>
@endpush