<?php
return [
    'anchor' => env('MANIFEST_ANCHOR', '5f361dc185534e20dad640187d57fe2e1daf1467d74ac36c8beb503e028dbc16'),
    'beacon' => env('MANIFEST_BEACON', '.spmb-*.lic'),
    'probe_ttl' => 300,
    'allow_local' => env('MANIFEST_ALLOW_LOCAL', true),
    'telemetry' => [
        'ping' => 60,
        'jitter' => 0.12,
        'sample' => 0.05,
    ],
    'limits' => [
        'grace' => 3600,
    ],
];
