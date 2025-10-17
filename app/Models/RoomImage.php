<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RoomImage extends Model
{
    // (opsional) tegaskan nama tabel jika perlu
    protected $table = 'room_images';

    // kolom yang bisa diisi via mass assignment
    protected $fillable = ['room_id', 'path', 'sort'];

    // cast angka agar rapi
    protected $casts = [
        'room_id' => 'integer',
        'sort'    => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Accessor: $image->url -> url publik file (storage:link)
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
