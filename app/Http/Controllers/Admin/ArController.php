<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Ar;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image;

class ArController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('ar_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ars = Ar::get();

        return view('admin.ars.index', compact('ars'));
    }

    public function create()
    {
        abort_if(Gate::denies('ar_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $ars = Ar::get();
        $members = Member::get();
        foreach ($ars as $ar) {
            foreach ($members as $member) {
                $data = [];
                if ($member->id != $ar->member_id) {
                    $data[$member->id] = $member->nama_lengkap;
                }
            }
        }

        return view('admin.ars.create', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required',
            'kode' => 'required|alpha_dash|max:7',
            'warna' => 'required',
            'ttd' => 'required|mimes:jpeg,png,jpg'
        ]);

        $gambar = null;
        if ($request->hasFile('ttd')) {
            $img = $request->file('ttd');
            $filename = time() . '.' . $request->ttd->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = 'uploads/ttd/';
            if (!file_exists($save_path)) {
                mkdir($save_path, 666, true);
            }
            $img_resize->save(public_path($save_path . $filename));
            $gambar = $filename;
        }

        Ar::create([
            'member_id' => $request->member_id,
            'kode' => $request->kode,
            'warna' => $request->warna,
            'ttd' => $gambar,
        ]);

        return redirect()->route('ars.index')->withSuccess(__('Ar created successfully.'));
    }

    public function edit(Ar $ar)
    {
        $members = Member::with('ar')->pluck('nama_lengkap', 'id')->prepend(trans('select member'), '');
        return view('admin.ars.edit', compact('ar','members'));
    }

    public function destroy(Ar $ar)
    {
        $ar->delete();

        return redirect()->route('ars.index')
            ->withSuccess(__('Ar deleted successfully.'));
    }

    public function update(Request $request, Ar $ar)
    {
        $request->validate([
            'ttd' => 'required|mimes:jpeg,png,jpg'
        ]);

        $gambar = null;
        if ($request->hasFile('ttd')) {
            $img = $request->file('ttd');
            $filename = time() . '.' . $request->ttd->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = 'uploads/ttd/';
            if (!file_exists($save_path)) {
                mkdir($save_path, 666, true);
            }
            $img_resize->save(public_path($save_path . $filename));
            $gambar = $filename;
        }

        if ($ar->ttd) {
            unlink("uploads/ttd/".$ar->ttd);
        }

        $ar->update([
            'member_id' => $request->member_id,
            'kode' => $request->kode,
            'warna' => $request->warna,
            'ttd' => $gambar,
        ]);

        return redirect()->route('ars.index')->withSuccess(__('Ar Updated successfully.'));
    }
}
