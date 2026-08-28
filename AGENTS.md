# AGENTS — Sekolahin (SPMB)

## Desain Frontend Default (WAJIB)

Project ini memakai **Design System Bringova** sebagai default untuk SEMUA pekerjaan frontend.

- Sumber kebenaran: [`design-system/bringova.md`](design-system/bringova.md) — dibaca otomatis via `opencode.json` `instructions`.
- Opencode WAJIB mengacu ke file tersebut untuk setiap makeover, komponen baru, atau relayout — tanpa perlu diminta ulang.
- Jika ada konflik antara style lama (`eggplore`, Tailwind native, `bg-white` card) dan Bringova, **Bringova menang**.

## Aturan Konsisten

- Tanpa kartu putih — pakai background `#f6f7fb` + divider.
- Scoped CSS per halaman, modal & picker DI DALAM wrapper.
- Native `<select>` / `confirm()` → modal Bringova.
- Pagination `vendor.pagination.bringova`.

## Gaya Komunikasi (WAJIB)

- Jawab SANGAT SINGKAT. Maksimal 3–5 baris teks biasa (kecuali user minta detail).
- Jangan jelaskan langkah demi langkah, reasoning, atau cara kerjakan.
- Setelah selesai task, hanya kirim daftar file yang diubah/dibuat, ringkasan 1 kalimat, dan error/keputusan penting jika ada.
