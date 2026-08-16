<?php

namespace App\Http\Controllers;

use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    public function handle(Request $request)
    {
        $callbackToken = $request->header('X-CALLBACK-TOKEN');
        
        if (!$this->xenditService->verifyCallbackToken($callbackToken)) {
            Log::warning('Xendit webhook: Invalid callback token', [
                'ip' => $request->ip(),
                'token' => $callbackToken
            ]);
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $payload = $request->all();
        
        Log::info('Xendit webhook received', [
            'external_id' => $payload['external_id'] ?? null,
            'status' => $payload['status'] ?? null,
            'invoice_id' => $payload['id'] ?? null
        ]);

        $result = $this->xenditService->handleWebhookCallback($payload);

        if (!$result['success']) {
            Log::error('Xendit webhook processing failed', [
                'error' => $result['error'] ?? 'Unknown error',
                'payload' => $payload
            ]);
        }

        return response()->json(['success' => true], 200);
    }
}

