<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyAkunKategoriRequest;
use App\Http\Requests\StoreAkunKategoriRequest;
use App\Http\Requests\UpdateAkunKategoriRequest;
use App\Models\Akun;
use App\Models\AkunKategori;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AkunKategoriController extends Controller
{
    public function index()
    {
        $akunKategoris = AkunKategori::with(['akun'])->get();

        return view('admin.akunKategoris.index', compact('akunKategoris'));
    }

    public function create()
    {
        $akuns = Akun::pluck('nama', 'id')->prepend(trans('select akun'), '');

        return view('admin.akunKategoris.create', compact('akuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'akun_id' => 'required',
        ]);

        $akunKategori = AkunKategori::create($request->all());

        return redirect()->route('akunKategoris.index')->withSuccess(__('Akun Kategori created successfully.'));
    }

    public function edit(AkunKategori $akunKategori)
    {
        $akuns = Akun::pluck('nama', 'id')->prepend(trans('select akun'), '');

        $akunKategori->load('akun');

        return view('admin.akunKategoris.edit', compact('akunKategori', 'akuns'));
    }

    public function update(Request $request, AkunKategori $akunKategori)
    {
        $akunKategori->update($request->all());

        return redirect()->route('akunKategoris.index')->withSuccess(__('Akun Kategori updated successfully.'));
    }

    public function destroy(AkunKategori $akunKategori)
    {
        $akunKategori->delete();

        return back();
    }
}
