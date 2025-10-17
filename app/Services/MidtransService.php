<?php

namespace App\Services;

use Midtrans\Config as MidConfig;

class MidtransService
{
    public static function boot(): void
    {
        // TRIM penting untuk menghapus spasi / karakter tak terlihat
        MidConfig::$serverKey    = trim((string) config('midtrans.server_key'));
        MidConfig::$isProduction = (bool) config('midtrans.is_production');
        MidConfig::$isSanitized  = true;
        MidConfig::$is3ds        = false;
    }
}
