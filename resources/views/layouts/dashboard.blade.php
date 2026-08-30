<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'SPMB') }} - @yield('title', 'Dashboard')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  :root {
    --page: #f5f6fa;
    --panel: #ffffff;
    --panel-2: #f8f9fb;
    --card-bg: #ffffff;
    --input-bg: #ffffff;
    --border: #e8e8e8;
    --hairline: #f0f0f0;
    --input-border: #e0e0e0;
    --hover: rgba(0, 0, 0, 0.04);
    --active: rgba(79, 110, 247, 0.10);
    --tx1: #1a1a2e;
    --tx2: #555555;
    --tx3: #888888;
    --tx4: #aaaaaa;
    --tx-body: #333333;
    --accent: #4f6ef7;
    --accent-fg: #ffffff;
    --accent-soft-bg: #e0e7ff;
    --accent-soft-fg: #4f6ef7;
    --danger: #dc2626;
    --on-danger: #ffffff;
    --success: #16a34a;
    --on-success: #ffffff;
    --warning: #d97706;
    --info: #2563eb;
    --badge-pending-bg: #fef3c7; --badge-pending-fg: #d97706;
    --badge-verified-bg: #dbeafe; --badge-verified-fg: #2563eb;
    --badge-accepted-bg: #dcfce7; --badge-accepted-fg: #16a34a;
    --badge-rejected-bg: #fee2e2; --badge-rejected-fg: #dc2626;
    --success-bg: #dcfce7; --success-border: #86efac; --success-fg: #16a34a;
    --error-bg: #fee2e2; --error-border: #fca5a5; --error-fg: #dc2626;
    --info-bg: #dbeafe; --info-border: #93c5fd; --info-fg: #1d4ed8;
    --pill-off: #d1d5db;
    --overlay: rgba(0, 0, 0, 0.5);
    --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.14);
    --ease: cubic-bezier(0.4, 0, 0.2, 1);
    --w-collapsed: 64px;
    --w-expanded: 280px;
    color-scheme: light;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { height: 100%; }
  body {
    background: var(--page);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--tx-body);
    font-size: 14px;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
  }
  button { font: inherit; color: inherit; background: none; border: none; cursor: pointer; }
  a { text-decoration: none; }
  :focus-visible { outline: 2px solid rgba(34, 211, 238, 0.7); outline-offset: 2px; border-radius: 8px; }

  /* ===================== ICON TREATMENT — HugeIcons (no bg, solid color, 1.4–1.6x) ===================== */
  svg.hi {
    display: inline-block;
    width: 1em; height: 1em;
    vertical-align: -0.15em;
    flex: 0 0 auto;
    stroke: currentColor;
  }
  /* Ukuran dasar ikon pada tombol/inline: sedikit lebih besar dari teks */
  .x-btn svg.hi, .r-fbtn svg.hi, .r-gobtn svg.hi, .ste-btn svg.hi,
  .d-btn-coral svg.hi, .d-link svg.hi, .x-link svg.hi, .btn svg.hi,
  button svg.hi, .x-search svg.hi, .r-search svg.hi, .x-cap svg.hi {
    width: 1.15em; height: 1.15em;
  }
  /* Ikon dalam container icon badge (semua class berakhiran -ic):
     tanpa background, tanpa kotak, ukuran 1.4–1.6x */
  [class$="-ic"] {
    background: transparent !important;
    width: auto !important;
    height: auto !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  [class$="-ic"] svg.hi {
    width: 1.5em; height: 1.5em;
  }
  /* Ukuran dasar per konteks (1.4–1.6x dari ukuran ikon FA sebelumnya) */
  .d-ic { font-size: 19px; }
  .d-row-ic { font-size: 16px; }
  .k-sum-ic { font-size: 20px; }
  .d-ic, .d-row-ic, .r-ic, .p-ic, .x-ic, .alg-ic, .m-ic, .det-ic, .pay-ic, .rkp-ic,
  .trk-ic, .sch-ic, .mjr-ic, .prd-ic, .ste-ic, .acc-ic, .k-ic, .k-sum-ic,
  .acc-modal-ic, .acd-row-ic, .d-doc-ic, .m-modal-ic, .m-row-ic, .mjr-modal-ic,
  .prd-modal-ic, .s-modal-ic, .s-row-ic, .s-sec-ic, .ste-biaya-ic, .ste-note-ic,
  .ste-rereg-ic, .t-modal-ic, .t-row-ic, .t-sec-ic, .x-modal-ic { font-size: 17px; }
  /* Warna solid per semantik (bukan soft) */
  .t-coral .d-ic, .d-stat.t-coral svg.hi, .r-ic.coral svg.hi, .p-ic.coral svg.hi { color: var(--coral, #FF6B6B); }
  .t-blue .d-ic, .d-stat.t-blue svg.hi, .r-ic.blue svg.hi, .p-ic.blue svg.hi { color: var(--blue, #3B82F6); }
  .t-green .d-ic, .d-stat.t-green svg.hi, .r-ic.green svg.hi, .p-ic.green svg.hi { color: var(--green, #10B981); }
  .t-amber .d-ic, .d-stat.t-amber svg.hi, .r-ic.amber svg.hi, .p-ic.amber svg.hi { color: var(--amber, #F59E0B); }
  .t-red .d-ic, .d-stat.t-red svg.hi, .r-ic.red svg.hi, .p-ic.red svg.hi { color: var(--red, #EF4444); }
  .t-purple .d-ic, .d-stat.t-purple svg.hi { color: var(--purple, #8B5CF6); }
  .d-row.warning .d-row-ic svg.hi { color: var(--amber, #F59E0B); }
  .d-row.danger .d-row-ic svg.hi { color: var(--red, #EF4444); }
  .d-row.upcoming .d-row-ic svg.hi { color: var(--blue, #3B82F6); }
  .d-sec-title svg.hi { color: var(--coral, #FF6B6B); width: 1.3em; height: 1.3em; }


  /* ===================== SIDEBAR (Bringova minimal) ===================== */
  .sidebar {
    position: fixed;
    top: 0; left: 0; bottom: 0;
    width: 264px;
    background: var(--panel);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    z-index: 40;
    overflow: hidden;
    transition: transform 300ms var(--ease), box-shadow 300ms var(--ease);
  }

  /* ---------- header / avatar ---------- */
  .sb-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 16px 14px;
    flex: 0 0 auto;
  }
  .avatar {
    flex: 0 0 auto;
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; letter-spacing: 0.02em;
    color: #ffffff;
    background: linear-gradient(135deg, #FF6B6B, #FF8E6E);
    box-shadow: 0 6px 16px -6px rgba(255, 107, 107, 0.6);
    user-select: none;
  }
  .who { display: flex; flex-direction: column; min-width: 0; }
  .who .name { font-size: 14px; font-weight: 600; line-height: 1.3; color: var(--tx1); }
  .who .mail { font-size: 11px; color: var(--tx3); line-height: 1.4; }

  /* ---------- labels (always visible) ---------- */
  .flabel {
    display: inline-block;
    overflow: hidden;
    white-space: nowrap;
    max-width: 200px;
  }

  /* ---------- nav ---------- */
  .sb-nav {
    flex: 1 1 auto;
    padding: 6px 12px 12px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.12) transparent;
  }
  .sb-nav::-webkit-scrollbar { width: 4px; }
  .sb-nav::-webkit-scrollbar-thumb { background: var(--tx4); border-radius: 99px; }

  /* ---------- menu item ---------- */
  .mi {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    height: 42px;
    padding: 0 12px;
    border-radius: 10px;
    color: var(--tx2);
    width: 100%;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    transition: background-color 150ms ease, color 150ms ease;
    text-align: left;
  }
  .mi .ic {
    flex: 0 0 auto;
    width: 20px; height: 20px;
    display: flex; align-items: center; justify-content: center;
    transition: transform 150ms ease;
  }
  .mi .ic svg { width: 20px; height: 20px; stroke-width: 1.8; }
  .mi .ic i { font-size: 15px; width: 20px; text-align: center; }
  .mi .flabel { font-size: 13.5px; }
  .mi:hover { background: #fff0ee; color: #FF6B6B; }
  .mi:hover .ic { transform: scale(1.02); }
  .mi:active .ic { transform: scale(0.96); }
  .mi.active { background: #ffe3e0; color: #FF5A5A; font-weight: 600; }
  .mi.active::before {
    content: '';
    position: absolute;
    left: 0; top: 10px; bottom: 10px;
    width: 3px;
    border-radius: 99px;
    background: #FF6B6B;
    box-shadow: 0 0 12px rgba(255, 107, 107, 0.5);
  }
  .mi.danger:hover, .mi.danger:hover .ic { color: #F87171; }

  /* ---------- section header (group label) ---------- */
  .mi.section-btn {
    margin-top: 14px;
    height: 30px;
    cursor: pointer;
  }
  .mi.section-btn .ic { display: none; }
  .mi.section-btn .flabel {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--tx4);
  }
  .chev {
    flex: 0 0 auto;
    width: 14px; height: 14px;
    margin-left: auto;
    color: var(--tx3);
    transform: rotate(-90deg);
    transition: transform 300ms var(--ease);
  }
  .chev svg { width: 14px; height: 14px; stroke-width: 2; display: block; }
  .section.open .chev { transform: rotate(0deg); }

  /* ---------- submenu ---------- */
  .submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 300ms var(--ease);
    margin-left: 8px;
  }
  .section.open > .submenu { max-height: 480px; }

  .si {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    height: 38px;
    padding: 0 12px 0 30px;
    border-radius: 8px;
    color: var(--tx2);
    font-size: 13px;
    text-align: left;
    width: 100%;
    transition: background-color 150ms ease, color 150ms ease;
    -webkit-tap-highlight-color: transparent;
  }
  .si .dot {
    flex: 0 0 auto;
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--tx4);
    transition: background-color 150ms ease, box-shadow 150ms ease;
  }
  .si:hover { background: #fff0ee; color: #FF6B6B; }
  .si:hover .dot { background: #FF6B6B; }
  .si.active { background: #ffe3e0; color: #FF5A5A; font-weight: 600; }
  .si.active::before {
    content: '';
    position: absolute;
    left: 0; top: 9px; bottom: 9px;
    width: 3px;
    border-radius: 99px;
    background: #FF6B6B;
    box-shadow: 0 0 12px rgba(255, 107, 107, 0.5);
  }
  .si.active .dot { background: #FF6B6B; box-shadow: 0 0 8px rgba(255, 107, 107, 0.8); }

  /* ---------- footer ---------- */
  .sb-foot {
    flex: 0 0 auto;
    padding: 10px 12px;
    border-top: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  /* ---------- hamburger (mobile) ---------- */
  .sb-hamburger {
    position: fixed;
    top: 16px; left: 16px;
    z-index: 45;
    width: 40px; height: 40px;
    border-radius: 12px;
    background: var(--panel);
    border: 1px solid var(--border);
    color: var(--tx1);
    display: none;
    align-items: center; justify-content: center;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transition: background-color 150ms ease, transform 150ms ease;
  }
  .sb-hamburger:hover { background: var(--hover); }
  .sb-hamburger:active { transform: scale(0.94); }
  .sb-hamburger svg { width: 20px; height: 20px; stroke-width: 2; }
  .sb-hamburger.hidden { display: none !important; }

  /* ---------- close button (mobile) ---------- */
  .sb-close {
    display: none;
    flex: 0 0 auto;
    width: 32px; height: 32px;
    border-radius: 8px;
    align-items: center; justify-content: center;
    color: var(--tx3);
    margin-left: auto;
    transition: background-color 150ms ease, color 150ms ease, transform 150ms ease;
  }
  .sb-close:hover { background: var(--hover); color: var(--tx1); }
  .sb-close:active { transform: scale(0.94); }
  .sb-close svg { width: 16px; height: 16px; stroke-width: 2; }

  /* ---------- mobile backdrop ---------- */
  .backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(3px);
    opacity: 0;
    visibility: hidden;
    transition: opacity 300ms var(--ease), visibility 300ms var(--ease);
    z-index: 35;
  }
  .backdrop.visible { opacity: 1; visibility: visible; }

  /* ---------- scroll lock ---------- */
  body.scroll-lock { overflow: hidden; }

  /* ---------- main content ---------- */
  .main {
    margin-left: calc(264px + 32px);
    transition: margin-left 300ms var(--ease);
  }
  body.sidebar-open .main { margin-left: calc(264px + 32px); }

  .panel-right { min-height: 100vh; padding: 24px 32px; }

  /* ===================== CONTENT ===================== */
  .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--tx3); margin-bottom: 6px; flex-wrap: wrap; }
  .breadcrumb a { color: var(--accent); text-decoration: none; }
  .breadcrumb .sep { color: var(--tx4); }
  .page-title { font-size: 26px; font-weight: 700; color: var(--tx1); margin-bottom: 6px; }

  .deal-meta { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
  .deal-meta .created { font-size: 13px; color: var(--tx3); }
  .deal-meta .created span { color: var(--tx1); font-weight: 600; }
  .avatar-stack { display: flex; }
  .avatar-stack .av { width: 28px; height: 28px; border-radius: 50%; border: 2px solid var(--page); margin-left: -8px; overflow: hidden; background: var(--panel-2); }
  .avatar-stack .av:first-child { margin-left: 0; }
  .avatar-stack .av img { width: 100%; height: 100%; object-fit: cover; }
  .avatar-stack .av-more { background: var(--accent-soft-bg); color: var(--accent-soft-fg); font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 600; }
  .add-member-btn { display: flex; align-items: center; gap: 4px; padding: 6px 12px; border: 1px dashed var(--input-border); border-radius: 6px; background: none; cursor: pointer; font-size: 12px; color: var(--tx2); }
  .add-member-btn:hover { border-color: var(--accent); color: var(--accent); }

  .tabs { display: flex; gap: 0; border-bottom: 1px solid var(--border); margin-bottom: 20px; flex-wrap: wrap; }
  .tab { padding: 10px 14px; font-size: 13px; color: var(--tx3); cursor: pointer; border-bottom: 2px solid transparent; font-weight: 500; white-space: nowrap; position: relative; text-decoration: none; display: inline-block; }
  .tab.active { color: var(--tx1); border-bottom-color: var(--tx1); font-weight: 600; }
  .tab .tab-badge { background: transparent; border: 1px solid currentColor; color: var(--page); font-size: 10px; border-radius: 10px; padding: 1px 6px; margin-left: 4px; }

  .summary-cards { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
  .summary-card { flex: 1; min-width: 180px; background: var(--panel-2); border: 1px solid var(--border); border-radius: 10px; padding: 16px; color: inherit; text-decoration: none; transition: border-color .15s, transform .15s; }
  a.summary-card:hover { border-color: var(--accent, var(--tx3)); transform: translateY(-2px); }
  .summary-card .label { font-size: 12px; color: var(--tx3); display: flex; align-items: center; gap: 4px; margin-bottom: 6px; }
  .summary-card .label i { font-size: 11px; }
  .summary-card .value { font-size: 30px; font-weight: 700; color: var(--tx1); }
  .summary-card .value small { font-size: 14px; font-weight: 400; color: var(--tx4); }
  .go-to-deals { display: inline-flex; align-items: center; gap: 4px; font-size: 13px; color: var(--tx2); margin-bottom: 20px; cursor: pointer; text-decoration: none; }
  .go-to-deals:hover { color: var(--accent); }

  .doc-tabs { display: flex; align-items: center; gap: 0; margin-bottom: 16px; flex-wrap: wrap; }
  .doc-tab { padding: 8px 16px; font-size: 13px; color: var(--tx3); cursor: pointer; border-radius: 6px; font-weight: 500; text-decoration: none; display: inline-block; }
  .doc-tab.active { background: var(--tx1); color: var(--page); }
  .doc-actions { margin-left: auto; display: flex; gap: 8px; }
  .doc-action-btn { padding: 6px 14px; border: 1px solid var(--input-border); border-radius: 6px; background: var(--input-bg); cursor: pointer; font-size: 12px; color: var(--tx2); display: flex; align-items: center; gap: 5px; }
  .doc-action-btn:hover { border-color: var(--accent); color: var(--accent); }
  .doc-action-btn.primary { background: var(--panel-2); border-color: var(--input-border); }

  .doc-list { display: flex; flex-direction: column; }
  .doc-row { display: flex; align-items: center; padding: 14px 0; border-bottom: 1px solid var(--hairline); }
  .doc-row:last-child { border-bottom: none; }
  .doc-icon { width: 36px; height: 36px; border-radius: 8px; background: var(--panel-2); display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 14px; color: var(--tx3); }
  .doc-info { flex: 1; }
  .doc-name { font-size: 13px; font-weight: 500; color: var(--tx1); }
  .doc-meta { font-size: 11px; color: var(--tx4); margin-top: 2px; }
  .doc-meta span { margin: 0 4px; }
  .doc-status { display: flex; align-items: center; gap: 6px; margin-left: 12px; }
  .doc-status-btn { width: 26px; height: 26px; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 11px; }
  .doc-status-btn.accept { background: var(--success); color: var(--on-success); }
  .doc-status-btn.reject { background: var(--danger); color: var(--on-danger); }
  .doc-menu { width: 26px; height: 26px; border: none; background: none; cursor: pointer; color: var(--tx4); font-size: 16px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }
  .doc-menu:hover { background: var(--panel-2); color: var(--tx1); }

  .data-table { width: 100%; border-collapse: collapse; }
  .data-table th { text-align: left; padding: 10px 14px; font-size: 11px; font-weight: 600; color: var(--tx3); text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid var(--border); }
  .data-table td { padding: 12px 14px; font-size: 13px; color: var(--tx-body); border-bottom: 1px solid var(--hairline); }
  .data-table tr:last-child td { border-bottom: none; }
  .data-table tr:hover td { background: var(--panel-2); }

  .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
  .alert { padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
  .alert-success { background: var(--success-bg); border: 1px solid var(--success-border); color: var(--success-fg); }
  .alert-error { background: var(--error-bg); border: 1px solid var(--error-border); color: var(--error-fg); }
  .alert-info { background: var(--info-bg); border: 1px solid var(--info-border); color: var(--info-fg); }
  .status-pending { background: transparent; border: 1px solid currentColor; color: var(--badge-pending-fg); }
  .status-verified { background: transparent; border: 1px solid currentColor; color: var(--badge-verified-fg); }
  .status-accepted { background: transparent; border: 1px solid currentColor; color: var(--badge-accepted-fg); }
  .status-rejected { background: transparent; border: 1px solid currentColor; color: var(--badge-rejected-fg); }

  .btn { padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
  .btn-primary { background: var(--accent); color: var(--accent-fg); }
  .btn-primary:hover { filter: saturate(1.1) brightness(1.05); }
  .btn-danger { background: var(--danger); color: var(--on-danger); }
  .btn-danger:hover { filter: brightness(0.92); }
  .btn-outline { background: var(--input-bg); border: 1px solid var(--input-border); color: var(--tx2); }
  .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

  .empty-state { text-align: center; padding: 40px 20px; color: var(--tx3); font-size: 13px; }

  .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
  .section-header h3 { font-size: 16px; font-weight: 600; color: var(--tx1); }

  .deadline-notif { background: var(--card-bg); border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-bottom: 20px; }
  .deadline-notif .section-header h3 i { color: var(--warning); margin-right: 6px; }
  .deadline-list { display: flex; flex-direction: column; }
  .deadline-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--hairline); }
  .deadline-item:last-child { border-bottom: none; }
  .deadline-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; font-size: 14px; }
  .deadline-item.warning .deadline-icon { background: transparent; border: 1px solid currentColor; color: var(--badge-pending-fg); }
  .deadline-item.danger .deadline-icon { background: transparent; border: 1px solid currentColor; color: var(--badge-rejected-fg); }
  .deadline-item.upcoming .deadline-icon { background: transparent; border: 1px solid currentColor; color: var(--badge-verified-fg); }
  .deadline-info { flex: 1; }
  .deadline-name { font-size: 13px; font-weight: 500; color: var(--tx1); }
  .deadline-num { font-size: 11px; color: var(--tx4); margin-left: 6px; font-weight: 400; }
  .deadline-meta { font-size: 11px; color: var(--tx3); margin-top: 2px; }
  .deadline-item.danger .deadline-meta { color: var(--badge-rejected-fg); }

  /* ===================== TOAST & MODAL ===================== */
  #toast {
    position: fixed; top: 24px; right: 24px;
    background: var(--tx1); color: var(--page);
    padding: 12px 20px; border-radius: 8px; font-size: 13px;
    z-index: 999; box-shadow: var(--shadow-lg);
  }

  /* ===================== LOADING OVERLAY ===================== */
  #loading-overlay {
    position: fixed; top: 0; right: 0; bottom: 0;
    left: calc(264px + 32px);
    z-index: 90;
    display: flex; align-items: center; justify-content: center;
    background: var(--page);
    opacity: 0; visibility: hidden;
    transition: opacity 200ms ease, visibility 200ms ease;
    pointer-events: none;
  }
  body.sidebar-open #loading-overlay { left: calc(264px + 32px); }
  #loading-overlay.visible { opacity: 1; visibility: visible; }
  #loading-overlay .spinner {
    width: 36px; height: 36px;
    border: 3px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: sb-spin 700ms linear infinite;
  }
  @keyframes sb-spin { to { transform: rotate(360deg); } }
  .modal-overlay {
    position: fixed; inset: 0; background: var(--overlay);
    z-index: 60; align-items: center; justify-content: center; padding: 16px;
  }
  .modal-card {
    background: var(--card-bg); border: 1px solid var(--border);
    border-radius: 12px; box-shadow: var(--shadow-lg);
    max-width: 28rem; width: 100%; padding: 24px;
  }
  .modal-head { display: flex; gap: 12px; margin-bottom: 16px; }
  .modal-icon { width: 40px; height: 40px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
  .modal-icon-amber { background: transparent; border: 1px solid currentColor; color: var(--badge-pending-fg); }
  .modal-title { font-size: 15px; font-weight: 600; color: var(--tx1); }
  .modal-text { font-size: 13px; color: var(--tx2); margin-top: 4px; }
  .modal-sub { font-size: 11px; color: var(--tx3); margin-top: 8px; }
  .modal-checkbox-row { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--tx2); margin-bottom: 16px; cursor: pointer; }
  .modal-checkbox-row input { border-radius: 4px; border: 1px solid var(--input-border); background: var(--input-bg); accent-color: var(--accent); }
  .modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .modal-btn-cancel { padding: 8px 16px; font-size: 13px; color: var(--tx2); background: none; border: none; cursor: pointer; }
  .modal-btn-action { padding: 8px 16px; font-size: 13px; font-weight: 500; border-radius: 6px; color: var(--accent-fg); background: var(--warning); border: none; cursor: pointer; }

  /* Track toggle pill */
  .track-pill { width: 44px; height: 24px; background: transparent; border: 1px solid currentColor; border-radius: 9999px; position: relative; transition: background .2s; }
  .track-pill.on { background: transparent; border: 1px solid currentColor; }
  .track-pill .track-knob { position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; background: transparent; border: 1px solid currentColor; border-radius: 9999px; transition: left .2s; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
  .track-pill.on .track-knob { left: 22px; }
  .track-toggle:disabled + .track-pill { opacity: .55; cursor: wait; }
  .track-toggle:disabled { cursor: wait; }

  /* ===================== RESPONSIVE (mobile) ===================== */
  @media (max-width: 767px) {
    .sb-hamburger { display: flex; }

    .sidebar {
      top: 0; left: 0; bottom: 0;
      width: 280px;
      height: 100dvh;
      border-radius: 0;
      border: none;
      border-right: 1px solid var(--border);
      transform: translateX(-100%);
      transition: transform 300ms var(--ease), box-shadow 300ms var(--ease);
    }
    .sidebar.mobile-open {
      transform: translateX(0);
      box-shadow: 24px 0 64px -16px rgba(0, 0, 0, 0.35);
    }

    .sb-close { display: flex; }

    .main { margin-left: 0; }
    body.sidebar-open .main { margin-left: 0; }
    .panel-right { padding: 76px 16px 24px; }

    #loading-overlay, body.sidebar-open #loading-overlay { left: 0; }
  }
</style>
</head>
<body>

@include('layouts.partials.sidebar')

<div class="main">
  <div class="panel-right">
    <div id="content-area">
    @yield('content')
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" style="display:none;"></div>

<!-- Loading Overlay -->
<div id="loading-overlay">
  <div class="spinner"></div>
</div>

<!-- Reset Pendaftaran Modal (global — di luar #content-area agar tidak hilang saat AJAX) -->
<div id="resetModal" class="modal-overlay" style="display:none;">
  <div class="modal-card">
    <div class="modal-head">
      <div class="modal-icon modal-icon-amber">↺</div>
      <div style="flex:1;">
        <h3 class="modal-title">Reset pendaftaran?</h3>
        <p id="resetModalMessage" class="modal-text"></p>
        <p class="modal-sub">Akun &amp; profil siswa tetap. Dokumen, bukti bayar &amp; invoice PDF ikut dihapus. NIS dihapus jika akun jadi tanpa pendaftaran.</p>
      </div>
    </div>
    <label class="modal-checkbox-row">
      <input type="checkbox" id="resetAllCheckbox">
      <span>Reset <b>semua pendaftaran</b> milik akun ini (opsional)</span>
    </label>
    <div class="modal-actions">
      <button type="button" onclick="closeResetModal()" class="modal-btn-cancel">Batal</button>
      <button type="button" id="resetModalAction" class="modal-btn-action">Ya, Reset</button>
    </div>
    <form id="resetForm" method="POST" style="display:none">
      @csrf
      <input type="hidden" name="scope" id="resetScopeInput" value="one">
    </form>
  </div>
</div>

@include('components.file-preview-modal')

<script>
// === Ikon HugeIcons (JS) ===
// Map kecil untuk ikon yang dirender via innerHTML (bukan komponen Blade).
// Body SVG diambil dari koleksi HugeIcons (icones.js.org/collection/hugeicons).
var __HI = {
  "checkmark": '<path d="M5 12l5 5 9-10"/>',
  "checkmark-circle-01": '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/>',
  "folder-open": '<path d="M3 8.5A1.5 1.5 0 0 1 4.5 7h4.1c.4 0 .8.2 1.1.4l1.6 1.2c.3.2.7.4 1.1.4h6.1A1.5 1.5 0 0 1 20 10.5V17a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8.5Z"/><path d="M3 10h18"/>',
  "loading-01": '<path d="M12 3a9 9 0 1 0 9 9"/>',
  "key-01": '<circle cx="8" cy="15" r="4"/><path d="M11 12l8-8M15 8l3 3M17 6l2 2"/>',
  "delete-02": '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
  "layer-group": '<path d="M12 3l9 5-9 5-9-5 9-5Z"/><path d="M3 12l9 5 9-5"/><path d="M3 16.5l9 5 9-5"/>',
  "calendar-01": '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/>',
  "credit-card": '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
  "coins-01": '<circle cx="12" cy="12" r="8"/><path d="M12 8v8M9.5 9.5h3.5a1.5 1.5 0 0 1 0 3H10a1.5 1.5 0 0 0 0 3h4"/>',
  "clock-01": '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
  "calendar-check-in-01": '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18M9 16l2 2 4-4"/>',
  "layers-01": '<path d="M12 3l9 5-9 5-9-5 9-5Z"/><path d="M3 12l9 5 9-5"/><path d="M3 16.5l9 5 9-5"/>'
};
function hiSvg(name, style) {
  var body = (window.__HI || {})[name] || '';
  if (!body) return '';
  var st = style ? ' style="' + style + '"' : '';
  return '<svg class="hi" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"' + st + '>' + body + '</svg>';
}
function hiHtml(name, style) { return hiSvg(name, style); }

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
function showReRegRejectModal(reRegistrationId) {
  var modal = document.getElementById('reRegRejectModal');
  var form = document.getElementById('reRegRejectForm');
  if (form) form.action = '/admin/re-registrations/' + reRegistrationId + '/reject';
  if (modal) modal.style.display = 'flex';
}
function hideReRegRejectModal() {
  var modal = document.getElementById('reRegRejectModal');
  if (modal) modal.style.display = 'none';
}
function toggleFilterForm() {
  var f = document.getElementById('filterForm');
  if (f) f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
function toggleFilterPanel() {
  var p = document.getElementById('filterPanel');
  if (p) p.style.display = p.style.display === 'none' ? 'flex' : 'none';
}

// ===================== Payment Verify / Reset (global — works with AJAX nav) =====================
// Halaman /admin/payments dimuat via AJAX (loadContent → innerHTML), sehingga <script>
// di dalam partial TIDAK dieksekusi. Fungsi-fungsi ini didefinisikan di layout agar
// selalu tersedia, baik saat full render maupun saat konten dimuat via AJAX.
var _payVerifyId = null;
var _payResetId = null;

function _payShowModal(el) { if (el) { el.classList.add('is-open'); el.setAttribute('aria-hidden', 'false'); } }
function _payHideModal(el) { if (el) { el.classList.remove('is-open'); el.setAttribute('aria-hidden', 'true'); } }

window.openPayVerify = function (id, regNumber) {
  _payVerifyId = id;
  var msg = document.getElementById('payVerifyMsg');
  if (msg) msg.textContent = 'Verifikasi pembayaran ' + (regNumber || '') + ' sebagai lunas?';
  _payShowModal(document.getElementById('payVerifyModal'));
};
window.closePayVerify = function () { _payVerifyId = null; _payHideModal(document.getElementById('payVerifyModal')); };
window.submitPayVerify = function () {
  if (!_payVerifyId) return;
  var btn = document.getElementById('payVerifyAction');
  if (btn) btn.disabled = true;
  var csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
  fetch('/admin/payments/' + _payVerifyId + '/verify', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
  .then(function (res) {
    if (btn) btn.disabled = false;
    closePayVerify();
    if (!res.ok || !res.body.success) { alert(res.body.message || 'Gagal memverifikasi pembayaran'); return; }
    location.reload();
  })
  .catch(function () { if (btn) btn.disabled = false; closePayVerify(); alert('Terjadi kesalahan jaringan'); });
};

window.openPayReset = function (id, regNumber) {
  _payResetId = id;
  var msg = document.getElementById('payResetMsg');
  if (msg) msg.textContent = 'Kembalikan pembayaran ' + (regNumber || '') + ' ke status pending? Data pembayaran terkait akan dihapus.';
  _payShowModal(document.getElementById('payResetModal'));
};
window.closePayReset = function () { _payResetId = null; _payHideModal(document.getElementById('payResetModal')); };
window.submitPayReset = function () {
  if (!_payResetId) return;
  var btn = document.getElementById('payResetAction');
  if (btn) btn.disabled = true;
  var csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
  fetch('/admin/payments/' + _payResetId + '/reset', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
  .then(function (res) {
    if (btn) btn.disabled = false;
    closePayReset();
    if (!res.ok || !res.body.success) { alert(res.body.message || 'Gagal mereset pembayaran'); return; }
    location.reload();
  })
  .catch(function () { if (btn) btn.disabled = false; closePayReset(); alert('Terjadi kesalahan jaringan'); });
};

// Backdrop click + Enter shortcut — delegated agar tetap bekerja untuk konten hasil AJAX
document.addEventListener('click', function (e) {
  if (e.target && e.target.id === 'payVerifyModal') closePayVerify();
  if (e.target && e.target.id === 'payResetModal') closePayReset();
});
document.addEventListener('keydown', function (e) {
  if (e.key !== 'Enter') return;
  var v = document.getElementById('payVerifyModal');
  var r = document.getElementById('payResetModal');
  if (v && v.classList.contains('is-open')) { e.preventDefault(); submitPayVerify(); }
  else if (r && r.classList.contains('is-open')) { e.preventDefault(); submitPayReset(); }
});

// ===================== Picker Bringova (dropdown modal) =====================
(function () {
  var currentKey = null;
  var currentTrigger = null;
  var currentInput = null;
  var currentValue = null;

  function $(sel, root) { return (root || document).querySelector(sel); }
  function $all(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

  function getBackdrop() { return document.getElementById('pickerBackdrop'); }
  function getPanel()    { return $('.reg .picker-panel'); }
  function getList()     { return document.getElementById('pickerList'); }
  function getSearch()   { return document.getElementById('pickerSearch'); }
  function getTitle()    { return document.getElementById('pickerTitle'); }

  function findByValue(arr, val) {
    if (val === null || val === undefined) return null;
    var sv = String(val);
    for (var i = 0; i < arr.length; i++) {
      if (String(arr[i].v) === sv) return arr[i];
    }
    return null;
  }

  function syncTriggerLabel(trigger) {
    if (!trigger) return;
    var key = trigger.getAttribute('data-picker');
    var input = document.querySelector('[data-picker-input="' + key + '"]');
    var data = (window.__pickerData || {})[key] || [];
    var labelEl = trigger.querySelector('.pick-label');
    var v = input ? input.value : '';
    var found = findByValue(data, v);
    if (found && String(found.v) !== '') {
      labelEl.textContent = found.l;
      labelEl.classList.remove('is-placeholder');
      trigger.classList.add('has-value');
    } else {
      labelEl.textContent = 'Pilih ' + ((window.__pickerLabels || {})[key] || 'item').toLowerCase().replace(/^pilih\s/, '') + '…';
      labelEl.classList.add('is-placeholder');
      trigger.classList.remove('has-value');
    }
  }

  function renderList(filter) {
    var data = (window.__pickerData || {})[currentKey] || [];
    var list = getList();
    if (!list) return;
    list.innerHTML = '';
    var f = (filter || '').toLowerCase().trim();
    var rows = data.filter(function (it) {
      if (!f) return true;
      return String(it.l).toLowerCase().indexOf(f) !== -1;
    });
    if (rows.length === 0) {
      var empty = document.createElement('div');
      empty.className = 'picker-empty';
      empty.innerHTML = hiSvg('folder-open') + ' Tidak ada item yang cocok';
      list.appendChild(empty);
      return;
    }
    rows.forEach(function (it) {
      var div = document.createElement('div');
      div.className = 'picker-item' + (String(it.v) === String(currentValue) ? ' is-selected' : '');
      div.setAttribute('role', 'option');
      div.setAttribute('data-value', it.v);
      div.innerHTML = '<span class="pi-label">' + escapeHtml(it.l) + '</span>' + hiSvg('checkmark');
      div.addEventListener('click', function () { selectValue(it.v, it.l); });
      list.appendChild(div);
    });
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function selectValue(v, l) {
    currentValue = v;
    if (currentInput) {
      currentInput.value = v;
      try { currentInput.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) {}
      // majors filter: trigger table reload when level/school picker changes
      if (currentInput.id === 'mjrLevel' || currentInput.id === 'mjrSchool') {
        if (typeof filterTable === 'function' && document.getElementById('mjrBody')) filterTable('majors');
      }
    }
    if (currentTrigger) {
      currentTrigger.querySelector('.pick-label').textContent = l;
      currentTrigger.querySelector('.pick-label').classList.remove('is-placeholder');
      if (String(v) !== '') currentTrigger.classList.add('has-value');
      else currentTrigger.classList.remove('has-value');
    }
    // Update selected highlight
    $all('.picker-item', getList()).forEach(function (el) {
      el.classList.toggle('is-selected', el.getAttribute('data-value') === String(v));
    });
  }

  function openPicker(key, trigger) {
    currentKey = key;
    currentTrigger = trigger;
    currentInput = document.querySelector('[data-picker-input="' + key + '"]');
    currentValue = currentInput ? currentInput.value : '';
    var data = (window.__pickerData || {})[key] || [];
    if (getTitle()) getTitle().textContent = (window.__pickerLabels || {})[key] || 'Pilih item';
    if (getSearch()) { getSearch().value = ''; }
    renderList('');
    var bd = getBackdrop();
    if (bd) {
      bd.classList.add('is-open');
      bd.setAttribute('aria-hidden', 'false');
    }
    $all('.r-pick', document).forEach(function (t) { t.setAttribute('aria-expanded', t === trigger ? 'true' : 'false'); });
    // Focus search
    setTimeout(function () { if (getSearch()) getSearch().focus(); }, 30);
  }

  function closePicker() {
    var bd = getBackdrop();
    if (bd) {
      bd.classList.remove('is-open');
      bd.setAttribute('aria-hidden', 'true');
    }
    $all('.r-pick', document).forEach(function (t) { t.setAttribute('aria-expanded', 'false'); });
    currentKey = null;
    currentTrigger = null;
    currentInput = null;
    currentValue = null;
  }

  function clearCurrent() {
    if (currentInput) {
      currentInput.value = '';
      try { currentInput.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) {}
      if (currentInput.id === 'mjrLevel' || currentInput.id === 'mjrSchool') {
        if (typeof filterTable === 'function' && document.getElementById('mjrBody')) filterTable('majors');
      }
    }
    if (currentTrigger) {
      currentTrigger.classList.remove('has-value');
      syncTriggerLabel(currentTrigger);
    }
    currentValue = '';
    renderList(getSearch() ? getSearch().value : '');
  }

  window.openPicker = function (key, trigger) { openPicker(key, trigger); };
  window.closePicker = closePicker;
  window.clearPicker = function (key) {
    var input = document.querySelector('[data-picker-input="' + key + '"]');
    var trigger = document.querySelector('.r-pick[data-picker="' + key + '"]');
    if (input) {
      input.value = '';
      try { input.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) {}
      if (input.id === 'mjrLevel' || input.id === 'mjrSchool') {
        if (typeof filterTable === 'function' && document.getElementById('mjrBody')) filterTable('majors');
      }
    }
    if (trigger) syncTriggerLabel(trigger);
  };
  window.clearCurrentPicker = clearCurrent;

  // Init: bind trigger click + search input + backdrop click + ESC key
  function init() {
    // Ambil data dari kontainer #reg-data (yang ada di partial, baik full render maupun AJAX)
    var dataEl = document.getElementById('reg-data');
    if (dataEl) {
      try {
        window.__pickerData   = JSON.parse(dataEl.getAttribute('data-picker') || '{}');
        window.__pickerLabels = JSON.parse(dataEl.getAttribute('data-picker-labels') || '{}');
      } catch (e) {
        window.__pickerData = window.__pickerData || {};
        window.__pickerLabels = window.__pickerLabels || {};
      }
    }
    $all('.r-pick[data-picker]').forEach(function (trigger) {
      if (trigger.__pickerBound) return;
      trigger.__pickerBound = true;
      var key = trigger.getAttribute('data-picker');
      syncTriggerLabel(trigger);
      trigger.addEventListener('click', function (e) {
        var clearEl = e.target.closest('.pick-clear');
        if (clearEl) {
          e.preventDefault();
          e.stopPropagation();
          clearPicker(key);
          return;
        }
        openPicker(key, trigger);
      });
    });
    var search = getSearch();
    if (search && !search.__pickerBound) {
      search.__pickerBound = true;
      search.addEventListener('input', function () { renderList(search.value); });
    }
    var bd = getBackdrop();
    if (bd && !bd.__pickerBound) {
      bd.__pickerBound = true;
      bd.addEventListener('click', function (e) {
        if (e.target === bd) closePicker();
      });
    }
    if (!window.__pickerKeyBound) {
      window.__pickerKeyBound = true;
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && getBackdrop() && getBackdrop().classList.contains('is-open')) {
          closePicker();
        }
      });
    }
  }

  window.pickerInitAll = function () { init(); };
  // Auto-init on first load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

// === Toast ===
function showToast(msg) {
  var t = document.getElementById('toast');
  t.textContent = msg;
  t.style.display = 'block';
  setTimeout(function () { t.style.display = 'none'; }, 2500);
}

document.addEventListener('DOMContentLoaded', function () {
  var contentArea = document.getElementById('content-area');
  var isLoading = false;

  // ================= SIDEBAR (static, selalu diperluas) =================
  var sidebar = document.getElementById('sidebar');
  var tip = document.getElementById('tooltip');
  var hamburger = document.getElementById('hamburger');
  var closeBtn = document.getElementById('closeBtn');
  var backdrop = document.getElementById('backdrop');
  var state = { expanded: true, pinned: false, hoverTimer: null, mobileOpen: false };

  var isMobile = function () {
    return window.matchMedia('(max-width: 767px)').matches;
  };

  function setExpanded(value) {
    state.expanded = value;
    sidebar.classList.toggle('expanded', value);
    document.body.classList.toggle('sidebar-open', value);
    if (value) hideTip();
  }

  function toggleSidebar() {
    state.pinned = !state.expanded;
    setExpanded(!state.expanded);
  }

  // -------- mobile drawer --------
  function openDrawer() {
    if (!isMobile()) return;
    state.mobileOpen = true;
    sidebar.classList.add('mobile-open');
    backdrop.classList.add('visible');
    hamburger.classList.add('hidden');
    hamburger.setAttribute('aria-expanded', 'true');
    document.body.classList.add('scroll-lock');
    if (closeBtn) closeBtn.focus();
  }

  function closeDrawer() {
    state.mobileOpen = false;
    sidebar.classList.remove('mobile-open');
    backdrop.classList.remove('visible');
    hamburger.classList.remove('hidden');
    hamburger.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('scroll-lock');
    hamburger.focus();
  }

  hamburger.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);

  window.addEventListener('resize', function () {
    if (!isMobile() && state.mobileOpen) {
      sidebar.classList.remove('mobile-open');
      backdrop.classList.remove('visible');
      hamburger.classList.remove('hidden');
      hamburger.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('scroll-lock');
      state.mobileOpen = false;
    }
  });

  // ================= TOOLTIP =================
  function showTip(anchor) {
    if (state.expanded) return;
    var r = anchor.getBoundingClientRect();
    tip.textContent = anchor.getAttribute('data-tip') || '';
    tip.classList.add('visible');
    var tw = tip.offsetWidth, th = tip.offsetHeight;
    var left = r.right + 12, top = r.top + r.height / 2 - th / 2;
    if (left + tw > window.innerWidth - 8) left = r.left - tw - 12;
    if (top < 8) top = 8;
    if (top + th > window.innerHeight - 8) top = window.innerHeight - th - 8;
    tip.style.left = left + 'px';
    tip.style.top = top + 'px';
  }
  function hideTip() {
    tip.classList.remove('visible');
    tip.textContent = '';
  }

  function bindHover(parent) {
    parent.addEventListener('mouseover', function (e) {
      var el = e.target.closest('[data-tip]');
      if (el) showTip(el);
    });
    parent.addEventListener('mouseout', function (e) {
      var el = e.target.closest('[data-tip]');
      if (el) hideTip();
    });
  }
  bindHover(sidebar);
  sidebar.addEventListener('focusin', function (e) {
    var el = e.target.closest('[data-tip]');
    if (el) showTip(el);
  });
  sidebar.addEventListener('focusout', function (e) {
    var el = e.target.closest('[data-tip]');
    if (el) hideTip();
  });

  // ================= SECTION TOGGLE + THEME (delegated) =================
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-action]');
    if (!el) return;
    var action = el.getAttribute('data-action');

    if (action === 'toggle') {
      if (isMobile() && !state.mobileOpen) { openDrawer(); return; }
      if (!state.expanded) { state.pinned = false; setExpanded(true); }
      var section = el.closest('.section');
      var open = section.classList.toggle('open');
      el.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
  });

  // ================= AJAX NAVIGATION =================
  document.querySelectorAll('.sb-nav [data-menu-item], .sb-foot [data-menu-item]').forEach(function (navLink) {
    navLink.addEventListener('click', function (e) {
      var href = navLink.getAttribute('href') || '';
      if (href.indexOf('/admin/settings') !== -1) return;
      e.preventDefault();
      if (isMobile() && state.mobileOpen) closeDrawer();
      loadContent(href);
    });
  });

  // ================= CONTENT AREA DELEGATION =================
  contentArea.addEventListener('click', function (e) {
    var tab = e.target.closest('a.tab, a.doc-tab, a.go-to-deals, a.summary-card');
    if (tab) {
      e.preventDefault();
      loadContent(tab.getAttribute('href'));
      return;
    }

    var pag = e.target.closest('.pagination a');
    if (pag) {
      e.preventDefault();
      loadContent(pag.getAttribute('href'));
      return;
    }

    var navPag = e.target.closest('nav[aria-label="Pagination"] a, nav[aria-label="Pagination Navigation"] a, nav a[href*="page="]');
    if (navPag) {
      e.preventDefault();
      loadContent(navPag.getAttribute('href'));
      return;
    }

    var actReset = e.target.closest('a[href*="activity-logs"]');
    if (actReset && actReset.getAttribute('href').indexOf('/export/') === -1) {
      e.preventDefault();
      loadContent(actReset.getAttribute('href'));
      return;
    }

    if (e.target.id === 'statusModal' || e.target.id === 'paymentModal' || e.target.id === 'rejectModal' || e.target.id === 'reRegRejectModal') {
      e.target.style.display = 'none';
    }
  });

  contentArea.addEventListener('submit', function (e) {
    if (e.target.id === 'statusForm' || e.target.id === 'paymentForm' || e.target.id === 'reRegRejectForm') {
      e.preventDefault();
      var form = e.target;
      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        var modal = form.closest('[style*="position:fixed"]') || document.getElementById('reRegRejectModal');
        if (modal) modal.style.display = 'none';
        if (data.success) {
          showToast(data.message);
          loadContent(window.location.href, false);
        } else {
          showToast(data.message || 'Terjadi kesalahan');
        }
      })
      .catch(function () { showToast('Gagal terhubung ke server'); });
      return;
    }

    if (e.target.id === 'filterForm') {
      e.preventDefault();
      var url = new URL(e.target.getAttribute('action'), window.location.origin);
      var fd = new FormData(e.target);
      for (var pair of fd.entries()) {
        if (pair[1]) url.searchParams.set(pair[0], pair[1]);
        else url.searchParams.delete(pair[0]);
      }
      loadContent(url.pathname + url.search);
    }
  });

  // Track status toggles (delegated — konten di-inject via AJAX)
  // Alur: konfirmasi saat menonaktifkan → loading (anti double-click) → PATCH AJAX → update UI lokal.
  contentArea.addEventListener('change', function (e) {
    var el = e.target;
    if (!el.classList || !el.classList.contains('track-toggle')) return;
    var row = el.closest('.track-row');
    var trackId = el.getAttribute('data-track');
    var levelId = el.getAttribute('data-level');
    var trackName = el.getAttribute('data-track-name');
    var levelName = el.getAttribute('data-level-name');
    var isActive = el.checked;
    var wasActive = el.getAttribute('data-status') !== '0';

    // Konfirmasi hanya saat MENONAKTIFKAN — aktifkan langsung (tanpa konfirmasi).
    if (!isActive) {
      // Pakai modal konfirmasi Bringova bila tersedia (didefinisikan partial tracks), else native confirm.
      if (typeof window.confirmTrackDeactivate === 'function') {
        window.confirmTrackDeactivate({
          el: el, row: row, trackId: trackId, levelId: levelId,
          trackName: trackName, levelName: levelName, wasActive: wasActive
        });
        return; // modal menangani sisanya
      }
      var ok = window.confirm('Nonaktifkan jalur ' + trackName + ' untuk jenjang ' + levelName + '?\n\nJalur ini tidak akan muncul di form pendaftaran siswa dan ditolak di backend. Data historis pendaftar lama tetap tersimpan.');
      if (!ok) { el.checked = wasActive; return; }
    }
    doTrackToggle(el, row, trackId, levelId, trackName, levelName, isActive, wasActive);
  });

  // Eksekusi toggle track (loading → PATCH AJAX → update UI lokal). Dipanggil dari listener
  // langsung (saat aktifkan) atau dari modal konfirmasi Bringova (saat nonaktifkan).
  function doTrackToggle(el, row, trackId, levelId, trackName, levelName, isActive, wasActive) {
    // Loading state: disable toggle sampai request selesai (cegah double-click).
    el.disabled = true;
    var pill = el.nextElementSibling;
    if (pill) pill.classList.add('track-pill-busy');

    function finish() {
      el.disabled = false;
      if (pill) pill.classList.remove('track-pill-busy');
    }

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
    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
    .then(function (res) {
      finish();
      if (!res.ok || !res.body.success) {
        el.checked = wasActive;
        showToast(res.body.message || 'Gagal menyimpan perubahan');
        return;
      }
      // Update UI lokal tanpa reload: checkbox + badge + pill mengikuti status server.
      var on = !!res.body.is_active;
      el.checked = on;
      el.setAttribute('data-status', on ? '1' : '0');
      if (row) {
        var badge = row.querySelector('.track-badge');
        if (badge) {
          badge.className = 'status-badge track-badge ' + (on ? 'status-accepted' : 'status-rejected');
          badge.removeAttribute('style');
        }
      }
      if (pill) pill.classList.toggle('on', on);
      showToast(res.body.message || (trackName + ' untuk ' + levelName + ' diperbarui'));
    })
    .catch(function () {
      finish();
      el.checked = wasActive;
      showToast('Gagal terhubung ke server');
    });
  }
  window.doTrackToggle = doTrackToggle;

  function loadContent(url, pushState) {
    if (pushState === undefined) pushState = true;
    if (isLoading) return;
    isLoading = true;
    showLoading();

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Cache-Control': 'no-cache', 'X-SPMB-Full': '1' }, cache: 'no-store' })
      .then(function (r) {
        var ct = r.headers.get('content-type') || '';
        if (ct.indexOf('application/json') === -1) {
          hideLoading();
          window.location.href = url;
          throw new Error('full-page');
        }
        return r.json();
      })
      .then(function (data) {
        contentArea.innerHTML = data.html;
        isLoading = false;
        hideLoading();
        if (pushState) history.pushState({ url: url }, '', url);
        updateSidebar(url);
        window.scrollTo(0, 0);
        if (typeof window.datepickerInitAll === 'function') window.datepickerInitAll();
        if (typeof window.pickerInitAll === 'function') window.pickerInitAll();
        var alert = contentArea.querySelector('.ajax-success');
        if (alert) setTimeout(function () { alert.remove(); }, 3000);
      })
      .catch(function () {
        isLoading = false;
        hideLoading();
      });
  }

  function showLoading() {
    var el = document.getElementById('loading-overlay');
    if (el) el.classList.add('visible');
  }

  function hideLoading() {
    var el = document.getElementById('loading-overlay');
    if (el) el.classList.remove('visible');
  }

  function updateSidebar(url) {
    document.querySelectorAll('.sb-nav .mi, .sb-nav .si, .sb-foot [data-menu-item]').forEach(function (item) { item.classList.remove('active'); });
    document.querySelectorAll('.sb-nav .section').forEach(function (s) { s.classList.remove('open'); });
    // Profile (di luar /admin) — aktifkan item footer profile.
    if (url.indexOf('/profile') !== -1) {
      var prof = document.querySelector('.sb-foot [data-menu-item][href*="/profile"]');
      if (prof) prof.classList.add('active');
      return;
    }
    var m = url.match(/\/admin\/([a-z-]+)/);
    if (!m) return;
    var seg = m[1];
    var target = null;
    document.querySelectorAll('.sb-nav [data-menu-item]').forEach(function (item) {
      var href = item.getAttribute('href') || '';
      if (href.indexOf('/admin/' + seg) !== -1) target = item;
    });
    if (target) {
      target.classList.add('active');
      var section = target.closest('.section');
      if (section) {
        section.classList.add('open');
        var h = section.querySelector('[data-action="toggle"]');
        if (h) h.setAttribute('aria-expanded', 'true');
      }
    } else {
      var dash = document.querySelector('.sb-nav [data-menu-item][href*="dashboard"]');
      if (dash) dash.classList.add('active');
    }
  }

  window.addEventListener('popstate', function (e) {
    if (e.state && e.state.url) {
      if (e.state.url.indexOf('/admin/settings') !== -1) { window.location.href = e.state.url; return; }
      loadContent(e.state.url, false);
    }
  });
  history.replaceState({ url: window.location.href }, '', window.location.href);

  // ============ MAJORS & PERIODS: filter AJAX (delegated) ============
  var mjrDebounce = null, prdDebounce = null;
  var mjrController = null, prdController = null;

  function filterTable(kind) {
    var isM = kind === 'majors';
    var body = document.getElementById(isM ? 'mjrBody' : 'prdBody');
    var search = document.getElementById(isM ? 'mjrSearch' : 'prdSearch');
    if (!body) return;
    var params = new URLSearchParams();
    if (search && search.value.trim()) params.set('q', search.value.trim());
    if (isM) {
      var ml = document.getElementById('mjrLevel');
      var ms = document.getElementById('mjrSchool');
      if (ml && ml.value) params.set('level', ml.value);
      if (ms && ms.value) params.set('school_id', ms.value);
    } else {
      var pl = document.getElementById('prdLevel');
      var ps = document.getElementById('prdStatus');
      var py = document.getElementById('prdYear');
      if (pl && pl.value) params.set('level', pl.value);
      if (ps && ps.value) params.set('status', ps.value);
      if (py && py.value) params.set('academic_year', py.value);
      history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
    }
    var cls = isM ? 'mjr-skeleton' : 'prd-skeleton';
    var wrap = isM ? 'mjr-table-wrap' : 'prd-table-wrap';
    body.innerHTML = '<div class="' + wrap + '"><table class="data-table"><tbody class="' + cls + '">' +
      '<tr><td colspan="20"><span class="skel" style="width:100%"></span></td></tr>' +
      '<tr><td colspan="20"><span class="skel" style="width:70%"></span></td></tr>' +
      '<tr><td colspan="20"><span class="skel" style="width:90%"></span></td></tr>' +
      '</tbody></table></div>';
    if (isM) { if (mjrController) mjrController.abort(); mjrController = new AbortController(); }
    else { if (prdController) prdController.abort(); prdController = new AbortController(); }
    fetch(window.location.pathname + '?' + params.toString(), {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      signal: isM ? mjrController.signal : prdController.signal
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data && data.html) body.innerHTML = data.html;
      if (data && data.total !== undefined) {
        var totalEl = document.getElementById(isM ? 'mjrTotal' : 'prdTotal');
        if (totalEl) totalEl.innerHTML = hiSvg(isM ? 'layer-group' : 'calendar-01', 'font-size:11px;') + ' Total <strong>' + data.total + '</strong> ' + (isM ? 'jurusan' : 'periode');
      }
    })
    .catch(function (err) { if (err && err.name === 'AbortError') return; });
  }

  document.addEventListener('input', function (e) {
    var id = e.target && e.target.id;
    if (id === 'mjrSearch') { clearTimeout(mjrDebounce); mjrDebounce = setTimeout(function () { filterTable('majors'); }, 300); }
    else if (id === 'prdSearch') { clearTimeout(prdDebounce); prdDebounce = setTimeout(function () { filterTable('periods'); }, 300); }
  });
  document.addEventListener('change', function (e) {
    var id = e.target && e.target.id;
    if (id === 'mjrLevel' || id === 'mjrSchool') filterTable('majors');
    else if (id === 'prdLevel' || id === 'prdStatus' || id === 'prdYear') filterTable('periods');
  });

  // ============ MAJORS & PERIODS: modal hapus (delegated) ============
  window.openMajorDelete = function (id, name) {
    var modal = document.getElementById('majorDeleteModal');
    var nameEl = document.getElementById('majorDeleteName');
    var form = document.getElementById('majorDeleteForm');
    if (nameEl) nameEl.textContent = name;
    if (form) form.action = '/admin/majors/' + id;
    if (modal) {
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      modal.style.display = 'flex';
    }
  };
  window.closeMajorDelete = function () {
    var modal = document.getElementById('majorDeleteModal');
    if (modal) {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      modal.style.display = 'none';
    }
  };
  window.openPeriodDelete = function (id, name, count) {
    var modal = document.getElementById('periodDeleteModal');
    var nameEl = document.getElementById('prdDeleteName');
    var form = document.getElementById('prdDeleteForm');
    var check = document.getElementById('prdConfirmCheck');
    var confirmBtn = document.getElementById('prdDeleteConfirm');
    var blockedBox = document.getElementById('prdBlockedBox');
    var blockedCount = document.getElementById('prdBlockedCount');
    var confirmWrap = document.getElementById('prdConfirmWrap');
    var sub = document.getElementById('prdModalSub');
    var title = document.getElementById('prdModalTitle');
    count = parseInt(count, 10) || 0;
    if (nameEl) nameEl.textContent = name;
    if (form) form.action = '/admin/periods/' + id;
    if (check) check.checked = false;
    if (count > 0) {
      if (blockedBox) blockedBox.style.display = 'block';
      if (blockedCount) blockedCount.textContent = count;
      if (confirmWrap) confirmWrap.style.display = 'none';
      if (confirmBtn) { confirmBtn.disabled = true; confirmBtn.style.opacity = '0.5'; confirmBtn.style.pointerEvents = 'none'; }
      if (title) title.textContent = 'Tidak dapat menghapus';
      if (sub) sub.textContent = 'Periode ini sudah terpakai. Gunakan tombol Edit untuk menonaktifkan periode.';
    } else {
      if (blockedBox) blockedBox.style.display = 'none';
      if (confirmWrap) confirmWrap.style.display = 'flex';
      if (confirmBtn) { confirmBtn.disabled = true; confirmBtn.style.opacity = '0.5'; confirmBtn.style.pointerEvents = 'none'; }
      if (title) title.textContent = 'Hapus periode?';
      if (sub) sub.textContent = 'Periode yang sudah memiliki pendaftar tidak dapat dihapus — nonaktifkan saja.';
    }
    if (modal) modal.style.display = 'flex';
  };
  window.closePeriodDelete = function () {
    var modal = document.getElementById('periodDeleteModal');
    if (modal) modal.style.display = 'none';
  };
  document.addEventListener('click', function (e) {
    var m = document.getElementById('majorDeleteModal');
    if (m && m.style.display === 'flex' && e.target === m) window.closeMajorDelete();
    var p = document.getElementById('periodDeleteModal');
    if (p && p.style.display === 'flex' && e.target === p) window.closePeriodDelete();
  });
  document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'prdConfirmCheck') {
      var btn = document.getElementById('prdDeleteConfirm');
      if (btn) {
        btn.disabled = !e.target.checked;
        btn.style.opacity = e.target.checked ? '1' : '0.5';
        btn.style.pointerEvents = e.target.checked ? 'auto' : 'none';
      }
    }
  });
  document.addEventListener('submit', function (e) {
    if (e.target && e.target.id === 'prdDeleteForm') {
      var btn = document.getElementById('prdDeleteConfirm');
      if (btn) { btn.disabled = true; btn.innerHTML = hiSvg('loading-01', 'font-size:11px;') + ' Menghapus...'; }
    }
  });

  // Escape: tutup modal / drawer
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (state.mobileOpen) { closeDrawer(); e.preventDefault(); return; }
      var rejectModal = document.getElementById('rejectModal');
      if (rejectModal && rejectModal.style.display === 'flex') rejectModal.style.display = 'none';
      var resetModal = document.getElementById('resetModal');
      if (resetModal && resetModal.style.display === 'flex') closeResetModal();
      var majorModal = document.getElementById('majorDeleteModal');
      if (majorModal && majorModal.style.display === 'flex') window.closeMajorDelete();
      var periodModal = document.getElementById('periodDeleteModal');
      if (periodModal && periodModal.style.display === 'flex') window.closePeriodDelete();
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
    if (m) m.style.display = 'flex';
  };
  window.closeResetModal = function () {
    var m = document.getElementById('resetModal');
    if (m) m.style.display = 'none';
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