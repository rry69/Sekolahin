<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class IssueBeacon extends Command
{
    protected $signature = 'beacon:issue {--domain=* : domain allowed, use * for any} {--exp= : unix timestamp or YYYY-MM-DD} {--out= : output file path} {--kid= : key id label}';
    protected $description = 'Issue a signed beacon file (.spmb-*.lic) — private key never stored in repo';

    public function handle(): int
    {
        $seedHex = env('BEACON_SIGN_SEED');
        if (! $seedHex || strlen(trim($seedHex)) !== 64) {
            $this->error('Set BEACON_SIGN_SEED (64 hex chars = 32 bytes Ed25519 seed) in .env on the issuer machine only. Never commit it.');
            return 1;
        }
        $seed = @hex2bin(trim($seedHex));
        if ($seed === false || strlen($seed) !== 32) {
            $this->error('BEACON_SIGN_SEED must be 64 hex chars.');
            return 1;
        }
        $kp = sodium_crypto_sign_seed_keypair($seed);
        $sk = sodium_crypto_sign_secretkey($kp);

        $domains = (array) $this->option('domain');
        $domains = array_values(array_filter(array_map('trim', $domains)));
        $dom = '*';
        if (! empty($domains)) {
            $dom = count($domains) === 1 ? mb_strtolower($domains[0]) : '*';
        }

        $exp = null;
        $rawExp = $this->option('exp');
        if ($rawExp !== null && $rawExp !== '') {
            if (is_numeric($rawExp)) {
                $exp = (int) $rawExp;
            } else {
                $ts = strtotime((string) $rawExp);
                if ($ts === false) {
                    $this->error('Invalid --exp value. Use unix timestamp or YYYY-MM-DD.');
                    return 1;
                }
                $exp = $ts;
            }
        }

        $payload = [
            'v' => 1,
            'dom' => $dom,
            'iat' => time(),
        ];
        if ($exp !== null) $payload['exp'] = $exp;
        $kid = $this->option('kid');
        if ($kid) $payload['kid'] = (string) $kid;
        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $sig = sodium_crypto_sign_detached($payloadJson, $sk);

        $envelope = [
            'p' => base64_encode($payloadJson),
            's' => base64_encode($sig),
        ];
        $out = $this->option('out');
        if (! $out) {
            $out = base_path('.spmb-' . substr(md5($payloadJson . microtime(true)), 0, 8) . '.lic');
        }
        File::put($out, json_encode($envelope, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL);
        $this->info('Beacon issued: ' . $out);
        $this->line('Payload: ' . $payloadJson);
        $this->line('Domain: ' . $dom . ($exp ? '  Exp: ' . date('Y-m-d H:i:s', $exp) : '  (lifetime)'));
        return 0;
    }
}
