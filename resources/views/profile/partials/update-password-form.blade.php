<section>
    <div class="prf-sec-label">Ubah Kata Sandi</div>
    <p class="prf-sec-desc">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="prf-field">
            <label class="prf-label" for="update_password_current_password">Kata Sandi Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" class="prf-input {{ $errors->updatePassword->has('current_password') ? 'is-error' : '' }}" autocomplete="current-password" placeholder="••••••••" />
            @if($errors->updatePassword->has('current_password'))<div class="prf-error">{{ $errors->updatePassword->first('current_password') }}</div>@endif
        </div>

        <div class="prf-field">
            <label class="prf-label" for="update_password_password">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" class="prf-input {{ $errors->updatePassword->has('password') ? 'is-error' : '' }}" autocomplete="new-password" placeholder="••••••••" />
            @if($errors->updatePassword->has('password'))<div class="prf-error">{{ $errors->updatePassword->first('password') }}</div>@endif
        </div>

        <div class="prf-field">
            <label class="prf-label" for="update_password_password_confirmation">Konfirmasi Kata Sandi</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="prf-input {{ $errors->updatePassword->has('password_confirmation') ? 'is-error' : '' }}" autocomplete="new-password" placeholder="••••••••" />
            @if($errors->updatePassword->has('password_confirmation'))<div class="prf-error">{{ $errors->updatePassword->first('password_confirmation') }}</div>@endif
        </div>

        <div class="prf-actions">
            <button type="submit" class="prf-btn coral"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
            @if (session('status') === 'password-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="prf-saved"><i class="fa-solid fa-check" style="margin-right:4px"></i> Tersimpan.</span>
            @endif
        </div>
    </form>
</section>
