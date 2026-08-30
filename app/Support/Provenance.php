<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class Provenance
{
    private const FIND_MAX_DEPTH = 2;

    public static function isLocalHost(): bool
    {
        if (! config('manifest.allow_local', true)) {
            return false;
        }
        $host = request()->getHost();
        if (in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
            return true;
        }
        $ip = request()->ip();
        return in_array($ip, ['127.0.0.1', '::1'], true);
    }

    public static function active(): bool
    {
        if (self::isLocalHost()) {
            return true;
        }
        $cached = Cache::get('pv:ok');
        if ($cached !== null) {
            return (bool) $cached;
        }
        $ok = self::verifyOnce();
        try {
            Cache::put('pv:ok', $ok, (int) config('manifest.probe_ttl', 300));
        } catch (\Throwable $e) {
        }
        return $ok;
    }

    public static function verifyOnce(): bool
    {
        $pkHex = (string) config('manifest.anchor', '');
        if ($pkHex === '' || strlen($pkHex) !== 64) {
            return false;
        }
        $pk = @hex2bin($pkHex);
        if ($pk === false) {
            return false;
        }
        $files = self::beaconFiles();
        if (empty($files)) {
            return false;
        }
        foreach ($files as $path) {
            if (self::verifyFile($path, $pk)) {
                return true;
            }
        }
        return false;
    }

    public static function verifyFile(string $path, string $pk): bool
    {
        if (! is_file($path) || ! is_readable($path)) {
            return false;
        }
        $raw = @File::get($path);
        if ($raw === false || $raw === '') {
            return false;
        }
        $data = json_decode(trim($raw), true);
        if (! is_array($data) || empty($data['p']) || empty($data['s'])) {
            return false;
        }
        $payloadB64 = $data['p'];
        $sigB64 = $data['s'];
        $payload = base64_decode($payloadB64, true);
        $sig = base64_decode($sigB64, true);
        if ($payload === false || $sig === false) {
            return false;
        }
        if (! sodium_crypto_sign_verify_detached($sig, $payload, $pk)) {
            return false;
        }
        $inner = json_decode($payload, true);
        if (! is_array($inner)) {
            return false;
        }
        if (! empty($inner['exp']) && is_numeric($inner['exp']) && time() > (int) $inner['exp']) {
            return false;
        }
        if (! empty($inner['dom']) && $inner['dom'] !== '*') {
            $host = mb_strtolower(request()->getHost());
            $dom = mb_strtolower(trim($inner['dom']));
            if ($dom !== '' && $dom !== $host) {
                $suffix = '.' . ltrim($dom, '.');
                if (! str_ends_with('.' . $host, $suffix) && $host !== $dom) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function beaconFiles(): array
    {
        $pattern = (string) config('manifest.beacon', '.spmb-*.lic');
        $root = base_path();
        $out = [];
        foreach (glob($root . DIRECTORY_SEPARATOR . $pattern) ?: [] as $f) {
            $out[] = $f;
        }
        if (count($out) < 1) {
            for ($d = 1; $d <= self::FIND_MAX_DEPTH; $d++) {
                foreach (glob($root . str_repeat(DIRECTORY_SEPARATOR . '*', $d) . DIRECTORY_SEPARATOR . $pattern) ?: [] as $f) {
                    $out[] = $f;
                }
                if (! empty($out)) break;
            }
        }
        return array_values(array_unique($out));
    }

    public static function shouldBlur(): bool
    {
        return ! self::active();
    }

    public static function statusForView(): array
    {
        $licensed = self::active();
        return [
            'licensed' => $licensed,
            'blur' => ! $licensed,
            'local' => self::isLocalHost(),
        ];
    }

    public static function forgetCache(): void
    {
        try { Cache::forget('pv:ok'); } catch (\Throwable $e) {}
    }

    public static function canUse(string $feature): bool
    {
        return self::active();
    }

    public static function enforce(string $feature): void
    {
        if (self::canUse($feature)) return;
        abort(403, 'Fitur ini memerlukan aktivasi.');
    }
}
