<section>
    <div class="prf-danger">
        <div class="prf-danger-title"><i class="fa-solid fa-triangle-exclamation"></i> Zona Bahaya</div>
        <div class="prf-sec-label" style="font-size:14px;margin-bottom:2px">Hapus Akun</div>
        <p class="prf-danger-desc">Setelah akun dihapus, semua data dan sumber daya akan dihapus permanen. Harap unduh data yang ingin Anda simpan sebelum melanjutkan.</p>
        <button type="button" class="prf-btn red" onclick="openPrfDelete()"><i class="fa-solid fa-trash-can"></i> Hapus Akun</button>
    </div>

    <div id="prfDeleteModal" class="prf-modal-backdrop" aria-hidden="true">
        <div class="prf-modal" role="dialog" aria-modal="true">
            <div class="prf-modal-body">
                <div class="prf-modal-ic"><i class="fa-solid fa-trash-can"></i></div>
                <div style="flex:1;min-width:0">
                    <div class="prf-modal-title">Hapus akun Anda?</div>
                    <p class="prf-modal-msg">Akun dan semua data akan dihapus permanen. Masukkan kata sandi untuk konfirmasi.</p>
                </div>
            </div>
            <form id="prfDeleteForm" method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="prf-field">
                    <label class="prf-label" for="prf_password">Kata Sandi</label>
                    <input id="prf_password" name="password" type="password" class="prf-input" placeholder="••••••••" autocomplete="current-password" />
                    @if($errors->userDeletion->has('password'))<div class="prf-error">{{ $errors->userDeletion->first('password') }}</div>@endif
                </div>
                <div class="prf-modal-actions">
                    <button type="button" class="prf-btn ghost" onclick="closePrfDelete()">Batal</button>
                    <button type="submit" class="prf-btn red">Hapus Akun</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function(){
      window.openPrfDelete = function(){
        var m=document.getElementById('prfDeleteModal');
        if(!m) return;
        m.classList.add('is-open'); m.setAttribute('aria-hidden','false');
        var inp=document.getElementById('prf_password'); if(inp) setTimeout(function(){ inp.focus(); },80);
      };
      window.closePrfDelete = function(){
        var m=document.getElementById('prfDeleteModal');
        if(!m) return;
        m.classList.remove('is-open'); m.setAttribute('aria-hidden','true');
      };
      var bd=document.getElementById('prfDeleteModal');
      if(bd) bd.addEventListener('click', function(e){ if(e.target===this) closePrfDelete(); });
      document.addEventListener('keydown', function(e){
        if(e.key==='Escape'){ var m=document.getElementById('prfDeleteModal'); if(m && m.classList.contains('is-open')) closePrfDelete(); }
      });
      @if($errors->userDeletion->isNotEmpty())
        document.addEventListener('DOMContentLoaded', function(){ openPrfDelete(); });
      @endif
    })();
    </script>
</section>
