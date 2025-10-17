<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function index(Request $r)
    {
        $user   = $r->user();
        $tenant = Tenant::with('room')->where('user_id', $user->id)->first();

        if (!$tenant || !$tenant->room) {
            return view('tenant.payments.index', [
                'title'     => 'Pembayaran',
                'tenant'    => $tenant,
                'room'      => null,
                'payment'   => null,
                'amount'    => 0,
                'snapToken' => null,
                'snapUrl'   => null,
                'errorMsg'  => 'Akun belum terhubung ke kamar.',
                'clientKey' => config('midtrans.client_key'),
            ]);
        }

        $amount = (int) $tenant->room->harga;

        // Cek apakah ada request untuk payment baru (ganti metode)
        $forceNew = $r->get('new_payment') === '1';
        
        $payment = null;
        if (!$forceNew) {
            $payment = Payment::whereUserId($user->id)
                        ->whereTenantId($tenant->id)
                        ->where('status', 'pending')
                        ->latest('id')->first();
        }

        // Jika tidak ada payment atau diminta payment baru
        if (!$payment || $forceNew) {
            // Expire payment lama jika ada
            if ($forceNew) {
                Payment::whereUserId($user->id)
                    ->whereTenantId($tenant->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'expired']);
            }
            
            $orderId = 'INV-'.date('YmdHis').'-'.$user->id.'-'.Str::upper(Str::random(4));
            $payment = Payment::create([
                'order_id' => $orderId,
                'user_id'  => $user->id,
                'tenant_id'=> $tenant->id,
                'room_id'  => $tenant->room_id,
                'amount'   => $amount,
                'status'   => 'pending',
                'gateway'  => 'midtrans',
            ]);
        }

        MidtransService::boot();
        $snapToken = null;
        $snapUrl   = null;
        $errorMsg  = null;

        // Hanya buat transaksi Midtrans jika belum ada snap_token
        if (!$payment->snap_token) {
            try {
                $params = [
                    'transaction_details' => [
                        'order_id'     => $payment->order_id,
                        'gross_amount' => $payment->amount,
                    ],
                    'customer_details'    => [
                        'first_name' => $user->name,
                        'email'      => $user->email,
                        'phone'      => $tenant->phone ?? $user->phone ?? '',
                    ],
                    'enabled_payments' => ['qris', 'gopay', 'bank_transfer'],
                    'expiry' => [
                        'start_time' => now()->format('Y-m-d H:i:s O'),
                        'unit'       => 'days',
                        'duration'   => 1,
                    ],
                ];

                $snap = Snap::createTransaction($params);
                $snapToken = $snap->token ?? null;
                $snapUrl   = $snap->redirect_url ?? null;

                $payment->update([
                    'snap_token'        => $snapToken,
                    'snap_redirect_url' => $snapUrl,
                ]);

            } catch (\Throwable $e) {
                // Jika error karena order_id duplicate, coba buat payment baru
                if (str_contains($e->getMessage(), 'order_id has already been taken')) {
                    // Expire payment lama dan buat baru
                    $payment->update(['status' => 'expired']);
                    
                    $newOrderId = 'INV-'.date('YmdHis').'-'.$user->id.'-'.Str::upper(Str::random(4));
                    $payment = Payment::create([
                        'order_id' => $newOrderId,
                        'user_id'  => $user->id,
                        'tenant_id'=> $tenant->id,
                        'room_id'  => $tenant->room_id,
                        'amount'   => $amount,
                        'status'   => 'pending',
                        'gateway'  => 'midtrans',
                    ]);
                    
                    // Coba lagi dengan order_id baru
                    try {
                        $params['transaction_details']['order_id'] = $payment->order_id;
                        $snap = Snap::createTransaction($params);
                        $snapToken = $snap->token ?? null;
                        $snapUrl   = $snap->redirect_url ?? null;
                        
                        $payment->update([
                            'snap_token'        => $snapToken,
                            'snap_redirect_url' => $snapUrl,
                        ]);
                        $errorMsg = null; // Reset error jika berhasil
                    } catch (\Throwable $e2) {
                        report($e2);
                        $errorMsg = 'Gagal membuat transaksi pembayaran. Silakan coba lagi.';
                    }
                } else {
                    report($e);
                    $errorMsg = 'Gagal membuat transaksi pembayaran. Silakan coba lagi.';
                }
            }
        } else {
            // Gunakan snap_token yang sudah ada
            $snapToken = $payment->snap_token;
            $snapUrl   = $payment->snap_redirect_url;
        }

        return view('tenant.payments.index', [
            'title'     => 'Pembayaran',
            'tenant'    => $tenant,
            'room'      => $tenant->room,
            'payment'   => $payment,
            'amount'    => $amount,
            'snapToken' => $snapToken,
            'snapUrl'   => $snapUrl,
            'errorMsg'  => $errorMsg,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    public function finish()
    {
        return redirect()->route('tenant.payments.index')
            ->with('ok','Terima kasih. Cek status pembayaranmu.');
    }

    public function success(Request $r)
    {
        $orderId = $r->get('order_id');
        $statusCode = $r->get('status_code');
        $transactionStatus = $r->get('transaction_status');
        
        $user = $r->user();
        $payment = null;
        
        if ($orderId) {
            $payment = Payment::where('order_id', $orderId)
                             ->where('user_id', $user->id)
                             ->first();
        }
        
        return view('tenant.payments.success', [
            'title' => 'Pembayaran Berhasil',
            'payment' => $payment,
            'orderId' => $orderId,
            'statusCode' => $statusCode,
            'transactionStatus' => $transactionStatus,
        ]);
    }

    public function newPayment(Request $r)
    {
        return redirect()->route('tenant.payments.index', ['new_payment' => '1']);
    }
}
