<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Announcement::orderByDesc('starts_at')
                        ->orderByDesc('created_at')
                        ->paginate(12);
        return view('admin.pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'judul'      => ['required','string','max:180'],
            'isi'        => ['required','string'],
            'starts_at'  => ['nullable','date'],
            'ends_at'    => ['nullable','date','after_or_equal:starts_at'],
            'is_published' => ['nullable','boolean'],
        ]);

        $data['is_published'] = (bool)($r->boolean('is_published'));
        $data['created_by']   = $r->user()->id;

        Announcement::create($data);
        return redirect()->route('admin.pengumuman.index')->with('ok', 'Pengumuman dibuat.');
    }

    public function edit(Announcement $pengumuman)
    {
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $r, Announcement $pengumuman)
    {
        $data = $r->validate([
            'judul'      => ['required','string','max:180'],
            'isi'        => ['required','string'],
            'starts_at'  => ['nullable','date'],
            'ends_at'    => ['nullable','date','after_or_equal:starts_at'],
            'is_published' => ['nullable','boolean'],
        ]);
        $data['is_published'] = (bool)($r->boolean('is_published'));

        $pengumuman->update($data);
        return redirect()->route('admin.pengumuman.index')->with('ok', 'Pengumuman diperbarui.');
    }

    public function destroy(Announcement $pengumuman)
    {
        $pengumuman->delete();
        return redirect()->route('admin.pengumuman.index')->with('ok', 'Pengumuman dihapus.');
    }
}
