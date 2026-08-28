<section>
    <div class="prfa-danger">
        <div class="prfa-danger-head">
            <span class="prfa-danger-ic"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <div>
                <div class="prfa-danger-title">Zona Bahaya · Hapus Akun</div>
                <p class="prfa-danger-desc">Setelah akun dihapus, semua data dan sumber daya akan dihapus permanen dan tidak dapat dikembalikan. Harap unduh atau simpan data yang ingin Anda pertahankan sebelum melanjutkan.</p>
            </div>
        </div>

        <div class="prfa-danger-warn">
            <i class="fa-solid fa-circle-info" style="margin-top:2px"></i>
            <span>Tindakan ini <b>tidak dapat dibatalkan</b>. Anda akan kehilangan akses ke dashboard admin dan seluruh data terkait akun ini.</span>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}" class="prfa-danger-form">
            @csrf
            @method('delete')

            <div class="prfa-danger-confirm">
                <label class="prf-label" for="prfa-delete-confirm">Ketik <b>HAPUS</b> untuk konfirmasi<span class="req">*</span></label>
                <input id="prfa-delete-confirm" type="text" class="prf-input" placeholder="HAPUS" autocomplete="off" autocapitalize="characters" />
                <div class="prf-hint">Mengetik kata "HAPUS" adalah langkah keamanan ekstra agar akun tidak terhapus secara tidak sengaja.</div>
            </div>

            <div class="prf-field">
                <label class="prf-label" for="prfa-delete-password">Kata Sandi Saat Ini<span class="req">*</span></label>
                <input id="prfa-delete-password" name="password" type="password" class="prf-input {{ $errors->userDeletion->has('password') ? 'is-error' : '' }}" placeholder="••••••••" autocomplete="current-password" />
                @if($errors->userDeletion->has('password'))<div class="prf-error">{{ $errors->userDeletion->first('password') }}</div>@endif
            </div>

            <div class="prf-actions">
                <button type="submit" id="prfa-delete-btn" class="prf-btn red" disabled><i class="fa-solid fa-trash-can"></i> Hapus Akun Permanen</button>
            </div>
        </form>
    </div>
</section>
