<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function notify(Request $request)
    {
        $body = $request->all();

        // verifikasi signature
        $serverKey = config('midtrans.server_key');
        $calcSig = hash('sha512',
            ($body['order_id'] ?? '').
            ($body['status_code'] ?? '').
            ($body['gross_amount'] ?? '').
            $serverKey
        );

        if (!isset($body['signature_key']) || $calcSig !== $body['signature_key']) {
            return response()->json(['message'=>'Invalid signature'], 403);
        }

        $payment = Payment::where('order_id', $body['order_id'] ?? '')->first();
        if (!$payment) {
            return response()->json(['message'=>'Order not found'], 404);
        }

        $midStatus = $body['transaction_status'] ?? 'pending';
        $status = match ($midStatus) {
            'capture', 'settlement' => 'paid',
            'pending'                => 'pending',
            'deny', 'cancel'         => 'cancel',
            'expire'                 => 'expire',
            default                  => 'failed',
        };

        $payment->update([
            'payment_type'    => $body['payment_type'] ?? $payment->payment_type,
            'status'          => $status,
            'raw_notification'=> $body,
            'paid_at'         => in_array($status, ['paid']) ? now() : $payment->paid_at,
        ]);

        return response()->json(['ok' => true]);
    }
}
