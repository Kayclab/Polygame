@extends('layouts.app')

@section('title', 'Settings - Poly Games Cafe')

@section('content')
<div class="px-6 sm:px-10 lg:px-16 xl:px-24 py-8 max-w-[1100px] mx-auto">

    {{-- ---- Page Header ---- --}}
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-2">
            <i data-lucide="shield" class="w-4 h-4 text-purple-600"></i>
            <span class="text-purple-600 text-[10px] font-bold font-inter uppercase leading-4 tracking-wide">Admin Control Panel</span>
        </div>
        <h1 class="text-zinc-900 text-3xl font-bold font-mono leading-9 mb-3">Settings</h1>
        <p class="text-gray-500 text-sm font-normal font-mono leading-6 opacity-80 max-w-xl">
            Manage your account profile and core store information. As the owner, you can modify these details below.
        </p>
    </div>

    {{-- ---- Cards Grid ---- --}}
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf @method('PUT')

        <div class="flex flex-col gap-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ===== PROFILE INFO CARD ===== --}}
                <div class="pgc-card px-8 pt-8 pb-8 flex flex-col gap-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 opacity-60">
                            <i data-lucide="user" class="w-4 h-4 text-zinc-900"></i>
                            <span class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider">Profile Info</span>
                        </div>
                        <span class="px-2.5 py-1 bg-purple-600/5 rounded-xl outline outline-1 outline-purple-600/10 text-purple-600 text-[9px] font-bold font-inter uppercase leading-3 tracking-wide shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">Owner</span>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="flex flex-col gap-1.5">
                            <label for="full_name" class="pgc-label">Full Name</label>
                            <input id="full_name" type="text" name="full_name"
                                   value="{{ old('full_name', auth()->user()->name ?? 'Sachio Senna') }}"
                                   class="pgc-input" required />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="pgc-label">Email Address</label>
                            <input id="email" type="email" name="email"
                                   value="{{ old('email', auth()->user()->email ?? 'sachio@company.com') }}"
                                   class="pgc-input" required />
                        </div>
                    </div>
                </div>

                {{-- ===== SECURITY CARD ===== --}}
                <div class="pgc-card px-8 pt-8 pb-8 flex flex-col gap-8">
                    <div class="flex items-center gap-2 opacity-60">
                        <i data-lucide="lock" class="w-4 h-4 text-zinc-900"></i>
                        <span class="text-zinc-900 text-sm font-bold font-inter uppercase leading-5 tracking-wider">Security</span>
                    </div>
                    <div class="flex flex-col gap-4">

                        {{-- Current Password --}}
                        <div class="flex flex-col gap-1.5">
                            <label for="current_password" class="pgc-label">Current Password</label>
                            <div class="relative">
                                <i data-lucide="key" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 opacity-40 pointer-events-none"></i>
                                <input id="current_password" type="password" name="current_password"
                                       placeholder="••••••••"
                                       class="pgc-input pl-11 pr-11 placeholder:text-zinc-900/50" />
                                <button type="button" onclick="togglePassword(this)"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- New Password --}}
                        <div class="flex flex-col gap-1.5">
                            <label for="new_password" class="pgc-label">New Password</label>
                            <div class="relative">
                                <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 opacity-40 pointer-events-none"></i>
                                <input id="new_password" type="password" name="new_password"
                                       placeholder="••••••••"
                                       class="pgc-input pl-11 pr-11 placeholder:text-zinc-900/50" />
                                <button type="button" onclick="togglePassword(this)"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Confirm New Password --}}
                        <div class="flex flex-col gap-1.5">
                            <label for="confirm_password" class="pgc-label">Confirm New Password</label>
                            <div class="relative">
                                <i data-lucide="shield-check" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 opacity-40 pointer-events-none"></i>
                                <input id="confirm_password" type="password" name="confirm_password"
                                       placeholder="••••••••"
                                       class="pgc-input pl-11 pr-11 placeholder:text-zinc-900/50" />
                                <button type="button" onclick="togglePassword(this)"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Change Password Button --}}
                        <button type="button"
                                class="pgc-btn-primary w-full h-12 rounded-2xl shadow-[0px_4px_6px_-4px_rgba(147,51,234,0.20),0px_10px_15px_-3px_rgba(147,51,234,0.20)] mt-2">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                            Change Password
                        </button>

                    </div>
                </div>

            </div>

            {{-- ===== UPDATE SETTINGS BUTTON ===== --}}
            <div class="flex justify-end pb-4">
                <button type="submit"
                        class="pgc-btn-primary h-12 px-8 rounded-2xl shadow-[0px_8px_10px_-6px_rgba(147,51,234,0.20),0px_20px_25px_-5px_rgba(147,51,234,0.20)]">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Update Settings
                </button>
            </div>

        </div>
    </form>

</div>
@endsection


{{-- ===================== SCRIPTS ===================== --}}
@push('scripts')
<script>
    function togglePassword(btn) {
        const input = btn.previousElementSibling;
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
@endpush