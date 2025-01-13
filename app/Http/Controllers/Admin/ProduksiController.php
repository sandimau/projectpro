<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProduksiRequest;
use App\Http\Requests\StoreProduksiRequest;
use App\Http\Requests\UpdateProduksiRequest;
use App\Models\Produksi;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProduksiController extends Controller
{
    public function index()
    {
        $produksis = Produksi::all();

        return view('admin.produksis.index', compact('produksis'));
    }

    public function create()
    {
        return view('admin.produksis.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'grup' => 'required',
        ]);

        $produksi = Produksi::create($request->all());

        return redirect()->route('produksis.index')->withSuccess(__('Produksi created successfully.'));
    }

    public function edit(Produksi $produksi)
    {
        return view('admin.produksis.edit', compact('produksi'));
    }

    public function update(Request $request, Produksi $produksi)
    {
        $produksi->update($request->all());

        return redirect()->route('produksis.index')->withSuccess(__('Produksi updated successfully.'));
    }
}
