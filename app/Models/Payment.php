<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id','user_id','tenant_id','room_id','amount','status',
        'payment_type','gateway','snap_token','snap_redirect_url',
        'raw_notification','paid_at',
    ];

    protected $casts = [
        'raw_notification' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function tenant(){ return $this->belongsTo(Tenant::class); }
    public function room(){ return $this->belongsTo(Room::class); }
}
