@php
    use App\Models\Notification;

    $notifications = collect();
    $unreadCount = 0;
    $notificationReadAllUrl = '#';
    $notificationBaseUrl = '#';
    $notificationReadBaseUrl = '#';

    if (Auth::check()) {
        $notifications = Notification::where('id_kry', Auth::user()->id_kry)
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = $notifications->where('is_read', false)->count();

        if (Auth::user()->role == 'owner') {
            $notificationReadAllUrl = route('owner.notifications.markAllRead');
            $notificationBaseUrl = url('/owner/notifications');
            $notificationReadBaseUrl = url('/owner/notifications/read');
        } else {
            $notificationReadAllUrl = route('staff.notifications.readAll');
            $notificationBaseUrl = url('/notifications');
            $notificationReadBaseUrl = url('/notifications/read');
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Poly Games Cafe')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Roboto+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'mono': ['Roboto Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>

<body class="bg-white pgc-body">
    <div class="flex h-screen overflow-hidden">
        {{-- ===================== SIDEBAR ===================== --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-20 lg:hidden hidden" onclick="toggleSidebar()">
        </div>

        <aside id="sidebar"
            class="fixed lg:static z-30 w-64 h-full bg-white border-r border-zinc-100 flex flex-col flex-shrink-0 -translate-x-full lg:translate-x-0 transition-transform duration-300">
            {{-- Logo --}}
            <div class="flex items-center gap-2.5 px-6 py-8">
                <div class="w-9 h-9 bg-purple-600/10 rounded-2xl flex justify-center items-center">
                    <i data-lucide="layout-grid" class="w-5 h-5 text-purple-600"></i>
                </div>
                <span class="text-zinc-900 text-base font-bold font-mono leading-7">Poly Games Cafe</span>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 pb-4">
                <p
                    class="px-4 mb-3 text-gray-500 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide opacity-60">
                    Main Menu</p>
                <ul class="space-y-1">
                    @if (Auth::user()->role == 'owner')
                        <li>
                            <a href="{{ route('owner.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('owner.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="layout-dashboard"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('owner.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('owner.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Beranda</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.employees.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('owner.employees.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="users"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('owner.employees.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('owner.employees.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Staff</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.evaluasi.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('owner.evaluasi.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="file-text"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('owner.evaluasi.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('owner.evaluasi.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Evaluasi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.overtime.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('owner.overtime.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="clock"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('owner.overtime.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('owner.overtime.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Permintaan
                                    Lembur</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.allowance.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('owner.allowance.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="utensils-crossed"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('owner.allowance.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('owner.allowance.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Permintaan
                                    Pinjaman</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('owner.payroll.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('owner.payroll.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="wallet"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('owner.payroll.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('owner.payroll.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Slip
                                    Gaji</span>
                            </a>
                        </li>
                    @endif

                    @if (Auth::user()->role == 'staff')
                        <li>
                            <a href="{{ route('staff.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('staff.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="layout-dashboard"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('staff.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('staff.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.evaluations.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('staff.evaluations.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="file-text"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('staff.evaluations.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('staff.evaluations.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Evaluasi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.payroll.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('staff.payroll.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="wallet"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('staff.payroll.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('staff.payroll.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Slip
                                    Gaji</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.food-allowance.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('staff.food-allowance.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="utensils-crossed"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('staff.food-allowance.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('staff.food-allowance.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Pinjaman</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.overtime.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('staff.overtime.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="clock"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('staff.overtime.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('staff.overtime.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Lembur</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.settings.index') }}"
                                class="flex items-center gap-3 pl-4 h-10 rounded-2xl transition-colors {{ request()->routeIs('staff.settings.index') ? 'bg-purple-600/5 shadow-[0px_2px_10px_-4px_rgba(147,51,234,0.10)]' : 'hover:bg-zinc-50' }}">
                                <i data-lucide="settings"
                                    class="w-4 h-4 flex-shrink-0 {{ request()->routeIs('staff.settings.index') ? 'text-purple-600' : 'text-gray-500' }}"></i>
                                <span
                                    class="text-sm font-inter leading-5 {{ request()->routeIs('staff.settings.index') ? 'text-purple-600 font-semibold' : 'text-gray-600 font-medium' }}">Pengaturan</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
        </aside>

        {{-- ===================== MAIN WRAPPER ===================== --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            {{-- Topbar --}}
            <header
                class="h-16 bg-white/80 backdrop-blur border-b border-zinc-100 flex items-center px-6 lg:px-8 flex-shrink-0 z-10">
                <div class="flex items-center justify-between w-full gap-4">
                    <button onclick="toggleSidebar()"
                        class="lg:hidden p-2 rounded-xl hover:bg-zinc-100 transition-colors">
                        <i data-lucide="menu" class="w-5 h-5 text-zinc-600"></i>
                    </button>
                    <div class="flex-1"></div>
                    <div class="flex items-center gap-2 border-l border-zinc-100 pl-4">
                        <div class="relative">
                            <button id="notif-btn" onclick="toggleNotif()"
                                class="relative w-9 h-9 flex items-center justify-center rounded-2xl hover:bg-zinc-100 transition-colors">
                                <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
                                @if($unreadCount > 0)
                                    <span id="notif-badge-dot"
                                        class="absolute top-2 right-2 w-2 h-2 bg-purple-600 rounded-full border-2 border-white"></span>
                                @endif
                            </button>

                            {{-- ══ NOTIF DROPDOWN — owner dan staff ══ --}}
                            @if (Auth::check())
                                <div id="notif-dropdown"
                                    class="hidden absolute right-0 top-12 w-[22rem] bg-white rounded-[1.5rem] shadow-[0px_25px_50px_-12px_rgba(0,0,0,0.15)] border border-zinc-100 z-50 overflow-hidden opacity-0 scale-95 transition-all duration-200 origin-top-right">
                                    {{-- Header --}}
                                    <div
                                        class="h-14 px-5 bg-gray-50/50 border-b border-zinc-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-xs font-black uppercase tracking-wider text-zinc-900 font-inter">Notifikasi</span>
                                            <span id="notif-count-badge"
                                                class="px-2 py-0.5 bg-purple-600 rounded-full text-[9px] font-black text-white font-inter">{{ $unreadCount }}
                                                NEW</span>
                                        </div>
                                        <button onclick="markAllNotifRead()"
                                            class="text-[9px] font-black uppercase tracking-wide text-purple-600 hover:text-purple-800 transition-colors font-inter">Tandai
                                            Semua Dibaca</button>
                                    </div>

                                    {{-- Items --}}
                                    <div id="notif-list" class="divide-y divide-zinc-100/60 max-h-[420px] overflow-y-auto">
                                        @forelse($notifications as $notif)
                                            <div class="notif-item relative flex gap-4 px-5 py-4 hover:bg-zinc-50/60 transition-colors cursor-pointer"
                                                data-id="{{ $notif->id_notification }}"
                                                data-read="{{ $notif->is_read ? 'true' : 'false' }}">
                                                @if(!$notif->is_read)
                                                    <div
                                                        class="notif-unread-bar absolute left-0 top-0 bottom-0 w-[3px] bg-purple-600 rounded-r">
                                                    </div>
                                                @endif
                                                @php

                                                    $iconBg = 'bg-purple-50 border-purple-100';
                                                    $iconColor = 'text-purple-600';
                                                    $icon = 'bell';

                                                    if ($notif->type == 'payroll') {
                                                        $iconBg = 'bg-emerald-50 border-emerald-100';
                                                        $iconColor = 'text-emerald-500';
                                                        $icon = 'wallet';
                                                    } elseif ($notif->type == 'announcement') {
                                                        $iconBg = 'bg-purple-50 border-purple-100';
                                                        $iconColor = 'text-purple-600';
                                                        $icon = 'megaphone';
                                                    } elseif ($notif->type == 'evaluation') {
                                                        $iconBg = 'bg-amber-50 border-amber-100';
                                                        $iconColor = 'text-amber-500';
                                                        $icon = 'star';
                                                    } elseif ($notif->type == 'overtime') {
                                                        $iconBg = 'bg-sky-50 border-sky-100';
                                                        $iconColor = 'text-sky-500';
                                                        $icon = 'clock';
                                                    } elseif ($notif->type == 'allowance') {
                                                        $iconBg = 'bg-amber-50 border-amber-100';
                                                        $iconColor = 'text-amber-500';
                                                        $icon = 'utensils-crossed';
                                                    } elseif ($notif->type == 'info') {
                                                        $iconBg = 'bg-blue-50 border-blue-100';
                                                        $iconColor = 'text-blue-500';
                                                        $icon = 'info';
                                                    }

                                                @endphp

                                                <div
                                                    class="w-10 h-10 shrink-0 {{ $iconBg }} rounded-2xl border flex items-center justify-center">
                                                    <i data-lucide="{{ $icon }}" class="w-4 h-4 {{ $iconColor }}"></i>
                                                </div>
                                                <div class="flex flex-col gap-1 flex-1 min-w-0">
                                                    <div class="flex justify-between items-start gap-2">
                                                        <div class="flex-1 min-w-0">
                                                            <span
                                                                class="text-sm font-bold leading-tight font-inter {{ $notif->is_read ? 'text-zinc-900/70' : 'text-zinc-900' }}">
                                                                {{ $notif->title }}
                                                            </span>
                                                        </div>

                                                        <div class="flex items-center gap-2 shrink-0">
                                                            <span
                                                                class="text-[9px] font-black uppercase text-gray-400 tracking-wide whitespace-nowrap font-inter">
                                                                {{ $notif->created_at->diffForHumans() }}
                                                            </span>
                                                            <button
                                                                onclick="deleteNotification(event, {{ $notif->id_notification }})"
                                                                class="w-5 h-5 flex items-center justify-center rounded-full hover:bg-red-50 text-gray-400 hover:text-red-500 transition-all">
                                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-500 leading-relaxed font-inter">
                                                        {{ $notif->message }}
                                                    </p>
                                                    @php

                                                        $priorityBg = 'bg-zinc-100';
                                                        $priorityText = 'text-zinc-500';

                                                        if ($notif->priority == 'high') {
                                                            $priorityBg = 'bg-rose-50';
                                                            $priorityText = 'text-rose-600';
                                                        } elseif ($notif->priority == 'medium') {
                                                            $priorityBg = 'bg-amber-50';
                                                            $priorityText = 'text-amber-600';
                                                        } elseif ($notif->priority == 'low') {
                                                            $priorityBg = 'bg-zinc-100';
                                                            $priorityText = 'text-zinc-500';
                                                        }

                                                    @endphp
                                                    <span
                                                        class="self-start mt-0.5 px-2 py-0.5 {{ $priorityBg }} rounded text-[8px] font-black uppercase {{ $priorityText }} tracking-wide font-inter">
                                                        {{ $notif->priority }} priority
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="flex items-center justify-center h-32">
                                                <p class="text-xs text-gray-400 font-inter">
                                                    Tidak ada notifikasi
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="relative">
                            <button onclick="toggleUserDropdown()"
                                class="flex items-center gap-3 ml-1 p-1.5 rounded-2xl hover:bg-zinc-50 transition-all">
                                <div
                                    class="w-8 h-8 bg-purple-600/10 rounded-xl outline outline-1 outline-purple-600/5 flex items-center justify-center">
                                    <span
                                        class="text-purple-600 text-xs font-bold font-inter leading-4">{{ strtoupper(substr(Auth::user()->n_kry, 0, 1)) }}</span>
                                </div>
                                <span
                                    class="hidden sm:block text-zinc-900/80 text-xs font-bold font-inter leading-4">{{ Auth::user()->n_kry }}</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-zinc-400"></i>
                            </button>

                            <div id="user-dropdown"
                                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-[0px_20px_40px_-12px_rgba(0,0,0,0.1)] outline outline-1 outline-zinc-100 overflow-hidden z-50 transition-all duration-200">
                                <div class="px-4 py-3 border-b border-zinc-50">
                                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-wider">Signed in
                                        as</p>
                                    <p class="text-xs font-bold text-zinc-700 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="p-1.5">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                            <i data-lucide="log-out" class="w-4 h-4"></i>
                                            <span class="text-xs font-bold font-inter">Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function toggleUserDropdown() {
                            const dropdown = document.getElementById('user-dropdown');
                            dropdown.classList.toggle('hidden');
                            const notifDropdownEl = document.getElementById('notif-dropdown');
                            if (notifDropdownEl) notifDropdownEl.classList.add('hidden');
                        }
                        window.addEventListener('click', function (e) {
                            const dropdown = document.getElementById('user-dropdown');
                            const btn = dropdown.previousElementSibling;
                            if (!btn.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.add('hidden');
                        });
                    </script>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-[#FDFDFD]">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')

    <script>
        lucide.createIcons();
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        const notifBtn = document.getElementById('notif-btn');
        const notifDropdown = document.getElementById('notif-dropdown');
        let notifOpen = false;

        function toggleNotif() { notifOpen ? closeNotif() : openNotif(); }
        function openNotif() {
            if (!notifDropdown) return;
            notifOpen = true;
            notifDropdown.classList.remove('hidden');
            requestAnimationFrame(() => requestAnimationFrame(() => {
                notifDropdown.classList.remove('opacity-0', 'scale-95');
                notifDropdown.classList.add('opacity-100', 'scale-100');
            }));
        }
        function closeNotif() {
            if (!notifDropdown) return;
            notifOpen = false;
            notifDropdown.classList.remove('opacity-100', 'scale-100');
            notifDropdown.classList.add('opacity-0', 'scale-95');
            setTimeout(() => notifDropdown.classList.add('hidden'), 200);
        }
        document.addEventListener('click', e => {
            if (notifOpen && notifBtn && !notifBtn.contains(e.target) && notifDropdown && !notifDropdown.contains(e.target)) closeNotif();
        });

        const notificationReadAllUrl = @json($notificationReadAllUrl);
        const notificationBaseUrl = @json($notificationBaseUrl);
        const notificationReadBaseUrl = @json($notificationReadBaseUrl);

        async function markAllNotifRead() {
            await fetch(notificationReadAllUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            document.querySelectorAll('.notif-item').forEach(function (item) {
                item.dataset.read = 'true';
                var bar = item.querySelector('.notif-unread-bar');
                if (bar) bar.remove();
                var title = item.querySelector('span.font-bold');
                if (title) { title.classList.remove('text-zinc-900'); title.classList.add('text-zinc-900/70'); }
            });
            if (document.getElementById('notif-count-badge')) document.getElementById('notif-count-badge').classList.add('hidden');
            if (document.getElementById('notif-badge-dot')) document.getElementById('notif-badge-dot').remove();
        }

        async function deleteNotification(event, notifId) {

            event.stopPropagation();

            await fetch(`${notificationBaseUrl}/${notifId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const notifItem = document.querySelector(`[data-id="${notifId}"]`);

            if (notifItem) {
                notifItem.remove();
            }

            const unread = document.querySelectorAll('.notif-item[data-read="false"]').length;

            const countBadge = document.getElementById('notif-count-badge');

            const badgeDot = document.getElementById('notif-badge-dot');

            if (unread === 0) {

                if (countBadge) {
                    countBadge.classList.add('hidden');
                }

                if (badgeDot) {
                    badgeDot.remove();
                }

            } else {

                if (countBadge) {
                    countBadge.textContent = unread + ' NEW';
                }
            }
        }

        document.querySelectorAll('.notif-item').forEach(function (item) {
            item.addEventListener('click', async function () {
                if (item.dataset.read === 'false') {
                    const notifId = item.dataset.id;
                    await fetch(`${notificationReadBaseUrl}/${notifId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    item.dataset.read = 'true';
                    var bar = item.querySelector('.notif-unread-bar');
                    if (bar) bar.remove();
                    var title = item.querySelector('span.font-bold');
                    if (title) { title.classList.remove('text-zinc-900'); title.classList.add('text-zinc-900/70'); }

                    var unread = document.querySelectorAll('.notif-item[data-read="false"]').length;
                    var countBadge = document.getElementById('notif-count-badge');
                    var badgeDot = document.getElementById('notif-badge-dot');

                    if (unread === 0) {
                        if (countBadge) countBadge.classList.add('hidden');
                        if (badgeDot) badgeDot.remove();
                    } else {
                        if (countBadge) countBadge.textContent = unread + ' NEW';
                    }
                }
            });
        });

        function openPanel(bd, pn) {
            bd.classList.remove('hidden');
            requestAnimationFrame(() => requestAnimationFrame(() => {
                pn.classList.remove('opacity-0', 'scale-95');
                pn.classList.add('opacity-100', 'scale-100');
            }));
        }
        function closePanel(bd, pn) {
            pn.classList.remove('opacity-100', 'scale-100');
            pn.classList.add('opacity-0', 'scale-95');
            setTimeout(() => bd.classList.add('hidden'), 200);
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                if (notifOpen) closeNotif();
                document.querySelectorAll('[data-modal-backdrop]').forEach(bd => {
                    const pn = document.getElementById(bd.dataset.modalPanel);
                    if (pn && !bd.classList.contains('hidden')) closePanel(bd, pn);
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>