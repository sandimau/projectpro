<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProceRequest;
use App\Http\Requests\StoreProceRequest;
use App\Http\Requests\UpdateProceRequest;
use App\Models\Proce;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProsesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('proce_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $proces = Proce::all();

        return view('admin.proces.index', compact('proces'));
    }

    public function create()
    {
        abort_if(Gate::denies('proce_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.proces.create');
    }

    public function store(StoreProceRequest $request)
    {
        $proce = Proce::create($request->all());

        return redirect()->route('admin.proces.index');
    }

    public function edit(Proce $proce)
    {
        abort_if(Gate::denies('proce_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.proces.edit', compact('proce'));
    }

    public function update(UpdateProceRequest $request, Proce $proce)
    {
        $proce->update($request->all());

        return redirect()->route('admin.proces.index');
    }

    public function show(Proce $proce)
    {
        abort_if(Gate::denies('proce_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.proces.show', compact('proce'));
    }

    public function destroy(Proce $proce)
    {
        abort_if(Gate::denies('proce_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $proce->delete();

        return back();
    }

    public function massDestroy(MassDestroyProceRequest $request)
    {
        $proces = Proce::find(request('ids'));

        foreach ($proces as $proce) {
            $proce->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
