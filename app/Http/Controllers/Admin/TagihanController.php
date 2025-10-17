<?php
// ======================================================================
// FILE: app/Http/Controllers/Admin/TagihanController.php  (NEW)
// ======================================================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TagihanController extends Controller
{
    public function index()
    {
        return view('admin.tagihan.index');
    }
}
