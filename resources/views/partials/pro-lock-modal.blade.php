{{-- Modal & CSS Akses Terbatas (reusable, include sekali per halaman di dalam scope wrapper) --}}
<style>
  .pl-pro-badge { display:inline-flex; align-items:center; gap:5px; margin-left:auto; padding:4px 11px; border-radius:20px; font:700 11px; background:#FEF3C7; color:#B45309; white-space:nowrap; }
  .pl-lock-box { position:relative; border-radius:14px; }
  .pl-lock-fields { filter:grayscale(.8); opacity:.55; pointer-events:none; user-select:none; }
  .pl-lock-shade { position:absolute; inset:0; border-radius:14px; background:rgba(246,247,251,.40); display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:2; }
  .pl-lock-chip { display:inline-flex; align-items:center; gap:8px; padding:9px 16px; border-radius:12px; background:#fff; box-shadow:0 12px 28px -14px rgba(26,26,46,.35); font:700 13px #1a1a2e; border:1px solid rgba(26,26,46,.06); }
  .pl-lock-chip .hi { color:#F59E0B; }
  .pl-lock-chip b { color:#FF6B6B; font-weight:800; }
  .pl-modal-backdrop { position:fixed; inset:0; z-index:95; background:rgba(26,26,46,.36); backdrop-filter:blur(3px); -webkit-backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
  .pl-modal-backdrop.is-open { display:flex; }
  .pl-modal { width:100%; max-width:400px; background:#fff; border-radius:18px; padding:22px; box-shadow:0 24px 60px -18px rgba(26,26,46,.4); animation:plPop .2s cubic-bezier(.22,1.2,.36,1); }
  @keyframes plPop { from { opacity:0; transform:scale(.97) translateY(4px); } to { opacity:1; transform:scale(1) translateY(0); } }
  .pl-modal-body { display:flex; gap:13px; margin-bottom:18px; }
  .pl-modal-ic { flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; background:#FEF3C7; color:#B45309; }
  .pl-modal-body h3 { margin:0 0 6px; font:800 15px #1a1a2e; }
  .pl-modal-body p { margin:0; font:13px #8a8f9d; line-height:1.5; }
  .pl-modal-msg b { color:#FF6B6B; font-weight:800; }
  .pl-modal-foot { display:flex; justify-content:flex-end; gap:8px; }
  .pl-btn { display:inline-flex; align-items:center; gap:7px; border:none; cursor:pointer; border-radius:10px; padding:9px 15px; font:700 12.5px; text-decoration:none; transition:transform .15s, filter .15s; }
  .pl-btn:hover { transform:translateY(-1px); }
  .pl-btn.ghost { background:#F3F4F6; color:#1a1a2e; }
  .pl-btn.ghost:hover { background:#fff; color:#FF6B6B; }
  .pl-btn.amber { background:#FEF3C7; color:#B45309; }
</style>

<div id="plProModal" class="pl-modal-backdrop" aria-hidden="true">
  <div class="pl-modal" role="dialog" aria-modal="true">
    <div class="pl-modal-body">
      <div class="pl-modal-ic"><x-hi name="lock"></x-hi></div>
      <div>
        <h3>Akses Terbatas</h3>
        <p id="plProMsg" class="pl-modal-msg"></p>
      </div>
    </div>
    <div class="pl-modal-foot">
      <button type="button" class="pl-btn ghost" onclick="closeProLockModal()">Tutup</button>
      <a href="https://shop.hrry.win" target="_blank" class="pl-btn amber"><x-hi name="comment-01"></x-hi> Kunjungi Toko</a>
    </div>
  </div>
</div>