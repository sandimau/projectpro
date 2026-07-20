<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class KategoriController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('kategori_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kategoris = Kategori::all();

        return view('admin.kategoris.index', compact('kategoris'));
    }

    public function create()
    {
        abort_if(Gate::denies('kategori_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.kategoris.create');
    }

    public function store(Request $request)
    {
        $kategori = Kategori::create($request->all());

        return redirect()->route('kategori.index')->withSuccess(__('kategori created successfully.'));
    }

    public function edit(Kategori $kategori)
    {
        abort_if(Gate::denies('kategori_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.kategoris.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $kategori->update($request->all());

        return redirect()->route('kategori.index')->withSuccess(__('kategori updated successfully.'));
    }
}
