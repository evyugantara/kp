<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'bill_id','order_id','user_id','tenant_id','room_id','amount','status',
        'method','payment_type','gateway','external_id','va_number','qris_ref',
        'snap_token','snap_redirect_url','raw_notification','raw_callback','paid_at',
    ];

    protected $casts = [
        'raw_notification' => 'array',
        'paid_at' => 'datetime',
    ];

    public function bill(){ return $this->belongsTo(Bill::class); }
    public function user(){ return $this->belongsTo(User::class); }
    public function tenant(){ return $this->belongsTo(Tenant::class); }
    public function room(){ return $this->belongsTo(Room::class); }
}
