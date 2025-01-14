<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kontak;
use App\Models\Produk;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use App\Models\ProdukKategori;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProdukModelController extends Controller
{
    protected $satuan = [
        'Pcs',
        'Box',
        'Lusin',
        'Pack',
        'Kg',
        'Gram',
        'Liter',
        'Meter',
        'Roll',
        'Unit',
        'Set',
        'Karton'
    ];

    public function index()
    {
        $currentUrl = request()->fullUrl();
        $lastNumber = preg_replace('/[^0-9]/', '', substr($currentUrl, strrpos($currentUrl, '?') + 1));
        $kategori = ProdukKategori::find($lastNumber);
        $produks = DB::table('produks')
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->where('produk_models.kategori_id', $kategori->id)
            ->get();
        return view('produkModel.index', compact('produks', 'kategori'));
    }

    public function create()
    {
        $kategori = ProdukKategori::find(request()->input('kategori_id'));
        $kontaks = Kontak::all();
        $satuan = $this->satuan;
        return view('produkModel.create', compact('kategori', 'kontaks', 'satuan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
            'satuan' => 'required',
            'deskripsi' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg',
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

        $kategori = ProdukKategori::find($request->kategori_id);

        $validated['kategori_id'] = $request->kategori_id;
        $validated['jual'] = $kategori->kategoriUtama->jual;
        $validated['beli'] = $kategori->kategoriUtama->beli;
        $validated['stok'] = $kategori->kategoriUtama->stok;
        $validated['gambar'] = $gambar;

        $produkModel = ProdukModel::create($validated);

        // Tambahkan data ke tabel produk
        Produk::create([
            'status' => 1,
            'produk_model_id' => $produkModel->id
        ]);
        return redirect()->route('produkModel.index', ['kategori_id' => $kategori->id])->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(ProdukModel $produkModel)
    {
        $kategori = ProdukKategori::find($produkModel->kategori_id);
        $kontaks = Kontak::all();
        $satuan = $this->satuan;
        return view('produkModel.edit', [
            'produkModel' => $produkModel,
            'kategori' => $kategori,
            'kontaks' => $kontaks,
            'satuan' => $satuan
        ]);
    }

    public function update(Request $request, ProdukModel $produkModel)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
            'satuan' => 'required',
            'deskripsi' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $gambar = $produkModel->gambar;
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

        $kategori = ProdukKategori::find($request->kategori_id);

        $validated['kategori_id'] = $request->kategori_id;
        $validated['jual'] = $kategori->kategoriUtama->jual;
        $validated['beli'] = $kategori->kategoriUtama->beli;
        $validated['stok'] = $kategori->kategoriUtama->stok;
        $validated['gambar'] = $gambar;
        $produkModel->update($validated);
        return redirect()->route('produkModel.index', ['kategori_id' => $kategori->id])->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(ProdukModel $produkModel)
    {
        if ($produkModel->gambar) {
            Storage::delete('public/' . $produkModel->gambar);
        }
        $produk->delete();
        return redirect()->route('produkModel.index')->with('success', 'Produk berhasil dihapus');
    }
}
