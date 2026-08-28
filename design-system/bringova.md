# Bringova Design System — Sekolahin (SPMB)

> **Sumber referensi:** Dribbble "Live Order Admin Dashboard Design — Bringova" (https://dribbble.com/shots/15700772).  
> **Scope:** Default visual untuk SEMUA halaman frontend project ini (admin + siswa + auth).  
> Opencode WAJIB mengacu ke file ini whenever mengerjakan makeover, komponen baru, atau perubahan posisi elemen.

---

## 1. Prinsip Inti

| Prinsip | Aturan |
|---------|--------|
| **Tanpa kartu putih** | Jangan pakai `bg-white shadow rounded-lg` sebagai wadah utama. Konten menyatu dengan background, dipisah **garis divider** (`border-top: 1px solid var(--divider)`). |
| **Background polos** | `#f6f7fb` (admin) / `#F6F7FB` / `#F4F5FB` (auth). Blob gradient hanya untuk auth/guest (pink/peach/coral). Di dalam `.dash`/`.reg`/dll jangan pakai blob — cukup `#f6f7fb`. |
| **Scoped per halaman** | Setiap partial punya wrapper class unik (`.dash`, `.reg`, `.det`, `.acc`, `.rre`, `.pay`, `.rkp`, `.trk`, `.sch`, `.mjr`, `.prd`, `.ste`, `.prf`, `.mjd`, `.alg`, …). Semua CSS diawali `.scope`. |
| **Modal & picker DI DALAM scope** | Closing `</div>` wrapper HARUS di paling akhir **setelah** modal & `#reg-data`. Jika di luar scope → CSS `scope .picker-*` tidak match → modal jadi inline stack tanpa styling. |
| **Hapus native controls** | Native `<select>` / `confirm()` / `alert()` dilarang. Ganti dengan **modal picker Bringova** (search + list) dan **modal konfirmasi Bringova** (backdrop blur + rounded 18). |
| **Default frontend** | Untuk task frontend apapun yang belum ada arahan desain, pakai Bringova tanpa tanya ulang. |

---

## 2. Palet

```css
--coral: #FF6B6B;       --coral-soft: #FFE5E3;  --coral-2: #FF8E6E;
--amber: #F59E0B;       --amber-soft: #FEF3C7;
--green: #10B981;       --green-soft: #D1FAE5;
--blue: #3B82F6;        --blue-soft: #DBEAFE;
--purple: #8B5CF6;      --purple-soft: #EDE9FE;
--red: #EF4444;         --red-soft: #FEE2E2;
--gray: #6b7280;        --gray-soft: #F3F4F6;
--ink: #1a1a2e;         --muted: #8a8f9d;
--divider: rgba(26,26,46,.10);
bg: #f6f7fb;  radius: 24px;  max-width konten centered: 1080px (jika perlu .s-inner)
```

Auth accent: gradient `135deg #FF6B6B → #FF8E6E` (tombol, tab indicator, avatar).

---

## 3. Typography & Shell

- Font: **Inter** (Bunny Fonts / Google), fallback `font-sans`.
- Title halaman: `26px / 800 / var(--ink) / letter-spacing -0.01em`.
- Meta/desc: `13px / var(--muted)`.
- Breadcrumb: `12.5px / 500 / var(--muted)` — link `var(--coral)`, separator `#d3d6de`.
- Admin shell: sidebar `264px` fixed expanded, `.main { margin-left: calc(264px + 32px) }`, `.panel-right { padding: 24px 32px }`, `#content-area` full width (partial AJAX inject).

---

## 4. Wrapper Halaman

```css
.scope {
  position: relative;
  border-radius: 24px;
  padding: 28px 28px 44px; /* 40–44px bottom */
  background: #f6f7fb;
}
.scope .s-inner { width: 100%; max-width: 1080px; margin: 0 auto; } /* bila perlu centered */
```

Setiap section di dalam scope:

```css
.scope .s-sec { border-top: 1px solid var(--divider); padding: 24px 0 6px; }
.scope .s-sec:first-of-type { border-top: none; padding-top: 4px; }
.scope .s-sec-head { display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
```

Grid info: `display:grid; grid-template-columns: repeat(2/3, 1fr); gap: 12–16px` — responsive `≤720px → 1 col`.

---

## 5. Header, Tabs, Alert

**Header:**
```
crumb (Dashboard / Halaman) → title 26/800 → meta → alert → head row (title + coral button)
```

**Tabs underline (bukan pill):**
```css
.scope .x-tabs { display:flex; gap:22px; border-bottom:1px solid var(--divider); margin-bottom:24px; }
.scope .x-tab { padding:9px 2px 11px; font:600 13–13.5px; color:var(--muted);
  border-bottom:2.5px solid transparent; margin-bottom:-1px; }
.scope .x-tab.active { color:var(--coral); border-bottom-color:var(--coral); }
.scope .x-tab .badge { background:var(--coral-soft); color:var(--coral); border-radius:20px; padding:1px 8px; font:700 10.5px; }
.scope .x-tab.active .badge { background:var(--coral); color:#fff; }
/* Jika tabs butuh AJAX (.doc-tab), reset: .scope .x-tabs a.doc-tab { all:unset; display:inline-flex; ... } */
```

**Alert flash:**
```css
.scope .x-alert { display:flex; gap:10px; padding:12px 16px; border-radius:12px; font:500 13px; margin-bottom:16px; }
.scope .x-alert.success { background:var(--green-soft); color:var(--green); }
.scope .x-alert.error   { background:var(--red-soft);   color:var(--red); }
.scope .x-alert.info    { background:var(--blue-soft);  color:var(--blue); }
```

---

## 6. List / Rows (pengganti tabel)

```css
.scope .x-list { display:flex; flex-direction:column; }
.scope .x-row  { display:flex; align-items:center; gap:15px;
  padding:13–15px 4px; border-bottom:1px solid var(--divider); }
.scope .x-row:last-child { border-bottom:none; }
.scope .x-ic   { flex:0 0 auto; width:46px; height:46px; border-radius:14px;
  display:flex; align-items:center; justify-content:center; font-size:17px;
  background:var(--gray-soft); color:var(--gray); }
.scope .x-body { flex:1; min-width:0; }
.scope .x-name { font:700 14px var(--ink); display:flex; gap:8px; flex-wrap:wrap; }
.scope .x-sub  { font:12px var(--muted); margin-top:2px; }
.scope .x-tags { display:flex; gap:7px; flex-wrap:wrap; margin-top:6px; }
.scope .x-actions { display:flex; align-items:center; gap:8px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }
.scope .x-cap { font:500 11px var(--muted); } /* caption muted • TKJ • #1 */
.scope .x-stats { font:12px var(--muted); } .x-stats b { font-weight:700; }
```

Warna badge ikon per status (mis. `.t-coral .d-ic`, `.t-green`, `.t-red`, `.warning .d-row-ic`).

---

## 7. Pills & Minimalisasi

**Pill default:**
```css
.scope .x-pill { display:inline-flex; align-items:center; gap:6px;
  padding:4px 11px; border-radius:20px; font:700 11–11.5px; white-space:nowrap; }
.scope .x-pill.green { background:var(--green-soft); color:var(--green); }
.scope .x-pill.amber { background:var(--amber-soft); color:#b45309; }
.scope .x-pill.blue  { background:var(--blue-soft);  color:var(--blue); }
.scope .x-pill.coral { background:var(--coral-soft); color:var(--coral); }
.scope .x-pill.red   { background:var(--red-soft);   color:var(--red); }
.scope .x-pill.gray  { background:var(--gray-soft);  color:var(--gray); }
```

**Minimalisasi:** sisakan **hanya pill status** (Aktif/Nonaktif, Diterima/Pending, dll). Code/jenjang/order/quota → teks muted inline (`· TKJ`, `SMA ·`, `#1`, `Reguler 20 sisa 15`), stats → `Pendaftar 12 · Pending 3 · Diterima 7` (angka berwarna, tanpa pill background). Jika perlu ikon + angka badge (dashboard `d-stat` → `d-ic` 48px + label/value).

---

## 8. Buttons

```css
.scope .x-btn { display:inline-flex; align-items:center; gap:7px; border:none; cursor:pointer;
  border-radius:11px; padding:10px 17px; font:700 13px; text-decoration:none;
  transition: transform .15s, filter .15s; }
.scope .x-btn:hover { transform: translateY(-1px); }
.scope .x-btn.coral { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color:#fff;
  box-shadow: 0 8px 18px -8px rgba(255,107,107,.6); }
.scope .x-btn.ghost { background: rgba(255,255,255,.6); color:var(--ink); }
.scope .x-btn.ghost:hover { background:#fff; color:var(--coral); }
.scope .x-btn.green { background:var(--green); color:#fff; }
.scope .x-btn.red   { background:var(--red-soft); color:var(--red); } /* atau var(--red) solid */
.scope .x-btn.amber { background:var(--amber-soft); color:#b45309; }
.scope .x-btn.sm { padding:6–9px 11–15px; font-size:11.5–12.5px; border-radius:9px; }
```

Auth submit: `h-12 w-full rounded-xl gradient coral, text-white, shadow coral, fa-arrow-right hover translateX(4px)`.

---

## 9. Inputs

**Dua varian — pilih sesuai kebutuhan (kombinasi minimal = default):**

```css
/* Border-bawah transparan (untuk text/number/email) */
.scope .x-input-line {
  width:100%; background: transparent; border:none; border-bottom:1px solid rgba(26,26,46,.18);
  border-radius:0; padding:9px 4px; font:13px var(--ink);
}
.scope .x-input-line:focus { outline:none; border-bottom-color: var(--coral); }

/* Kotak (untuk textarea, date, search, file) */
.scope .x-input-box {
  width:100%; background: rgba(255,255,255,.35); border:1px solid rgba(26,26,46,.14);
  border-radius:11–12px; padding:11px 13px; font:13px var(--ink);
  backdrop-filter: blur(8px);
}
.scope .x-input-box:focus { outline:none; border-color:var(--coral);
  box-shadow: 0 0 0 4px rgba(255,107,107,.14); background: rgba(255,255,255,.55); }

/* Search box */
.scope .x-search { display:flex; align-items:center; gap:8px;
  background: rgba(255,255,255,.6); border:1px solid rgba(26,26,46,.08);
  border-radius:12px; padding:9px 12px; flex:1; }
.scope .x-search input { border:none; background:transparent; outline:none; flex:1; font:13px; }
```

Focus ring: `box-shadow 0 0 0 4px rgba(255,107,107,.14)` untuk box. Error: `border-color #EF4444 / ring red`.

`x-date-picker` (custom date) override jadi kotak `rounded 11px` dengan prinsip sama.

---

## 10. Picker Modal (pengganti native select)

Trigger:
```blade
<button type="button" class="r-pick" data-picker="key" aria-haspopup="listbox">
  <span class="pick-label">Semua Status</span>
  <span class="pick-clear" data-clear="key" role="button" tabindex="0"><i class="fa-solid fa-xmark"></i></span>
</button>
<input type="hidden" name="status" data-picker-input="key" value="{{ request('status') }}">
```
```css
.scope .r-pick { display:inline-flex; align-items:center; flex-wrap:nowrap; max-width:100%;
  background: transparent; border:none; border-bottom:1px solid rgba(26,26,46,.18);
  border-radius:0; padding:9px 4px; font:500 13px; color:var(--ink); cursor:pointer; }
.scope .r-pick:hover, .scope .r-pick:focus { border-bottom-color: var(--coral); outline:none; }
.scope .r-pick .pick-label { flex:1 1 auto; min-width:0; }
.scope .r-pick .pick-label.is-placeholder { color: var(--muted); }
.scope .r-pick .pick-clear { flex:0 0 auto; } /* span, bukan button (no nested button) */
.scope .r-pick .pick-caret { display:none; } /* caret dihapus, pakai border-bawah saja */
```

Modal (scoped, DI DALAM wrapper):
```blade
<div id="pickerBackdrop" class="picker-backdrop" aria-hidden="true">
  <div class="picker-panel" role="dialog">
    <div class="picker-head"><div class="picker-title" id="pickerTitle"></div>
      <button class="picker-close" onclick="closePicker()"><i class="fa-solid fa-xmark"></i></button></div>
    <div class="picker-search"><i class="fa-solid fa-magnifying-glass"></i>
      <input id="pickerSearch" type="search" placeholder="Cari…" autocomplete="off"></div>
    <div class="picker-list" id="pickerList" role="listbox"></div>
    <div class="picker-foot"><button class="picker-clear-all" onclick="clearCurrentPicker()">Bersihkan</button>
      <button class="picker-done" onclick="closePicker()">Selesai</button></div>
  </div>
</div>
<div id="reg-data" hidden data-picker='@json($pickerJson)' data-picker-labels='@json($pickerLabels)'></div>
```
```css
.scope .picker-backdrop { position:fixed; inset:0; z-index:90; background:rgba(26,26,46,.36);
  backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
.scope .picker-backdrop.is-open { display:flex; }
.scope .picker-panel { width:100%; max-width:380px; background:#fff; border-radius:18px; padding:18px;
  box-shadow: 0 24px 60px -18px rgba(26,26,46,.4); animation: pickerPop .2s cubic-bezier(.22,1.2,.36,1); }
.scope .picker-item { display:flex; align-items:center; justify-content:space-between;
  padding:10px 12px; border-radius:10px; cursor:pointer; font:500 13px; }
.scope .picker-item.is-selected { background:var(--coral); color:#fff; }
.scope .picker-item .pi-check { opacity:0; } .scope .picker-item.is-selected .pi-check { opacity:1; }
.scope .picker-foot .picker-done { background: linear-gradient(135deg, var(--coral), var(--coral-2)); color:#fff; border-radius:10px; }
```

JS global ada di `layouts/dashboard.blade.php` IIFE — expose `window.openPicker/closePicker/clearPicker/clearCurrentPicker/pickerInitAll`. Membaca `#reg-data`, bind `.r-pick[data-picker]`, pakai IDs `pickerBackdrop/pickerList/pickerSearch/pickerTitle`, tulis ke `[data-picker-input=key]` + dispatch `change` → auto `filterTable('majors')` untuk `mjrLevel/mjrSchool`. Init auto-run `DOMContentLoaded` + hook `loadContent` AJAX via `pickerInitAll()`.

---

## 11. Modal Konfirmasi

```css
.scope .x-modal-backdrop { position:fixed; inset:0; z-index:90; background:rgba(26,26,46,.36);
  backdrop-filter:blur(3px); display:none; align-items:center; justify-content:center; padding:16px; }
.scope .x-modal-backdrop.is-open { display:flex; }
.scope .x-modal { width:100%; max-width:400px; background:#fff; border-radius:18px; padding:22px;
  box-shadow: 0 24px 60px -18px rgba(26,26,46,.4); animation: xModalPop .2s cubic-bezier(.22,1.2,.36,1); }
.scope .x-modal-body { display:flex; gap:13px; margin-bottom:18px; }
.scope .x-modal-ic { flex:0 0 auto; width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; }
.scope .x-modal-ic.red   { background:var(--red-soft);  color:var(--red); }
.scope .x-modal-ic.amber { background:var(--amber-soft); color:#b45309; }
.scope .x-modal-ic.green { background:var(--green-soft); color:var(--green); }
```

JS: `openXModal(id,name)` set `#xMsg` + `form.action` + `backdrop display:flex + is-open`; `closeXModal()` hide + `pending=null`; backdrop `e.target===this` close; `Escape` close; `#xAction` click → `pendingForm.submit()` atau `fetch PATCH /admin/tracks/{id}/level/{id}`.

> Untuk toggle nonaktifkan jalur: `dashboard.blade.php` extract `window.doTrackToggle` + `window.confirmTrackDeactivate` (partial `#trackConfirmModal`).

---

## 12. Toolbar & Pagination

```blade
<div class="x-toolbar">
  <div class="x-search"><i class="fa-solid fa-magnifying-glass"></i><input name="search" value="{{ request('search') }}" placeholder="Cari…"></div>
  <button type="button" class="x-fbtn ghost sm" onclick="toggleFilterPanel()">Filter</button>
  <button type="submit" class="x-gobtn coral">Cari</button>
</div>
<div id="filterPanel" class="x-filters" style="display: {{ request('level') ? 'flex' : 'none' }}">
  <div class="x-field"><span class="x-field-label">Jenjang</span>
    <button class="r-pick" data-picker="level">…</button><input type="hidden" name="level" data-picker-input="level"></div>
</div>
```

Pagination: `{{ $items->appends(request()->query())->links('vendor.pagination.bringova') }}` — view `vendor/pagination/bringova.blade.php` (pill putih 36px, active coral gradient, chevron `fa-chevron-left/right` 11px, hover coral, ellipsis muted).

---

## 13. Aturan AJAX & Controller

- Partial dipakai **full render + AJAX** (`loadContent` → `contentArea.innerHTML = data.html` → script tag tidak auto-run). Fungsi JS yang dipakai partial **harus di layout** (`toggleFilterPanel`, `showReRegRejectModal`, `showRejectModal`, `openMajorDelete`, `doTrackToggle`, `pickerInitAll` hook).
- Tabs/pagination delegasi di layout: `.doc-tab`, `nav[aria-label=Pagination] a`, `#filterForm` submit → `loadContent(href)`.
- Controller: variabel picker (`$majors`, `$levels`, `$schools`, `$paymentTypes`, …) **pindahkan SEBELUM branch AJAX** agar tersedia di kedua render. Search dibungkus `where(function($q){ ... })` + `trim + mb_substr 100` + `group by` agar tidak meniadakan filter lain.
- Bypass AJAX untuk halaman yang butuh reload penuh (mis. `/admin/settings`): di `dashboard.blade.php` sidebar click handler + `popstate` → jika `href contains /admin/settings` → `return` tanpa preventDefault / `window.location.href`.

---

## 14. Auth (Guest) — Blob Variant

`layouts/guest.blade.php`: body `#F4F5FB`, `flex min-h-screen items-center justify-center`, 4 radial blob `blur 50–60px` (coral/peach/blue), konten `max-w-md`, branding logo `h-12 w-12 rounded-2xl gradient coral` + `Sekolahin / Penerimaan Murid Baru`, card outline `rounded-3xl border border-gray-200/70 bg-white/10 backdrop-blur-sm p-8`. Override `.auth-shell` untuk halaman auth lain (input `rounded-xl border-gray-200 focus coral ring`, button `gradient coral`).

`auth/access.blade.php`: `#auth-switcher` grid stack + `.auth-tab` + `#tab-indicator` gradient coral, `inputBase` `rounded-xl border shadow-sm focus coral ring`, `btn-submit` coral gradient, togglePassword + updateStrength (4 skor warna).

---

## 15. Checklist Sebelum Commit

- [ ] Wrapper scoped + closing `</div>` paling akhir (modal & `#reg-data` di dalam)
- [ ] Tanpa `bg-white shadow` kartu; pakai divider
- [ ] Tabs `all:unset` jika pakai `.doc-tab`
- [ ] Semua `<select>` → `r-pick` (span clear, no nested button, caret hidden, border-bawah)
- [ ] Semua `confirm()` → modal Bringova
- [ ] Pagination `vendor.pagination.bringova`
- [ ] Controller: picker vars sebelum AJAX branch, search grouped
- [ ] `view:cache` + `npm run build` + render tinker (scoped class + modal chain + native count 0)
- [ ] `max-width 1080px + margin auto` jika konten terlalu ke kiri

---

*File ini adalah sumber kebenaran desain frontend project. Jika ada konflik dengan style lama (`eggplore` / Tailwind native), Bringova menang. Update file ini saat pola baru ditambahkan.*
