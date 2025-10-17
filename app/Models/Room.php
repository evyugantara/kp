<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode','nama','harga','deskripsi','fasilitas','tersedia','foto_path'
    ];

    protected $casts = [
        'fasilitas' => 'array',
        'tersedia'  => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(RoomImage::class)->orderBy('sort')->orderBy('id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function getCoverUrlAttribute(): ?string
    {
        if ($this->foto_path) return Storage::url($this->foto_path);
        $first = $this->images()->first();
        return $first ? $first->url : null;
    }

    /** Update field 'tersedia' sesuai ada tidaknya penghuni status AKTIF (case-insensitive). */
    public function refreshAvailability(): void
    {
        $occupied = $this->tenants()
            ->whereRaw("LOWER(TRIM(status)) = 'aktif'")
            ->exists();

        $this->updateQuietly(['tersedia' => ! $occupied]);
    }
}
