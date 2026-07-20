<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spek;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpekController extends Controller
{
    public function index()
    {
        $speks = Spek::all();

        return view('admin.speks.index', compact('speks'));
    }

    public function create()
    {
        return view('admin.speks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        $spek = Spek::create($request->all());

        return redirect()->route('speks.index')->withSuccess(__('Speks created successfully.'));
    }

    public function edit(Spek $spek)
    {
        return view('admin.speks.edit', compact('spek'));
    }

    public function update(Request $request, Spek $spek)
    {
        $spek->update($request->all());

        return redirect()->route('speks.index')->withSuccess(__('Speks created successfully.'));
    }
}
