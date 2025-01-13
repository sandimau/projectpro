<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ar;
use App\Models\Kontak;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KontakController extends Controller
{
    public function index()
    {
        $kontaks = Kontak::all();

        return view('admin.kontaks.index', compact('kontaks'));
    }

    public function create()
    {
        $ars = Ar::get();
        return view('admin.kontaks.create', compact('ars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'noTelp' => 'required',
            'konsumen' => 'accepted',
        ]);

        Kontak::create([
            'nama' => $request->nama,
            'noTelp' => $request->noTelp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'supplier' => $request->supplier,
            'konsumen' => $request->konsumen,
            'kontak' => $request->kontak,
            'ar_id' => $request->ar_id,
        ]);

        return redirect()->route('kontaks.index')->withSuccess(__('Kontak created successfully.'));
    }

    public function edit(Kontak $kontak)
    {
        $ars = Ar::get();
        return view('admin.kontaks.edit', compact('kontak', 'ars'));
    }

    public function update(Request $request, Kontak $kontak)
    {
        $kontak->update([
            'nama' => $request->nama,
            'noTelp' => $request->noTelp,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'supplier' => $request->supplier,
            'konsumen' => $request->konsumen,
            'kontak' => $request->kontak,
            'ar_id' => $request->ar_id,
        ]);

        return redirect()->route('kontaks.index')->withSuccess(__('Kontak updated successfully.'));
    }

    public function show(Kontak $kontak)
    {
        $ars = Ar::get();
        return view('admin.kontaks.show', compact('ars', 'kontak'));
    }
}
