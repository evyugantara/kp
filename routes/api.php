<?php

use App\Http\Controllers\Api\MidtransWebhookController;

Route::post('/payments/midtrans/notify', [MidtransWebhookController::class, 'notify']);
