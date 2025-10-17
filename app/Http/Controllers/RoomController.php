<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * ====== HALAMAN PUBLIK
     * GET /
     */
    public function publicIndex()
    {
        // daftar kamar untuk landing page
        $rooms = Room::with(['images' => function ($q) {
                $q->orderBy('sort')->orderBy('id');
            }])
            // urutkan yang tersedia dulu lalu harga termurah
            ->when(
                SchemaHas::column('rooms', 'tersedia'),
                fn ($q) => $q->orderByDesc('tersedia')
            )
            ->orderBy('harga')
            ->paginate(12);

        return view('rooms.public-index', compact('rooms'));
    }

    /**
     * GET /kamar/{room:kode}
     * Detail kamar publik
     */
    public function publicShow(Room $room)
    {
        $room->load(['images' => fn ($q) => $q->orderBy('sort')->orderBy('id')]);
        return view('rooms.public-show', compact('room'));
    }

    /**
     * ====== ADMIN: LIST
     * GET /admin/rooms
     */
    public function index()
    {
        $rooms = Room::withCount('images')
            ->orderBy('kode')
            ->paginate(20);

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * GET /admin/rooms/create
     */
    public function create()
    {
        $room = new Room();
        return view('admin.rooms.form', compact('room'));
    }

    /**
     * POST /admin/rooms
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'kode'      => ['required','alpha_dash','max:50','unique:rooms,kode'],
            'nama'      => ['required','string','max:190'],
            'harga'     => ['required','integer','min:0'],
            'deskripsi' => ['nullable','string'],
            'status'    => ['required', Rule::in(['Tersedia','Tidak'])],
            'cover'     => ['nullable','image','max:6144'],
            'gallery.*' => ['nullable','image','max:6144'],
        ]);

        DB::transaction(function () use ($r, &$data) {

            // map status -> tersedia (boolean)
            $data['tersedia'] = ($data['status'] === 'Tersedia');

            // simpan cover jika ada
            $coverPath = null;
            if ($r->hasFile('cover')) {
                $coverPath = $r->file('cover')->store('rooms/covers', 'public');
                $data['cover_path'] = $coverPath;
            }

            /** @var Room $room */
            $room = Room::create($data);

            // simpan galeri jika ada
            if ($r->hasFile('gallery')) {
                foreach ($r->file('gallery') as $i => $file) {
                    $path = $file->store('rooms/gallery', 'public');
                    RoomImage::create([
                        'room_id' => $room->id,
                        'path'    => $path,
                        'sort'    => $i,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('ok', 'Kamar berhasil ditambahkan.');
    }

    /**
     * GET /admin/rooms/{room}/edit
     */
    public function edit(Room $room)
    {
        $room->load(['images' => fn ($q) => $q->orderBy('sort')->orderBy('id')]);
        return view('admin.rooms.form', compact('room'));
    }

    /**
     * PUT /admin/rooms/{room}
     */
    public function update(Request $r, Room $room)
    {
        $data = $r->validate([
            'kode'      => ['required','alpha_dash','max:50', Rule::unique('rooms','kode')->ignore($room->id)],
            'nama'      => ['required','string','max:190'],
            'harga'     => ['required','integer','min:0'],
            'deskripsi' => ['nullable','string'],
            'status'    => ['required', Rule::in(['Tersedia','Tidak'])],
            'cover'     => ['nullable','image','max:6144'],
            'gallery.*' => ['nullable','image','max:6144'],
        ]);

        DB::transaction(function () use ($r, $room, &$data) {
            $data['tersedia'] = ($data['status'] === 'Tersedia');

            // cover baru?
            if ($r->hasFile('cover')) {
                if (!empty($room->cover_path)) {
                    Storage::disk('public')->delete($room->cover_path);
                }
                $data['cover_path'] = $r->file('cover')->store('rooms/covers', 'public');
            }

            $room->update($data);

            // tambah galeri baru (yang lama tetap, hapusnya via route deleteImage)
            if ($r->hasFile('gallery')) {
                // lanjutkan sort dari terbesar sekarang
                $lastSort = (int) $room->images()->max('sort');
                foreach ($r->file('gallery') as $offset => $file) {
                    $path = $file->store('rooms/gallery', 'public');
                    RoomImage::create([
                        'room_id' => $room->id,
                        'path'    => $path,
                        'sort'    => $lastSort + 1 + $offset,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.rooms.edit', $room)
            ->with('ok', 'Perubahan kamar berhasil disimpan.');
    }

    /**
     * DELETE /admin/rooms/{room}
     */
    public function destroy(Room $room)
    {
        DB::transaction(function () use ($room) {
            // hapus semua foto
            foreach ($room->images as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
            // hapus cover
            if (!empty($room->cover_path)) {
                Storage::disk('public')->delete($room->cover_path);
            }
            $room->delete();
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('ok', 'Kamar dihapus.');
    }

    /**
     * DELETE /admin/rooms/{room}/images/{image}
     * Hapus satu foto galeri.
     */
    public function deleteImage(Room $room, RoomImage $image)
    {
        abort_unless($image->room_id === $room->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('ok', 'Foto dihapus.');
    }
}

/**
 * Helper kecil untuk cek kolom ada/tidak tanpa error
 */
class SchemaHas
{
    public static function column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
    