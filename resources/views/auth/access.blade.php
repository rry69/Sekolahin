<x-guest-layout :title="__($mode === 'register' ? 'Daftar' : 'Masuk')">
    @php
        // Mode aktif: bisa dari URL (mode), atau dari redirect balik setelah error validasi.
        $registerActive = $mode === 'register'
            || $errors->has('name')
            || $errors->has('password')
            || $errors->has('password_confirmation')
            || $errors->has('terms')
            || old('name') !== null;
        $loginActive = ! $registerActive;

        // Kelas input: rounded, background putih lembut, fokus -> outline coral
        $inputBase = 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-sm text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-[#FF6B6B] focus:outline-none focus:ring-4 focus:ring-[#FF6B6B]/10';
        $inputErr  = ' border-red-300 bg-red-50 focus:border-red-400 focus:ring-red-100';
    @endphp

    <style>
        /* Switcher Masuk <-> Register: kedua panel di-stack (grid), transisi murni CSS. */
        #auth-switcher { display: grid; }
        #auth-switcher > .auth-panel {
            grid-area: 1 / 1;
            transition: opacity .25s ease, transform .25s ease, visibility 0s .25s;
        }
        #auth-switcher > .auth-panel.is-active {
            opacity: 1;
            transform: translateX(0);
            visibility: visible;
            pointer-events: auto;
        }
        #auth-switcher > .auth-panel.is-out-left {
            opacity: 0;
            transform: translateX(-16px);
            visibility: hidden;
            pointer-events: none;
        }
        #auth-switcher > .auth-panel.is-out-right {
            opacity: 0;
            transform: translateX(16px);
            visibility: hidden;
            pointer-events: none;
        }
        @media (prefers-reduced-motion: reduce) {
            #auth-switcher > .auth-panel { transition: none !important; }
            .btn-submit svg.hi { transition: none !important; }
        }

        /* Tab pil Masuk / Daftar */
        .auth-tab {
            position: relative;
            z-index: 1;
            flex: 1;
            padding: .55rem 1rem;
            font-size: .875rem;
            font-weight: 600;
            color: #9CA3AF;
            border-radius: .65rem;
            transition: color .2s ease-out;
            text-align: center;
        }
        .auth-tab.is-active { color: #fff; }

        /* Indikator geser tab */
        #tab-indicator {
            position: absolute;
            top: 4px; bottom: 4px;
            width: calc(50% - 4px);
            border-radius: .65rem;
            background: linear-gradient(135deg, #FF6B6B, #FF8E6E);
            box-shadow: 0 4px 12px -4px rgba(255,107,107,.6);
            transition: left .25s ease-out;
        }
        #tab-indicator.pos-login { left: 4px; }
        #tab-indicator.pos-register { left: 50%; }

        /* Tombol submit: panah bergeser saat hover */
        .btn-submit svg.hi { transition: transform .2s ease-out; }
        .btn-submit:hover svg.hi { transform: translateX(4px); }

        /* Meter kekuatan sandi */
        .pw-bar { height: 5px; border-radius: 9999px; background: #E5E7EB; transition: background-color .2s ease-out; }
    </style>

    <div class="px-1 py-1">

        {{-- ===== Tab pil: Masuk | Daftar ===== --}}
        <div id="auth-tabs" role="tablist" aria-label="Mode autentikasi"
             class="relative mb-7 flex rounded-xl p-1" style="background: #FFF0EE;">
            <span id="tab-indicator" class="{{ $loginActive ? 'pos-login' : 'pos-register' }}" aria-hidden="true"></span>
            <button type="button" id="tab-login" role="tab" aria-selected="{{ $loginActive ? 'true' : 'false' }}"
                    onclick="return switchMode('login')"
                    class="auth-tab cursor-pointer {{ $loginActive ? 'is-active' : '' }}">Masuk</button>
            <button type="button" id="tab-register" role="tab" aria-selected="{{ $registerActive ? 'true' : 'false' }}"
                    onclick="return switchMode('register')"
                    class="auth-tab cursor-pointer {{ $registerActive ? 'is-active' : '' }}">Daftar</button>
        </div>

        <div id="auth-switcher">

            {{-- ================= PANEL LOGIN ================= --}}
            <div id="panel-login" class="auth-panel {{ $loginActive ? 'is-active' : 'is-out-right' }}" role="tabpanel">

                @if (session('status'))
                    <div class="mb-4 flex items-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-green-700" style="background: #E1F5F1; border: 1px solid rgba(45,201,156,.3);">
                        <x-hi icon="fa-circle-check" class="text-green-600" />
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-6">
                    <h1 class="text-[24px] font-extrabold tracking-tight text-gray-900">Masuk ke akun Anda</h1>
                    <p class="mt-1.5 text-sm text-gray-500">Masukkan email dan kata sandi untuk melanjutkan.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="login-email" class="mb-1.5 block text-xs font-semibold text-gray-700">{{ __('Email') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><x-hi icon="fa-envelope" class="text-sm" /></span>
                            <input id="login-email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com"
                                   class="{{ $inputBase }} pl-10 {{ $errors->has('email') ? $inputErr : '' }}">
                        </div>
                        @error('email')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500"><x-hi icon="fa-circle-exclamation" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="login-password" class="mb-1.5 block text-xs font-semibold text-gray-700">{{ __('Kata Sandi') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><x-hi icon="fa-lock" class="text-sm" /></span>
                            <input id="login-password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi"
                                   class="{{ $inputBase }} pl-10 pr-11 {{ $errors->has('password') ? $inputErr : '' }}">
                            <button type="button" onclick="togglePassword(this)" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 cursor-pointer items-center justify-center rounded-lg text-gray-400 transition hover:bg-[#FFF0EE] hover:text-[#FF6B6B]" aria-label="Tampilkan kata sandi">
                                <x-hi icon="fa-eye" class="text-sm" />
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500"><x-hi icon="fa-circle-exclamation" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-[13px] text-gray-500">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="h-4 w-4 cursor-pointer rounded border-gray-300 shadow-sm focus:ring-[#FF8E6E]" style="accent-color:#FF6B6B;">
                            {{ __('Ingat saya') }}
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[13px] font-semibold text-[#FF6B6B] hover:text-[#E8555B] hover:underline">
                                {{ __('Lupa kata sandi?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-submit flex h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl text-[15px] font-semibold text-white transition active:scale-[.985] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#FF8E6E]/40"
                            style="background: linear-gradient(135deg, #FF6B6B, #FF8E6E); box-shadow: 0 10px 20px -8px rgba(255,107,107,.6);">
                        {{ __('Masuk') }} <x-hi icon="fa-arrow-right" />
                    </button>
                </form>
            </div>

            {{-- ================= PANEL REGISTER ================= --}}
            <div id="panel-register" class="auth-panel {{ $registerActive ? 'is-active' : 'is-out-left' }}" role="tabpanel">

                <div class="mb-6">
                    <h1 class="text-[24px] font-extrabold tracking-tight text-gray-900">Buat akun Sekolahin</h1>
                    <p class="mt-1.5 text-sm text-gray-500">Isi data diri Anda untuk memulai pendaftaran.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="reg-name" class="mb-1.5 block text-xs font-semibold text-gray-700">{{ __('Nama Lengkap') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><x-hi icon="fa-user" class="text-sm" /></span>
                            <input id="reg-name" type="text" name="name" :value="old('name')" required autocomplete="name" placeholder="Nama sesuai identitas"
                                   class="{{ $inputBase }} pl-10 {{ $errors->has('name') ? $inputErr : '' }}">
                        </div>
                        @error('name')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500"><x-hi icon="fa-circle-exclamation" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="reg-email" class="mb-1.5 block text-xs font-semibold text-gray-700">{{ __('Email') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><x-hi icon="fa-envelope" class="text-sm" /></span>
                            <input id="reg-email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com"
                                   class="{{ $inputBase }} pl-10 {{ $errors->has('email') ? $inputErr : '' }}">
                        </div>
                        @error('email')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500"><x-hi icon="fa-circle-exclamation" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="reg-password" class="mb-1.5 block text-xs font-semibold text-gray-700">{{ __('Kata Sandi') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><x-hi icon="fa-lock" class="text-sm" /></span>
                            <input id="reg-password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                                   class="{{ $inputBase }} pl-10 pr-11 {{ $errors->has('password') ? $inputErr : '' }}"
                                   oninput="updateStrength(this.value)">
                            <button type="button" onclick="togglePassword(this)" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 cursor-pointer items-center justify-center rounded-lg text-gray-400 transition hover:bg-[#FFF0EE] hover:text-[#FF6B6B]" aria-label="Tampilkan kata sandi">
                                <x-hi icon="fa-eye" class="text-sm" />
                            </button>
                        </div>
                        {{-- Meter kekuatan sandi --}}
                        <div id="strength-wrap" class="mt-2 hidden" aria-live="polite">
                            <div class="flex gap-1.5">
                                <span class="pw-bar flex-1"></span><span class="pw-bar flex-1"></span><span class="pw-bar flex-1"></span><span class="pw-bar flex-1"></span>
                            </div>
                            <p id="strength-text" class="mt-1 text-[11px] font-medium text-gray-400">Kekuatan sandi</p>
                        </div>
                        @error('password')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500"><x-hi icon="fa-circle-exclamation" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="reg-password_confirmation" class="mb-1.5 block text-xs font-semibold text-gray-700">{{ __('Ulangi Kata Sandi') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><x-hi icon="fa-lock" class="text-sm" /></span>
                            <input id="reg-password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang kata sandi"
                                   class="{{ $inputBase }} pl-10 pr-11 {{ $errors->has('password_confirmation') ? $inputErr : '' }}">
                            <button type="button" onclick="togglePassword(this)" class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 cursor-pointer items-center justify-center rounded-lg text-gray-400 transition hover:bg-[#FFF0EE] hover:text-[#FF6B6B]" aria-label="Tampilkan kata sandi">
                                <x-hi icon="fa-eye" class="text-sm" />
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-500"><x-hi icon="fa-circle-exclamation" /> {{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Terms -->
                    <label class="flex cursor-pointer items-start gap-2 text-[13px] text-gray-500">
                        <input type="checkbox" name="terms" value="1" required
                               class="mt-0.5 h-4 w-4 cursor-pointer rounded border-gray-300 shadow-sm focus:ring-[#FF8E6E]" style="accent-color:#FF6B6B;">
                        <span>Saya menyetujui <a href="{{ route('terms') }}" target="_blank" rel="noopener" class="font-semibold text-[#FF6B6B] hover:underline">syarat &amp; ketentuan</a></span>
                    </label>

                    <button type="submit" class="btn-submit flex h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl text-[15px] font-semibold text-white transition active:scale-[.985] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#FF8E6E]/40"
                            style="background: linear-gradient(135deg, #FF6B6B, #FF8E6E); box-shadow: 0 10px 20px -8px rgba(255,107,107,.6);">
                        {{ __('Daftar') }} <x-hi icon="fa-arrow-right" />
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePassword(btn) {
            var input = btn.parentElement.querySelector('input');
            var icon = btn.querySelector('svg.hi');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            if (icon) icon.outerHTML = hiSvg(show ? 'fa-eye-slash' : 'fa-eye', 'class="text-sm"');
        }

        /* Meter kekuatan sandi (register) — skor sederhana: panjang + keragaman karakter */
        function updateStrength(value) {
            var wrap = document.getElementById('strength-wrap');
            if (!wrap) return;
            var bars = wrap.querySelectorAll('.pw-bar');
            var label = document.getElementById('strength-text');

            if (!value) { wrap.classList.add('hidden'); return; }
            wrap.classList.remove('hidden');

            var score = 0;
            if (value.length >= 8) score++;
            if (value.length >= 12) score++;
            if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
            if (/\d/.test(value) || /[^A-Za-z0-9]/.test(value)) score++;
            score = Math.max(value.length ? 1 : 0, Math.min(score, 4));

            var colors = ['#EF4444', '#F59E0B', '#84CC16', '#22C55E'];
            var texts = ['Lemah', 'Cukup', 'Baik', 'Kuat'];
            var textColor = ['#DC2626', '#D97706', '#65A30D', '#16A34A'];

            bars.forEach(function (bar, i) {
                bar.style.backgroundColor = i < score ? colors[score - 1] : '#E5E7EB';
            });
            label.textContent = 'Kekuatan sandi: ' + texts[score - 1];
            label.style.color = textColor[score - 1];
        }

        (function () {
            var panelLogin = document.getElementById('panel-login');
            var panelRegister = document.getElementById('panel-register');
            var tabLogin = document.getElementById('tab-login');
            var tabRegister = document.getElementById('tab-register');
            var indicator = document.getElementById('tab-indicator');
            var current = panelRegister.classList.contains('is-active') ? 'register' : 'login';

            function focusFirst(panel) {
                var el = panel.querySelector('input');
                if (el) el.focus();
            }

            function applyMode(mode) {
                if (mode === current) return;
                current = mode;

                var isLogin = mode === 'login';
                (isLogin ? panelLogin : panelRegister).classList.remove('is-out-left', 'is-out-right');
                (isLogin ? panelLogin : panelRegister).classList.add('is-active');
                (isLogin ? panelRegister : panelLogin).classList.remove('is-active');
                (isLogin ? panelRegister : panelLogin).classList.add(isLogin ? 'is-out-left' : 'is-out-right');

                tabLogin.classList.toggle('is-active', isLogin);
                tabLogin.setAttribute('aria-selected', isLogin ? 'true' : 'false');
                tabRegister.classList.toggle('is-active', !isLogin);
                tabRegister.setAttribute('aria-selected', isLogin ? 'false' : 'true');
                indicator.className = isLogin ? 'pos-login' : 'pos-register';

                focusFirst(isLogin ? panelLogin : panelRegister);
            }

            window.switchMode = function (mode) {
                applyMode(mode);
                history.replaceState(null, '', mode === 'login' ? '/login' : '/register');
                return false; // batalkan navigasi default
            };

            // Dukung tombol back/forward browser
            window.addEventListener('popstate', function () {
                var path = window.location.pathname.replace(/\/+$/, '');
                applyMode(path.indexOf('/register') !== -1 ? 'register' : 'login');
            });
        })();
    </script>
    @endpush
</x-guest-layout>
