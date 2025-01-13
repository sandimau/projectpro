<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sistem;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image;

class SistemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sistem_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sistems = Sistem::all();

        return view('admin.sistems.index', compact('sistems'));
    }

    public function create()
    {
        abort_if(Gate::denies('sistem_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.sistems.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'type' => 'required',
        ]);

        $sistem = Sistem::create($request->all());

        return redirect()->route('sistem.index')->withSuccess(__('Sistem created successfully.'));
    }

    public function edit()
    {
        abort_if(Gate::denies('sistem_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $sistems = Sistem::all();
        return view('admin.sistems.edit', compact('sistems'));
    }

    public function update(Request $request, Sistem $sistem)
    {
        $sistems = Sistem::all();
        $sistem = [];
        foreach ($sistems as $value) {
            if ($value->type == 'text') {
                $sistem[$value->nama] = 'required';
            }
            if ($value->type == 'file') {
                $sistem[$value->nama] = 'required|mimes:jpeg,png,jpg';
            }
        }
        $request->validate($sistem);

        foreach ($sistems as $value) {
            if ($value->type == 'file') {
                $gambar = $value->isi ?? '';

                if ($request->{$value->nama}) {
                    $img = request()->{$value->nama};
                    $filename = time() . '.' . $request->{$value->nama}->extension();
                    $img_resize = Image::make($img->getRealPath());
                    $img_resize->resize(700, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $save_path = 'uploads/'.$value->nama.'/';
                    if (!file_exists($save_path)) {
                        mkdir($save_path, 666, true);
                    }
                    $img_resize->save(public_path($save_path . $filename));
                    $gambar = $filename;
                }
                $value->update([
                    'isi' => $gambar,
                ]);
            }
            if ($value->type == 'text') {
                $value->update([
                    'isi' => $request->{$value->nama},
                ]);
            }
        }

        return redirect()->route('sistem.index')->withSuccess(__('Sistem updated successfully.'));
    }
}
