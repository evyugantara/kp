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

        $payment = Payment::whereUserId($user->id)
                    ->whereTenantId($tenant->id)
                    ->where('status', 'pending')
                    ->latest('id')->first();

        if (!$payment) {
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

        try {
            $params = [
                'transaction_details' => [
                    'order_id'     => $payment->order_id,
                    'gross_amount' => $payment->amount,
                ],
                'customer_details'    => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                    'phone'      => $tenant->phone,
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
            report($e);
            $errorMsg  = 'Gagal membuat transaksi Midtrans: '.$e->getMessage();
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
}
