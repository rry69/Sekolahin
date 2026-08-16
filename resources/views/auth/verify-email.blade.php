<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div id="verified-banner" class="hidden mb-4 font-medium text-sm text-green-700 bg-green-100 border border-green-400 px-4 py-3 rounded">
        Email Anda berhasil diverifikasi! Anda akan diarahkan...
    </div>

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    @push('scripts')
        <script>
            (function () {
                let verified = {{ $isVerified ? 'true' : 'false' }};
                if (verified) {
                    showVerified();
                    return;
                }

                const timer = setInterval(async function () {
                    try {
                        const res = await fetch('{{ route('verification.status') }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data.verified && !verified) {
                            verified = true;
                            clearInterval(timer);
                            showVerified();
                        }
                    } catch (e) {
                        /* ignore transient errors, keep polling */
                    }
                }, 2000);

                function showVerified() {
                    const banner = document.getElementById('verified-banner');
                    const actions = document.querySelector('form[action$="logout"]');
                    if (banner) banner.classList.remove('hidden');
                    if (actions) actions.style.display = 'none';
                    setTimeout(function () {
                        window.location.href = '{{ route('dashboard') }}';
                    }, 2500);
                }
            })();
        </script>
    @endpush
</x-guest-layout>
