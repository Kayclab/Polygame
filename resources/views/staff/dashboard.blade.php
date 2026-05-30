@extends('layouts.app')
@section('title', 'Dashboard – Poly Games Cafe')

@push('styles')
    <style>
        /* ═══════════════════════════════════════════
       CAROUSEL
     ═══════════════════════════════════════════ */
        #hero-track { display: flex; will-change: transform; transition: transform 0.35s cubic-bezier(.22, 1, .36, 1); touch-action: pan-y; cursor: grab; }
        #hero-track:active { cursor: grabbing; }
        #hero-track>* { flex-shrink: 0; width: 100%; }
        /* ═══════════════════════════════════════════
       WRAPPER PADDING — semua ukuran device
     ═══════════════════════════════════════════ */
        .dash-wrap { padding: 16px 12px; /* HP kecil < 360px */ }
        @media (min-width: 360px) { .dash-wrap { padding: 20px 16px; } /* HP kecil normal */ }
        @media (min-width: 480px) { .dash-wrap { padding: 24px 20px; } /* HP besar */ }
        @media (min-width: 640px) { .dash-wrap { padding: 28px 24px; } /* Tablet kecil */ }
        @media (min-width: 768px) { .dash-wrap { padding: 32px 28px; } /* Tablet */ }
        @media (min-width: 1024px) { .dash-wrap { padding: 32px 32px; } /* Laptop */ }
        @media (min-width: 1280px) { .dash-wrap { padding: 40px 40px; } /* Desktop besar */ }
        /* ═══════════════════════════════════════════
       PAGE HEADER
     ═══════════════════════════════════════════ */
        .dash-title { font-size: 22px; font-weight: 700; color: #18181b; margin: 0 0 4px; line-height: 1.2; }
        @media (min-width: 480px) { .dash-title { font-size: 26px; } }
        @media (min-width: 768px) { .dash-title { font-size: 30px; } }
        @media (min-width: 1024px) { .dash-title { font-size: 32px; } }
        /* ═══════════════════════════════════════════
       CAROUSEL CARD PADDING
     ═══════════════════════════════════════════ */
        .slide-inner { padding: 24px 20px; min-height: 200px; }
        @media (min-width: 360px) { .slide-inner { padding: 28px 24px; min-height: 210px; } }
        @media (min-width: 480px) { .slide-inner { padding: 32px 28px; min-height: 220px; } }
        @media (min-width: 640px) { .slide-inner { padding: 36px 36px; min-height: 230px; } }
        @media (min-width: 768px) { .slide-inner { padding: 40px 40px; min-height: 240px; } }
        @media (min-width: 1024px) { .slide-inner { padding: 48px 48px; min-height: 260px; } }
        /* ═══════════════════════════════════════════
       SLIDE TITLE
     ═══════════════════════════════════════════ */
        .slide-title { font-size: 22px; font-weight: 900; color: white; line-height: 1.1; margin: 0 0 10px; }
        @media (min-width: 360px) { .slide-title { font-size: 24px; margin-bottom: 12px; } }
        @media (min-width: 480px) { .slide-title { font-size: 28px; } }
        @media (min-width: 640px) { .slide-title { font-size: 30px; margin-bottom: 14px; } }
        @media (min-width: 768px) { .slide-title { font-size: 34px; margin-bottom: 16px; } }
        @media (min-width: 1024px) { .slide-title { font-size: 38px; } }
        /* ═══════════════════════════════════════════
       SLIDE DESCRIPTION
     ═══════════════════════════════════════════ */
        .slide-desc { font-size: 13px; font-weight: 500; color: rgba(255, 255, 255, .85); line-height: 1.6; font-style: italic; margin: 0 0 16px; max-width: 100%; }
        @media (min-width: 480px) { .slide-desc { font-size: 14px; } }
        @media (min-width: 640px) { .slide-desc { font-size: 16px; margin-bottom: 20px; } }
        @media (min-width: 768px) { .slide-desc { font-size: 18px; max-width: 600px; } }
        @media (min-width: 1024px) { .slide-desc { font-size: 20px; max-width: 700px; margin-bottom: 24px; } }
        /* ═══════════════════════════════════════════
       SLIDE META (date, author)
     ═══════════════════════════════════════════ */
        .slide-meta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        @media (min-width: 640px) { .slide-meta { gap: 20px; } }
        .slide-meta-item { display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: rgba(255, 255, 255, .6); }
        @media (min-width: 640px) { .slide-meta-item { font-size: 11px; } }
        /* ═══════════════════════════════════════════
       CAROUSEL BORDER RADIUS
     ═══════════════════════════════════════════ */
        #hero-carousel { border-radius: 20px; }
        @media (min-width: 480px) { #hero-carousel { border-radius: 24px; } }
        @media (min-width: 768px) { #hero-carousel { border-radius: 28px; } }
        @media (min-width: 1024px) { #hero-carousel { border-radius: 32px; } }
        /* ═══════════════════════════════════════════
       DOTS + NAV
     ═══════════════════════════════════════════ */
        .carousel-controls { display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-top: 12px; padding-right: 2px; }
        @media (min-width: 640px) { .carousel-controls { gap: 14px; margin-top: 14px; } }
        @media (min-width: 1024px) { .carousel-controls { gap: 16px; margin-top: 16px; } }
        .nav-btn { width: 34px; height: 34px; border-radius: 12px; border: 1px solid #e4e4e7; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0, 0, 0, .06); transition: background .15s, transform .1s; }
        .nav-btn:hover { background: #f4f4f5; }
        .nav-btn:active { transform: scale(.93); }
        @media (min-width: 640px) { .nav-btn { width: 38px; height: 38px; border-radius: 13px; } }
        @media (min-width: 1024px) { .nav-btn { width: 40px; height: 40px; border-radius: 14px; } }
        /* ═══════════════════════════════════════════
       RECENT PROGRESS CARD
     ═══════════════════════════════════════════ */
        .progress-card { border-radius: 20px; }
        @media (min-width: 480px) { .progress-card { border-radius: 24px; } }
        @media (min-width: 1024px) { .progress-card { border-radius: 32px; } }
        .progress-row { display: flex; align-items: center; justify-content: space-between; padding: 0 16px; height: 72px; text-decoration: none; transition: background .15s; }
        @media (min-width: 480px) { .progress-row { padding: 0 20px; height: 76px; } }
        @media (min-width: 640px) { .progress-row { padding: 0 24px; height: 80px; } }
        @media (min-width: 1024px) { .progress-row { padding: 0 32px; } }
        .progress-icon { width: 34px; height: 34px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        @media (min-width: 480px) { .progress-icon { width: 38px; height: 38px; border-radius: 14px; } }
        @media (min-width: 640px) { .progress-icon { width: 40px; height: 40px; border-radius: 16px; } }
        .progress-title { font-size: 13px; font-weight: 700; color: #18181b; margin: 0 0 2px; }
        @media (min-width: 480px) { .progress-title { font-size: 14px; } }
        .progress-desc { font-size: 11px; color: #9ca3af; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
        @media (min-width: 360px) { .progress-desc { max-width: 200px; } }
        @media (min-width: 480px) { .progress-desc { max-width: 260px; font-size: 12px; } }
        @media (min-width: 640px) { .progress-desc { max-width: 360px; } }
        @media (min-width: 768px) { .progress-desc { max-width: 500px; } }
        @media (min-width: 1024px) { .progress-desc { max-width: none; white-space: normal; } }
        .progress-time { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #d1d5db; white-space: nowrap; }
        @media (min-width: 640px) { .progress-time { font-size: 10px; } }
        /* ═══════════════════════════════════════════
       BG DECO — hide on small screens
     ═══════════════════════════════════════════ */
        .slide-deco { display: none; }
        @media (min-width: 480px) { .slide-deco { display: block; position: absolute; right: -40px; top: -40px; width: 260px; height: 260px; opacity: .08; pointer-events: none; } }
        @media (min-width: 768px) { .slide-deco { width: 320px; height: 320px; } }
        @media (min-width: 1024px) { .slide-deco { width: 380px; height: 380px; } }
    </style>
@endpush

@section('content')
    <div class="dash-wrap">
        {{-- ── PAGE HEADER ── --}}
        <div style="margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:7px;margin-bottom:4px;">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><rect x="1.33" y="1.33" width="13.33" height="13.33" rx="2" stroke="#9333ea" stroke-width="1.33" /><line x1="1.33" y1="8" x2="14.67" y2="8" stroke="#9333ea" stroke-width="1.33" /><line x1="8" y1="1.33" x2="8" y2="14.67" stroke="#9333ea" stroke-width="1.33" /></svg>
                <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#9333ea;">Dashboard Staf</span>
            </div>
            <h1 class="dash-title">Selamat datang kembali, {{ auth()->user()->n_kry ?? 'John' }}!</h1>
            <p style="font-size:13px;color:#6b7280;margin:0;">Semoga shift hari ini menyenangkan!</p>
        </div>

        {{-- ── ANNOUNCEMENT CAROUSEL ── --}}
        <div style="margin-bottom:28px;position:relative;">
            <div id="hero-carousel" style="overflow:hidden;">
                <div id="hero-track">
                    @forelse($announcements as $announcement)
                        <div style="background:#9333ea; position:relative; overflow:hidden; display:flex; flex-direction:column; justify-content:center; box-shadow:0 20px 40px -10px rgba(147,51,234,.25);">
                            <div class="slide-deco" style="transform:rotate(12deg);"><svg viewBox="0 0 200 200" fill="none" style="width:100%; height:100%;"><rect x="30" y="24" width="110" height="94" rx="8" stroke="white" stroke-width="14" /><rect x="44" y="110" width="44" height="60" rx="4" stroke="white" stroke-width="14" /><rect x="30" y="70" width="110" height="4" fill="white" /></svg></div>
                            <div class="slide-inner">
                                <div style="display:inline-flex; align-items:center; gap:7px; background:rgba(255,255,255,.20); border:1px solid rgba(255,255,255,.30); border-radius:14px; padding:0 14px; height:30px; width:fit-content; margin-bottom:16px;">
                                    <span style="width:7px; height:7px; background:#fb7185; border-radius:50%; opacity:.75; flex-shrink:0;"></span>
                                    <span style="font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.1em; color:white;">Pengumuman</span>
                                </div>
                                <h2 class="slide-title">{{ $announcement->title }}</h2>
                                <p class="slide-desc">"{{ $announcement->content }}"</p>
                                <div class="slide-meta">
                                    <span class="slide-meta-item"><svg width="13" height="13" viewBox="0 0 16 16" fill="none"><rect x="2" y="2.67" width="12" height="12" rx="1.5" stroke="rgba(255,255,255,.6)" stroke-width="1.33" /><line x1="5.33" y1="1.33" x2="5.33" y2="4" stroke="rgba(255,255,255,.6)" stroke-width="1.33" stroke-linecap="round" /><line x1="10.67" y1="1.33" x2="10.67" y2="4" stroke="rgba(255,255,255,.6)" stroke-width="1.33" stroke-linecap="round" /><line x1="2" y1="6.67" x2="14" y2="6.67" stroke="rgba(255,255,255,.6)" stroke-width="1.33" /></svg>{{ \Carbon\Carbon::parse($announcement->created_at)->format('d M Y') }}</span>
                                    <span class="slide-meta-item"><svg width="13" height="13" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="5.33" r="2.67" stroke="rgba(255,255,255,.6)" stroke-width="1.33" /><path d="M2.67 13.33a5.33 5.33 0 0110.67 0" stroke="rgba(255,255,255,.6)" stroke-width="1.33" stroke-linecap="round" /></svg>Dibuat oleh {{ optional($announcement->karyawan)->n_kry ?? 'Admin' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="background:#9333ea; color:white; padding:40px; border-radius:24px;">Belum ada pengumuman.</div>
                    @endforelse
                </div>{{-- end #hero-track --}}
            </div>{{-- end #hero-carousel --}}

            {{-- DOTS + NAV BUTTONS (di luar carousel) --}}
            @if(count($announcements) > 1)
                <div class="carousel-controls">
                    <div style="display:flex; align-items:center; gap:5px;">
                        @foreach($announcements as $index => $announcement)
                            <div id="dot-{{ $index }}" onclick="heroGo({{ $index }})" style="height:5px; border-radius:99px; background:{{ $index == 0 ? '#9333ea' : '#d4d4d8' }}; width:{{ $index == 0 ? '24px' : '5px' }}; transition:all .3s; cursor:pointer;"></div>
                        @endforeach
                    </div>
                    <button class="nav-btn" onclick="heroGo(window._hi-1)" aria-label="Previous"><svg width="7" height="12" viewBox="0 0 10 14" fill="none"><path d="M8 1L2 7l6 6" stroke="#18181b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg></button>
                    <button class="nav-btn" onclick="heroGo(window._hi+1)" aria-label="Next"><svg width="7" height="12" viewBox="0 0 10 14" fill="none"><path d="M2 1l6 6-6 6" stroke="#18181b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg></button>
                </div>
            @endif
        </div>{{-- end carousel wrapper --}}

        {{-- ── YOUR RECENT PROGRESS ── --}}
        <div>
            <div style="display:flex;align-items:center;gap:7px;margin-bottom:14px;">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><rect x="1.33" y="1.33" width="13.33" height="13.33" rx="2" stroke="#9333ea" stroke-width="1.33" /></svg>
                <span style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.12em;color:#18181b;">Progres Terbaru Anda</span>
            </div>

            <div class="progress-card" style="background:white;border:1px solid #f4f4f5;box-shadow:0 1px 3px rgba(0,0,0,.05);overflow:hidden;">
                {{-- SLIP GAJI --}}
                @if($latestSlip)
                    <a href="{{ route('staff.payroll.index') }}" class="progress-row" style="border-bottom:1px solid #f4f4f5;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">
                            <div class="progress-icon" style="background:#ecfdf5;"><svg width="15" height="15" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="#059669" stroke-width="1.4" /></svg></div>
                            <div style="min-width:0;flex:1;">
                                <p class="progress-title">Slip Gaji Tersedia</p>
                                <p class="progress-desc">Slip periode {{ \Carbon\Carbon::parse($latestSlip->periode . '-01')->translatedFormat('F Y') }} telah tersedia.</p>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:8px;"><span class="progress-time">{{ \Carbon\Carbon::parse($latestSlip->created_at)->diffForHumans() }}</span></div>
                    </a>
                @endif

                @if($latestPinjaman)
                    <a href="{{ route('staff.food-allowance.index') }}" class="progress-row" style="border-bottom:1px solid #f4f4f5;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">
                            <div class="progress-icon" style="background:#faf5ff;"><svg width="15" height="15" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="#9333ea" stroke-width="1.4" /></svg></div>
                            <div style="min-width:0;flex:1;">
                                <p class="progress-title">Pinjaman Makan Tercatat</p>
                                <p class="progress-desc">Total pinjaman Rp {{ number_format($latestPinjaman->total, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:8px;"><span class="progress-time">{{ \Carbon\Carbon::parse($latestPinjaman->created_at)->diffForHumans() }}</span></div>
                    </a>
                @endif

                {{-- LEMBUR --}}
                @if($latestLembur)
                    <a href="{{ route('staff.overtime.index') }}" class="progress-row" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">
                            <div class="progress-icon" style="background:#fffbeb;"><svg width="15" height="15" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="#d97706" stroke-width="1.4" /></svg></div>
                            <div style="min-width:0;flex:1;">
                                <p class="progress-title">Permintaan Lembur</p>
                                <p class="progress-desc">{{ $latestLembur->qty_jam }} jam lembur • Status: {{ ucfirst($latestLembur->sts_lbr) }}</p>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:8px;"><span class="progress-time">{{ \Carbon\Carbon::parse($latestLembur->created_at)->diffForHumans() }}</span></div>
                    </a>
                @endif
            </div>
        </div>
    </div>{{-- end .dash-wrap --}}
@endsection

@push('scripts')
    <script>
        (function () {
            var carousel = document.getElementById('hero-carousel'), track = document.getElementById('hero-track'), total = {{ max(count($announcements), 1) }}, startX = 0, isDrag = false; window._hi = 0;
            function goTo(idx) {
                idx = Math.max(0, Math.min(total - 1, idx)); window._hi = idx; track.style.transform = 'translateX(-' + (idx * carousel.offsetWidth) + 'px)';
                for (var i = 0; i < total; i++) { var d = document.getElementById('dot-' + i); if (!d) continue; d.style.width = i === idx ? '24px' : '5px'; d.style.background = i === idx ? '#9333ea' : '#d4d4d8'; }
            }
            window.heroGo = goTo;
            /* Reposisi saat resize tanpa animasi */
            window.addEventListener('resize', function () { track.style.transition = 'none'; goTo(window._hi); setTimeout(function () { track.style.transition = ''; }, 50); });
            /* ── TOUCH (mobile) ── */
            track.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; isDrag = true; track.style.transition = 'none'; }, { passive: true });
            track.addEventListener('touchmove', function (e) { if (!isDrag) return; var diff = e.touches[0].clientX - startX; track.style.transform = 'translateX(' + (-window._hi * carousel.offsetWidth + diff) + 'px)'; }, { passive: true });
            track.addEventListener('touchend', function (e) { if (!isDrag) return; isDrag = false; track.style.transition = ''; var diff = e.changedTouches[0].clientX - startX; if (diff < -50) goTo(window._hi + 1); else if (diff > 50) goTo(window._hi - 1); else goTo(window._hi); });
            /* ── MOUSE DRAG (desktop/laptop) ── */
            track.addEventListener('mousedown', function (e) { startX = e.clientX; isDrag = true; track.style.transition = 'none'; e.preventDefault(); });
            window.addEventListener('mousemove', function (e) { if (!isDrag) return; var diff = e.clientX - startX; track.style.transform = 'translateX(' + (-window._hi * carousel.offsetWidth + diff) + 'px)'; });
            window.addEventListener('mouseup', function (e) { if (!isDrag) return; isDrag = false; track.style.transition = ''; var diff = e.clientX - startX; if (diff < -50) goTo(window._hi + 1); else if (diff > 50) goTo(window._hi - 1); else goTo(window._hi); });
            goTo(0);
        })();
    </script>
@endpush