<?php

namespace App\Http\Controllers\Admin;

use App\Models\AkunDetail;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    public function neraca()
    {
        $kas = AkunDetail::TotalKas();
        $modal = AkunDetail::modal();

        return view('admin.laporan.neraca', compact('kas', 'modal'));
    }
}
