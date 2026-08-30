<section>
    <div class="prf-sec-label">Informasi Profil</div>
    <p class="prf-sec-desc">Perbarui informasi profil dan alamat email akun Anda.</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="prf-field">
            <label class="prf-label" for="name">Nama<span class="req">*</span></label>
            <input id="name" name="name" type="text" class="prf-input {{ $errors->has('name') ? 'is-error' : '' }}" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Nama lengkap" />
            @error('name')<div class="prf-error">{{ $message }}</div>@enderror
        </div>

        <div class="prf-field">
            <label class="prf-label" for="email">Email<span class="req">*</span></label>
            <input id="email" name="email" type="email" class="prf-input {{ $errors->has('email') ? 'is-error' : '' }}" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="nama@email.com" />
            @error('email')<div class="prf-error">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="prf-hint">
                    Alamat email Anda belum terverifikasi.
                    <button form="send-verification" style="background:none;border:none;padding:0;color:var(--coral);font-weight:600;cursor:pointer;text-decoration:underline">Klik di sini untuk mengirim ulang email verifikasi.</button>
                    @if (session('status') === 'verification-link-sent')
                        <span style="display:block;margin-top:6px;color:var(--green);font-weight:600">Link verifikasi baru telah dikirim ke email Anda.</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="prf-actions">
            <button type="submit" class="prf-btn coral"><x-hi icon="fa-floppy-disk" /> Simpan</button>
            @if (session('status') === 'profile-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="prf-saved"><x-hi icon="fa-check" style="margin-right:4px" /> Tersimpan.</span>
            @endif
        </div>
    </form>
</section>
