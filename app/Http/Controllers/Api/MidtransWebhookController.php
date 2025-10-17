<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function notify(Request $request)
    {
        try {
            $body = $request->all();
            
            // Log incoming notification
            \Log::info('Midtrans Callback', $body);

            // verifikasi signature
            $serverKey = config('midtrans.server_key');
            $calcSig = hash('sha512',
                ($body['order_id'] ?? '').
                ($body['status_code'] ?? '').
                ($body['gross_amount'] ?? '').
                $serverKey
            );

            if (!isset($body['signature_key']) || $calcSig !== $body['signature_key']) {
                \Log::warning('Midtrans Invalid Signature', [
                    'order_id' => $body['order_id'] ?? 'N/A',
                    'provided' => $body['signature_key'] ?? 'N/A',
                    'calculated' => $calcSig
                ]);
                return response()->json(['message'=>'Invalid signature'], 403);
            }

            $payment = Payment::where('order_id', $body['order_id'] ?? '')->first();
            if (!$payment) {
                \Log::warning('Midtrans Payment Not Found', ['order_id' => $body['order_id'] ?? 'N/A']);
                return response()->json(['message'=>'Order not found'], 404);
            }

            $midStatus = $body['transaction_status'] ?? 'pending';
            $oldStatus = $payment->status;
            
            $status = match ($midStatus) {
                'capture', 'settlement' => 'paid',
                'pending'                => 'pending',
                'deny', 'cancel'         => 'cancel',
                'expire'                 => 'expire',
                default                  => 'failed',
            };

            $payment->update([
                'payment_type'     => $body['payment_type'] ?? $payment->payment_type,
                'status'           => $status,
                'raw_notification' => $body,
                'raw_callback'     => $body, // for audit trail
                'paid_at'          => in_array($status, ['paid']) ? now() : $payment->paid_at,
            ]);
            
            \Log::info('Midtrans Payment Updated', [
                'order_id' => $payment->order_id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'amount' => $payment->amount
            ]);

            return response()->json(['ok' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Midtrans Callback Error', [
                'error' => $e->getMessage(),
                'body' => $request->all()
            ]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
