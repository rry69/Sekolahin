<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'SPMB') }} - @yield('title', 'Dashboard')</title>
<!-- ponytail: satu CDN icon set, lebih dari itu overkill -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- ponytail: CDN Tailwind untuk admin views; ganti autoprefixer/build saat production -->
<script src="https://cdn.tailwindcss.com"></script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; display: flex; height: 100vh; background: #f5f6fa; color: #333; font-size: 14px; }

  /* === SIDEBAR === */
  .sidebar { width: 240px; background: #fff; border-right: 1px solid #e8e8e8; display: flex; flex-direction: column; flex-shrink: 0; overflow-y: auto; }
  .sidebar-header { padding: 20px 16px 16px; text-align: center; border-bottom: 1px solid #f0f0f0; }
  .sidebar-header .avatar { width: 56px; height: 56px; border-radius: 50%; background: #ddd; margin: 0 auto 8px; overflow: hidden; }
  .sidebar-header .avatar img { width: 100%; height: 100%; object-fit: cover; }
  .sidebar-header h2 { font-size: 15px; font-weight: 600; margin-bottom: 1px; }
  .sidebar-header .subtitle { font-size: 11px; color: #999; }
  .sidebar-nav { flex: 1; padding: 10px 0; }
  .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 16px; cursor: pointer; color: #555; font-size: 13px; transition: background .15s; text-decoration: none; }
  .nav-item:hover { background: #f5f6fa; }
  .nav-item.active { color: #1a1a2e; font-weight: 600; background: #f0f0ff; }
  .nav-item i { width: 18px; text-align: center; font-size: 13px; }

  /* === NAV GROUPS === */
  .nav-group { }
  .nav-group-header { display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 16px; background: none; border: none; cursor: pointer; color: #888; font-size: 11px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; transition: color .15s; font-family: inherit; text-align: left; }
  .nav-group-header:hover { color: #1a1a2e; }
  .nav-group-header > i:first-child { width: 18px; text-align: center; font-size: 13px; }
  .nav-group-chevron { margin-left: auto; font-size: 11px; transition: transform .2s; }
  .nav-group.collapsed .nav-group-chevron { transform: rotate(-90deg); }
  .nav-group.collapsed .nav-group-body { display: none; }
  .nav-group-body { padding-bottom: 4px; }
  .nav-item-child { padding-left: 36px; }
  .nav-item-child i { width: 18px; text-align: center; font-size: 13px; }
  .sidebar-bottom { padding: 10px 16px; border-top: 1px solid #e8e8e8; }
  .sidebar-bottom .nav-item { padding: 9px 0; }

  /* === MAIN LAYOUT === */
  .main { display: flex; flex: 1; overflow: hidden; }

  /* === RIGHT PANEL === */
  .panel-right { flex: 1; overflow-y: auto; padding: 24px 32px; }

  .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #888; margin-bottom: 6px; }
  .breadcrumb a { color: #4f6ef7; text-decoration: none; }
  .breadcrumb .sep { color: #ccc; }
  .page-title { font-size: 26px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }

  .deal-meta { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
  .deal-meta .created { font-size: 13px; color: #666; }
  .deal-meta .created span { color: #1a1a2e; font-weight: 600; }
  .avatar-stack { display: flex; }
  .avatar-stack .av { width: 28px; height: 28px; border-radius: 50%; border: 2px solid #fff; margin-left: -8px; overflow: hidden; background: #ddd; }
  .avatar-stack .av:first-child { margin-left: 0; }
  .avatar-stack .av img { width: 100%; height: 100%; object-fit: cover; }
  .avatar-stack .av-more { background: #e0e7ff; color: #4f6ef7; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 600; }
  .add-member-btn { display: flex; align-items: center; gap: 4px; padding: 6px 12px; border: 1px dashed #ccc; border-radius: 6px; background: none; cursor: pointer; font-size: 12px; color: #666; }
  .add-member-btn:hover { border-color: #4f6ef7; color: #4f6ef7; }

  .tabs { display: flex; gap: 0; border-bottom: 1px solid #e0e0e0; margin-bottom: 20px; }
  .tab { padding: 10px 14px; font-size: 13px; color: #888; cursor: pointer; border-bottom: 2px solid transparent; font-weight: 500; white-space: nowrap; position: relative; text-decoration: none; display: inline-block; }
  .tab.active { color: #1a1a2e; border-bottom-color: #1a1a2e; font-weight: 600; }
  .tab .tab-badge { background: #1a1a2e; color: #fff; font-size: 10px; border-radius: 10px; padding: 1px 6px; margin-left: 4px; }

  .summary-cards { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
  .summary-card { flex: 1; min-width: 180px; background: #f8f9fb; border-radius: 10px; padding: 16px; }
  .summary-card .label { font-size: 12px; color: #999; display: flex; align-items: center; gap: 4px; margin-bottom: 6px; }
  .summary-card .label i { font-size: 11px; }
  .summary-card .value { font-size: 30px; font-weight: 700; color: #1a1a2e; }
  .summary-card .value small { font-size: 14px; font-weight: 400; color: #aaa; }
  .go-to-deals { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; color: #666; margin-bottom: 20px; cursor: pointer; text-decoration: none; }
  .go-to-deals:hover { color: #4f6ef7; }

  .doc-tabs { display: flex; align-items: center; gap: 0; margin-bottom: 16px; }
  .doc-tab { padding: 8px 16px; font-size: 13px; color: #888; cursor: pointer; border-radius: 6px; font-weight: 500; text-decoration: none; display: inline-block; }
  .doc-tab.active { background: #1a1a2e; color: #fff; }
  .doc-actions { margin-left: auto; display: flex; gap: 8px; }
  .doc-action-btn { padding: 6px 14px; border: 1px solid #e0e0e0; border-radius: 6px; background: #fff; cursor: pointer; font-size: 12px; color: #555; display: flex; align-items: center; gap: 5px; }
  .doc-action-btn:hover { border-color: #4f6ef7; color: #4f6ef7; }
  .doc-action-btn.primary { background: #f8f9fb; border-color: #d0d0d0; }

  .doc-list { display: flex; flex-direction: column; }
  .doc-row { display: flex; align-items: center; padding: 14px 0; border-bottom: 1px solid #f0f0f0; }
  .doc-row:last-child { border-bottom: none; }
  .doc-icon { width: 36px; height: 36px; border-radius: 8px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 14px; color: #888; }
  .doc-info { flex: 1; }
  .doc-name { font-size: 13px; font-weight: 500; color: #1a1a2e; }
  .doc-meta { font-size: 11px; color: #aaa; margin-top: 2px; }
  .doc-meta span { margin: 0 4px; }
  .doc-status { display: flex; align-items: center; gap: 6px; margin-left: 12px; }
  .doc-status-btn { width: 26px; height: 26px; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; }
  .doc-status-btn.accept { background: #22c55e; color: #fff; }
  .doc-status-btn.reject { background: #ef4444; color: #fff; }
  .doc-menu { width: 26px; height: 26px; border: none; background: none; cursor: pointer; color: #aaa; font-size: 16px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }
  .doc-menu:hover { background: #f3f4f6; color: #333; }

  /* === TABLE STYLES (ponytail: minimal table styling for data) === */
  .data-table { width: 100%; border-collapse: collapse; }
  .data-table th { text-align: left; padding: 10px 14px; font-size: 11px; font-weight: 600; color: #999; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #e8e8e8; }
  .data-table td { padding: 12px 14px; font-size: 13px; color: #333; border-bottom: 1px solid #f0f0f0; }
  .data-table tr:last-child td { border-bottom: none; }
  .data-table tr:hover td { background: #f8f9fb; }

  .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
  .status-pending { background: #fef3c7; color: #d97706; }
  .status-verified { background: #dbeafe; color: #2563eb; }
  .status-accepted { background: #dcfce7; color: #16a34a; }
  .status-rejected { background: #fee2e2; color: #dc2626; }

  .btn { padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
  .btn-primary { background: #4f6ef7; color: #fff; }
  .btn-primary:hover { background: #3b5de7; }
  .btn-danger { background: #ef4444; color: #fff; }
  .btn-danger:hover { background: #dc2626; }
  .btn-outline { background: #fff; border: 1px solid #e0e0e0; color: #555; }
  .btn-outline:hover { border-color: #4f6ef7; color: #4f6ef7; }

  .empty-state { text-align: center; padding: 40px 20px; color: #999; font-size: 13px; }

  .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
  .section-header h3 { font-size: 16px; font-weight: 600; color: #1a1a2e; }

  .deadline-notif { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; padding: 16px; margin-bottom: 20px; }
  .deadline-notif .section-header h3 i { color: #f59e0b; margin-right: 6px; }
  .deadline-list { display: flex; flex-direction: column; }
  .deadline-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
  .deadline-item:last-child { border-bottom: none; }
  .deadline-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 14px; }
  .deadline-item.warning .deadline-icon { background: #fef3c7; color: #d97706; }
  .deadline-item.danger .deadline-icon { background: #fee2e2; color: #dc2626; }
  .deadline-item.upcoming .deadline-icon { background: #dbeafe; color: #2563eb; }
  .deadline-info { flex: 1; }
  .deadline-name { font-size: 13px; font-weight: 500; color: #1a1a2e; }
  .deadline-num { font-size: 11px; color: #aaa; margin-left: 6px; font-weight: 400; }
  .deadline-meta { font-size: 11px; color: #999; margin-top: 2px; }
  .deadline-item.danger .deadline-meta { color: #dc2626; }
</style>
</head>
<body>

<!-- SIDEBAR -->
@include('layouts.partials.sidebar')

<div class="main">

  <!-- RIGHT PANEL -->
  <div class="panel-right" style="overflow-y:auto;">
    <div id="content-area">
    @yield('content')
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" style="display:none;position:fixed;top:24px;right:24px;background:#1a1a2e;color:#fff;padding:12px 20px;border-radius:8px;font-size:13px;z-index:999;box-shadow:0 4px 12px rgba(0,0,0,0.2);"></div>

<!-- Reset Pendaftaran Modal (global — di luar #content-area agar tidak hilang saat AJAX) -->
<div id="resetModal" style="display:none;position:fixed;inset:0;background:rgba(17,24,39,0.6);z-index:60;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:12px;box-shadow:0 20px 40px rgba(0,0,0,0.2);max-width:28rem;width:100%;padding:24px;">
        <div style="display:flex;gap:12px;margin-bottom:16px;">
            <div style="width:40px;height:40px;border-radius:9999px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">↺</div>
            <div style="flex:1;">
                <h3 style="font-size:15px;font-weight:600;color:#111827;">Reset pendaftaran?</h3>
                <p id="resetModalMessage" style="font-size:13px;color:#4b5563;margin-top:4px;"></p>
                <p style="font-size:11px;color:#6b7280;margin-top:8px;">Akun &amp; profil siswa tetap. Dokumen, bukti bayar &amp; invoice PDF ikut dihapus. NIS dihapus jika akun jadi tanpa pendaftaran.</p>
            </div>
        </div>
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#374151;margin-bottom:16px;cursor:pointer;">
            <input type="checkbox" id="resetAllCheckbox" style="border-radius:4px;border:1px solid #d1d5db;">
            <span>Reset <span style="font-weight:600;">semua pendaftaran</span> milik akun ini (opsional)</span>
        </label>
        <div style="display:flex;justify-content:flex-end;gap:8px;">
            <button type="button" onclick="closeResetModal()" style="padding:8px 16px;font-size:13px;color:#6b7280;background:none;border:none;cursor:pointer;">Batal</button>
            <button type="button" id="resetModalAction" style="padding:8px 16px;font-size:13px;font-weight:500;border-radius:6px;color:#fff;background:#d97706;border:none;cursor:pointer;">Ya, Reset</button>
        </div>
        <form id="resetForm" method="POST" style="display:none">
            @csrf
            <input type="hidden" name="scope" id="resetScopeInput" value="one">
        </form>
    </div>
</div>

@include('components.file-preview-modal')
@include('components.help-widget')

<script>
// === Modal Functions ===
function openStatusModal(id, status, notes) {
  document.getElementById('statusForm').action = '/admin/registrations/' + id + '/status';
  document.getElementById('statusSelect').value = status;
  document.getElementById('notesInput').value = notes || '';
  document.getElementById('statusModal').style.display = 'flex';
}
function closeStatusModal() {
  document.getElementById('statusModal').style.display = 'none';
}
function openPaymentModal(id, status, amount) {
  document.getElementById('paymentForm').action = '/admin/registrations/' + id + '/payment';
  document.getElementById('paymentStatusSelect').value = status;
  document.getElementById('paymentAmountInput').value = amount;
  document.getElementById('paymentModal').style.display = 'flex';
}
function closePaymentModal() {
  document.getElementById('paymentModal').style.display = 'none';
}
function showRejectModal(paymentId) {
  var modal = document.getElementById('rejectModal');
  var form = document.getElementById('rejectForm');
  if (form) form.action = '/admin/payments/' + paymentId + '/reject';
  if (modal) modal.style.display = 'flex';
}
function hideRejectModal() {
  var modal = document.getElementById('rejectModal');
  if (modal) modal.style.display = 'none';
}
function toggleFilterForm() {
  var f = document.getElementById('filterForm');
  if (f) f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

// === Toast ===
function showToast(msg) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.style.display = 'block';
  setTimeout(function() { t.style.display = 'none'; }, 2500);
}

// === AJAX Navigation ===
document.addEventListener('DOMContentLoaded', function() {
  var contentArea = document.getElementById('content-area');
  var isLoading = false;

  // Sidebar menu clicks: load in-place via AJAX, no page navigation
  document.querySelectorAll('.sidebar-nav [data-menu-item]').forEach(function(navLink) {
    navLink.addEventListener('click', function(e) {
      e.preventDefault();
      loadContent(navLink.getAttribute('href'));
    });
  });

  // Collapsible group toggles
  document.querySelectorAll('.nav-group-header[data-group-toggle]').forEach(function(header) {
    header.addEventListener('click', function() {
      header.closest('.nav-group').classList.toggle('collapsed');
    });
  });

  contentArea.addEventListener('click', function(e) {
    // Tab/doc-tab links
    var tab = e.target.closest('a.tab, a.doc-tab, a.go-to-deals');
    if (tab) {
      e.preventDefault();
      loadContent(tab.getAttribute('href'));
      return;
    }

    // Pagination
    var pag = e.target.closest('.pagination a');
    if (pag) {
      e.preventDefault();
      loadContent(pag.getAttribute('href'));
      return;
    }

    // Close modal on backdrop click
    if (e.target.id === 'statusModal' || e.target.id === 'paymentModal' || e.target.id === 'rejectModal') {
      e.target.style.display = 'none';
    }
  });

  // Filter form & track toggles
  contentArea.addEventListener('submit', function(e) {
    // Status/Payment update modals
    if (e.target.id === 'statusForm' || e.target.id === 'paymentForm') {
      e.preventDefault();
      var form = e.target;
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          form.closest('[style*="position:fixed"]').style.display = 'none';
          showToast(data.message);
          loadContent(window.location.href, false);
        }
      });
      return;
    }

    // Filter form
    if (e.target.id === 'filterForm') {
      e.preventDefault();
      var url = new URL(e.target.action, window.location.origin);
      var fd = new FormData(e.target);
      for (var pair of fd.entries()) {
        if (pair[1]) url.searchParams.set(pair[0], pair[1]);
        else url.searchParams.delete(pair[0]);
      }
      loadContent(url.pathname + url.search);
    }
  });

  // Track status toggles (delegated — konten di-inject via AJAX)
  contentArea.addEventListener('change', function(e) {
    var el = e.target;
    if (!el.classList || !el.classList.contains('track-toggle')) return;
    var trackId = el.getAttribute('data-track');
    var levelId = el.getAttribute('data-level');
    var trackName = el.getAttribute('data-track-name');
    var levelName = el.getAttribute('data-level-name');
    var isActive = el.checked;
    var row = el.closest('.track-row');

    el.disabled = true;

    fetch('/admin/tracks/' + trackId + '/level/' + levelId, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ is_active: isActive ? 1 : 0 })
    })
    .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, body: j }; }); })
    .then(function(res) {
      el.disabled = false;
      if (!res.ok || !res.body.success) {
        el.checked = !isActive;
        showToast(res.body.message || 'Gagal menyimpan perubahan');
        return;
      }
      el.checked = !!res.body.is_active;
      if (row) {
        var badge = row.querySelector('.track-badge');
        if (badge) {
          var on = !!res.body.is_active;
          badge.textContent = on ? 'Aktif' : 'Nonaktif';
          badge.className = 'status-badge track-badge ' + (on ? 'status-accepted' : 'status-rejected');
          if (!on) badge.setAttribute('style', 'background:#fee2e2;color:#dc2626;');
          else badge.removeAttribute('style');
        }
      }
      // Update toggle pill visuals
      var pill = el.nextElementSibling;
      if (pill) {
        pill.style.background = res.body.is_active ? '#4f6ef7' : '#d1d5db';
        var knob = pill.firstElementChild;
        if (knob) knob.style.left = res.body.is_active ? '22px' : '2px';
      }
      showToast(res.body.message || (trackName + ' untuk ' + levelName + ' diperbarui'));
    })
    .catch(function() {
      el.disabled = false;
      el.checked = !isActive;
      showToast('Gagal terhubung ke server');
    });
  });

  function loadContent(url, pushState) {
    if (pushState === undefined) pushState = true;
    if (isLoading) return;
    isLoading = true;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-cache' }, cache: 'no-store' })
      .then(function(r) {
        var ct = r.headers.get('content-type') || '';
        if (ct.indexOf('application/json') === -1) {
          // full-page route (admin.registrations/payments/majors...) -> force navigation
          window.location.href = url;
          throw new Error('full-page');
        }
        return r.json();
      })
      .then(function(data) {
        contentArea.innerHTML = data.html;
        isLoading = false;
        if (pushState) history.pushState({ url: url }, '', url);
        updateSidebar(url);
        window.scrollTo(0, 0);
        // Re-initialize custom date pickers in the newly injected content
        if (typeof window.datepickerInitAll === 'function') window.datepickerInitAll();
        // Auto-dismiss success alerts
        var alert = contentArea.querySelector('.ajax-success');
        if (alert) setTimeout(function() { alert.remove(); }, 3000);
      })
      .catch(function() {
        isLoading = false;
      });
  }

  function updateSidebar(url) {
    var navItems = document.querySelectorAll('.sidebar-nav .nav-item');
    navItems.forEach(function(item) { item.classList.remove('active'); });
    var m = url.match(/\/admin\/([a-z-]+)/);
    if (m) {
      var target = document.querySelector('.sidebar-nav .nav-item[href*="/admin/' + m[1] + '"]');
      if (target) {
        target.classList.add('active');
        var group = target.closest('.nav-group');
        if (group) group.classList.remove('collapsed');
      }
      else document.querySelector('.nav-item[href*="dashboard"]').classList.add('active');
    }
  }

  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.url) loadContent(e.state.url, false);
  });
  history.replaceState({ url: window.location.href }, '', window.location.href);

  // Escape key for modals
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      var rejectModal = document.getElementById('rejectModal');
      if (rejectModal && rejectModal.style.display === 'flex') {
        rejectModal.style.display = 'none';
      }
      var resetModal = document.getElementById('resetModal');
      if (resetModal && resetModal.style.display === 'flex') {
        closeResetModal();
      }
    }
  });

  // Reset modal (global — supaya bisa dipanggil dari partial yang di-inject via AJAX)
  var _resetPendingId = null;
  window.openResetModal = function (id, regNumber, name) {
    _resetPendingId = id;
    var msg = document.getElementById('resetModalMessage');
    if (msg) msg.textContent = 'Reset pendaftaran ' + regNumber + ' (' + name + ')? Data pendaftaran, dokumen, pembayaran & daftar ulang akan dihapus permanen.';
    var cb = document.getElementById('resetAllCheckbox');
    var sc = document.getElementById('resetScopeInput');
    if (cb) cb.checked = false;
    if (sc) sc.value = 'one';
    var m = document.getElementById('resetModal');
    if (m) { m.style.display = 'flex'; m.classList.remove('hidden'); }
  };
  window.closeResetModal = function () {
    var m = document.getElementById('resetModal');
    if (m) { m.style.display = 'none'; m.classList.add('hidden'); }
    _resetPendingId = null;
  };
  (function () {
    var rm = document.getElementById('resetModal');
    if (rm) rm.addEventListener('click', function (e) { if (e.target === this) closeResetModal(); });
    var cb2 = document.getElementById('resetAllCheckbox');
    if (cb2) cb2.addEventListener('change', function () {
      var sc2 = document.getElementById('resetScopeInput');
      if (sc2) sc2.value = this.checked ? 'all' : 'one';
    });
    var act = document.getElementById('resetModalAction');
    if (act) act.addEventListener('click', function () {
      if (!_resetPendingId) return;
      var form = document.getElementById('resetForm');
      form.action = '/admin/registrations/' + _resetPendingId + '/reset';
      form.submit();
    });
  })();
});
</script>

</body>
</html>
