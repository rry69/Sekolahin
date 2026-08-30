<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Ikon HugeIcons (dari icones.js.org/collection/hugeicons).
 *
 * Memuat body SVG dari file JSON lokal (_hugeicons.json) supaya inline,
 * tanpa dependency CDN / runtime JS. Fallback: jika nama tidak ditemukan,
 * kembalikan string kosong — komponen <x-hi> menampilkan fallback teks.
 */
class Hi
{
    protected static ?array $icons = null;

    /** Ambil seluruh map body SVG HugeIcons (lazy, sekali load). */
    public static function all(): array
    {
        if (static::$icons === null) {
            $path = resource_path('views/admin/partials/_hugeicons.json');
            static::$icons = File::exists($path)
                ? (json_decode(File::get($path), true) ?: [])
                : [];
        }

        return static::$icons;
    }

    /** Ambil body SVG untuk nama ikon HugeIcons. */
    public static function body(string $name): string
    {
        return static::all()[$name] ?? '';
    }

    /**
     * Konversi key (nama HugeIcons ATAU kelas Font Awesome) -> nama HugeIcons.
     * Jika key sudah berupa nama HugeIcons yang dikenal, dipakai langsung.
     */
    public static function name(?string $key): string
    {
        if (!$key) {
            return '';
        }

        $key = trim($key);

        // Sudah nama HugeIcons?
        if (isset(static::all()[$key])) {
            return $key;
        }

        // Normalisasi: buang prefix style FA ("fa-solid ", "fa-regular ", "fa-brands ", "fa ")
        $normalized = preg_replace('/\bfa-(solid|regular|brands)\b/', '', $key) ?? $key;
        $normalized = trim(preg_replace('/\s+/', ' ', $normalized));

        $map = config('hugeicons', []);

        if ($normalized !== '' && isset($map[$normalized])) {
            return $map[$normalized];
        }
        if (isset($map['fa-'.$normalized])) {
            return $map['fa-'.$normalized];
        }

        // Ambil token terakhir (format "fa-solid fa-clock" -> "fa-clock")
        $parts = preg_split('/\s+/', $normalized) ?: [];
        $last = end($parts);
        if ($last !== false && $last !== '') {
            if (isset($map[$last])) {
                return $map[$last];
            }
            if (isset($map['fa-'.$last])) {
                return $map['fa-'.$last];
            }
        }

        return '';
    }
}
