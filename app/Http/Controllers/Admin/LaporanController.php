<?php

namespace App\Http\Controllers\Admin;

use App\Models\AkunDetail;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    public function neraca()
    {
        $kas = AkunDetail::TotalKas();

        $modal = AkunDetail::modal()->first();
        $modal_saldo = $modal->saldo ?? 0;

        return view('admin.laporan.neraca', compact('kas', 'modal_saldo'));
    }
}
