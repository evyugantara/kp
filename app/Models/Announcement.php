<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    protected $fillable = [
        'judul','isi','starts_at','ends_at','is_published','created_by'
    ];

    protected $casts = [
        'starts_at'    => 'datetime',
        'ends_at'      => 'datetime',
        'is_published' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Aktif = published dan berada dalam rentang waktu tayang (atau tanpa rentang) */
    public function scopeActive(Builder $q): Builder
    {
        $now = now(); // akan mengikuti timezone app
        return $q->where('is_published', true)
                 ->where(function ($qq) use ($now) {
                     $qq->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', $now);
                 })
                 ->where(function ($qq) use ($now) {
                     $qq->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', $now);
                 });
    }

    /** Helper ringkas untuk ditampilkan di UI */
    public function jadwalSingkat(): string
    {
        $s = $this->starts_at ? $this->starts_at->translatedFormat('d M Y H:i') : 'segera';
        $e = $this->ends_at   ? $this->ends_at->translatedFormat('d M Y H:i')   : 'tanpa batas';
        return "{$s} — {$e}";
    }
}
