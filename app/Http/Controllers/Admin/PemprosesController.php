<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemproses;
use Illuminate\Http\Request;

class PemprosesController extends Controller
{
    public function index()
    {
        $pemproses = Pemproses::all();

        return view('admin.pemproses.index', compact('pemproses'));
    }

    public function create()
    {
        return view('admin.pemproses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|max:50',
            'warna' => 'nullable|max:10',
        ]);

        Pemproses::create([
            'nama' => $request->nama,
            'warna' => $this->normalizeWarna($request->warna),
        ]);

        return redirect()->route('pemproses.index')->withSuccess(__('Pemproses created successfully.'));
    }

    public function edit(Pemproses $pemproses)
    {
        return view('admin.pemproses.edit', compact('pemproses'));
    }

    public function update(Request $request, Pemproses $pemproses)
    {
        $request->validate([
            'nama' => 'required|max:50',
            'warna' => 'nullable|max:10',
        ]);

        $pemproses->update([
            'nama' => $request->nama,
            'warna' => $this->normalizeWarna($request->warna),
        ]);

        return redirect()->route('pemproses.index')->withSuccess(__('Pemproses updated successfully.'));
    }

    private function normalizeWarna(?string $warna): ?string
    {
        if (empty($warna)) {
            return null;
        }

        return ltrim($warna, '#');
    }
}
