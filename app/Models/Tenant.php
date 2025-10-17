<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Tenant extends Model
{
    protected $fillable = [
        'user_id','room_id','phone','nik','alamat',
        'tanggal_masuk','tanggal_keluar','status','catatan',
    ];

    protected $casts = [
        'tanggal_masuk'  => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function room() { return $this->belongsTo(Room::class); }

    // Sinkron ketersediaan kamar tiap perubahan tenant
    protected static function booted()
    {
        $sync = function (self $tenant) {
            if ($tenant->room) $tenant->room->refreshAvailability();
            $old = $tenant->getOriginal('room_id');
            if ($old && $old != $tenant->room_id) {
                if ($r = Room::find($old)) $r->refreshAvailability();
            }
        };
        static::created($sync);
        static::updated($sync);
        static::deleted($sync);
    }

    /** Jatuh tempo berikutnya: tanggal_masuk setiap bulan; kalau sudah lewat bulan ini, geser ke bulan depan */
    public function nextDueDate(): ?Carbon
    {
        if (!$this->tanggal_masuk) return null;

        $start = $this->tanggal_masuk->copy();
        $today = now()->startOfDay();

        $due = Carbon::create($today->year, $today->month, min($start->day, $today->daysInMonth))->startOfDay();
        if ($due->lt($today)) {
            $n = $today->copy()->addMonth();
            $due = Carbon::create($n->year, $n->month, min($start->day, $n->daysInMonth))->startOfDay();
        }
        return $due;
    }
}
