<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Akun;
use App\Models\AkunKategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class AkunController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('akun_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $akuns = Akun::all();

        return view('admin.akuns.index', compact('akuns'));
    }

    public function create()
    {
        abort_if(Gate::denies('akun_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.akuns.create');
    }

    public function store(Request $request)
    {
        $akun = Akun::create($request->all());

        return redirect()->route('akuns.index')->withSuccess(__('Akun created successfully.'));
    }

    public function edit(Akun $akun)
    {
        abort_if(Gate::denies('akun_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.akuns.edit', compact('akun'));
    }

    public function update(Request $request, Akun $akun)
    {
        $akun->update($request->all());

        return redirect()->route('akuns.index')->withSuccess(__('Akun updated successfully.'));
    }

    public function destroy(Akun $akun)
    {
        abort_if(Gate::denies('akun_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $akun->delete();
        return back();
    }
}
