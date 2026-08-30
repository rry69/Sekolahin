<section>
    @php $pl = !($_pv['licensed'] ?? true) && ($isAdminUser ?? ($user->role?->name ?? '') === 'Admin'); @endphp
    <div class="prf-sec-label">Ubah Kata Sandi @if($pl) <span class="pl-pro-badge"><x-hi name="lock" /> Fitur PRO</span> @endif</div>
    <p class="prf-sec-desc">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>

    @if($pl)
    <div class="pl-lock-box">
      <div class="pl-lock-fields">
    @endif
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
            <button type="submit" class="prf-btn coral"><x-hi icon="fa-floppy-disk" /> Simpan</button>
            @if (session('status') === 'password-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="prf-saved"><x-hi icon="fa-check" style="margin-right:4px" /> Tersimpan.</span>
            @endif
        </div>
    </form>
    @if($pl)
      </div>
      <div class="pl-lock-shade" role="button" tabindex="0" aria-label="Buka info fitur PRO" data-pro-msg="Mengubah kata sandi Admin adalah fitur PRO. <b>Aktifkan lisensi</b> untuk mengubahnya.">
        <span class="pl-lock-chip"><x-hi name="lock" /> Fitur <b>PRO</b> Terkunci — klik untuk info</span>
      </div>
    </div>
    @endif
</section>
