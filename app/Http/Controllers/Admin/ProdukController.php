<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\LastStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class ProdukController extends Controller
{
    public function index(Kategori $kategori)
    {
        $produks = Produk::where('kategori_id',$kategori->id)->get();

        return view('admin.produks.index', compact('produks','kategori'));
    }

    public function create(Kategori $kategori)
    {
        $satuan = [
            'kilo' => 'kilo', 'lembar' => 'lembar', 'rim' => 'rim', 'koli' => 'koli', 'jasa' => 'jasa', 'meter' => 'meter', 'roll' => 'roll', 'gross' => 'gross', 'liter' => 'liter', 'buah' => 'buah',
            'pak(25)' => 'pak(25)', 'pak(50)' => 'pak(50)', 'pak(100)' => 'pak(100)', 'pak(120)' => 'pak(120)', 'pak(200)' => 'pak(200)', 'pak(250)' => 'pak(250)', 'pak(500)' => 'pak(500)', 'pak(1000)' => 'pak(1000)', 'pak(2000)' => 'pak(2000)',
        ];
        return view('admin.produks.create', compact('satuan','kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'gambar' => 'required|mimes:jpeg,png,jpg'
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $img = $request->file('gambar');
            $filename = time() . '.' . $request->gambar->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = 'uploads/produk/';
            if (!file_exists($save_path)) {
                mkdir($save_path, 666, true);
            }
            $img_resize->save(public_path($save_path . $filename));
            $gambar = $filename;
        }

        Produk::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'satuan' => $request->satuan,
            'deskripsi' => $request->deskripsi,
            'beli' => $request->beli,
            'jual' => $request->jual,
            'stok' => $request->stok,
            'gambar' => $gambar,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('produks.index',$request->kategori_id)->withSuccess(__('Produk created successfully.'));
    }

    public function edit(Produk $produk)
    {
        $satuan = [
            'kilo' => 'kilo', 'lembar' => 'lembar', 'rim' => 'rim', 'koli' => 'koli', 'jasa' => 'jasa', 'meter' => 'meter', 'roll' => 'roll', 'gross' => 'gross', 'liter' => 'liter', 'buah' => 'buah',
            'pak(25)' => 'pak(25)', 'pak(50)' => 'pak(50)', 'pak(100)' => 'pak(100)', 'pak(120)' => 'pak(120)', 'pak(200)' => 'pak(200)', 'pak(250)' => 'pak(250)', 'pak(500)' => 'pak(500)', 'pak(1000)' => 'pak(1000)', 'pak(2000)' => 'pak(2000)',
        ];
        return view('admin.produks.edit', compact('produk','satuan'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'required',
            'gambar' => 'required|mimes:jpeg,png,jpg'
        ]);

        $gambar = $produk->gambar;
        if ($request->hasFile('gambar')) {
            $img = $request->file('gambar');
            $filename = time() . '.' . $request->gambar->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = 'uploads/produk/';
            if (!file_exists($save_path)) {
                mkdir($save_path, 666, true);
            }
            $img_resize->save(public_path($save_path . $filename));
            $gambar = $filename;
        }

        $produk->update([
            'nama' => $request->nama,
            'harga' => $request->harga ?? 0,
            'satuan' => $request->satuan,
            'deskripsi' => $request->deskripsi,
            'beli' => $request->beli ?? 0,
            'jual' => $request->jual,
            'stok' => $request->stok,
            'gambar' => $gambar,
        ]);

        return redirect()->route('produks.index',$produk->kategori_id)->withSuccess(__('Produk updated successfully.'));
    }

    public function aset()
    {
        $asets = DB::table('produk_last_stoks as t')
            ->join(
                DB::raw('(SELECT produk_id FROM produk_last_stoks GROUP BY produk_id) as subquery'),
                't.produk_id',
                '=',
                'subquery.produk_id'
            )
            ->join('produks as p', 'p.id', '=', 't.produk_id')
            ->join('kategoris as k', 'k.id', '=', 'p.kategori_id')
            ->select('t.saldo', 'p.harga_beli','p.nama', 'k.nama as namaKategori')
            ->get();
        return view('admin.produks.aset', compact('asets'));
    }
}
