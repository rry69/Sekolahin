<?php

namespace App\Console\Commands;

use App\Support\Provenance;
use Illuminate\Console\Command;

class VerifyBeacon extends Command
{
    protected $signature = 'beacon:verify {--file= : specific file to check}';
    protected $description = 'Verify beacon/license files';

    public function handle(): int
    {
        $file = $this->option('file');
        if ($file) {
            $pk = hex2bin((string) config('manifest.anchor'));
            $ok = Provenance::verifyFile($file, $pk ?: '');
            $this->line($file . ' : ' . ($ok ? '<info>VALID</info>' : '<error>INVALID</error>'));
            return $ok ? 0 : 1;
        }
        $files = Provenance::beaconFiles();
        if (empty($files)) {
            $this->warn('No beacon files found (' . config('manifest.beacon') . ')');
            $this->line('Licensed: ' . (Provenance::active() ? 'YES' : 'NO (local bypass: ' . (Provenance::isLocalHost() ? 'yes' : 'no') . ')'));
            return Provenance::active() ? 0 : 1;
        }
        $any = false;
        $pk = hex2bin((string) config('manifest.anchor'));
        foreach ($files as $f) {
            $ok = Provenance::verifyFile($f, $pk ?: '');
            $this->line($f . ' : ' . ($ok ? '<info>VALID</info>' : '<error>INVALID</error>'));
            if ($ok) $any = true;
        }
        $this->line('Overall licensed: ' . ($any || Provenance::isLocalHost() ? 'YES' : 'NO'));
        return ($any || Provenance::isLocalHost()) ? 0 : 1;
    }
}
