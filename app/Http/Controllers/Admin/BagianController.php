<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBagianRequest;
use App\Http\Requests\StoreBagianRequest;
use App\Http\Requests\UpdateBagianRequest;
use App\Models\Bagian;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BagianController extends Controller
{
    public function index()
    {
        $bagians = Bagian::all();

        return view('admin.bagians.index', compact('bagians'));
    }

    public function create()
    {
        return view('admin.bagians.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'grade' => 'required',
        ]);

        $bagian = Bagian::create($request->all());

        return redirect()->route('bagian.index')->withSuccess(__('Bagian created successfully.'));
    }

    public function edit(Bagian $bagian)
    {
        return view('admin.bagians.edit', compact('bagian'));
    }

    public function update(Request $request, Bagian $bagian)
    {
        $bagian->update($request->all());

        return redirect()->route('bagian.index')->withSuccess(__('Bagian Updated successfully.'));
    }
}
