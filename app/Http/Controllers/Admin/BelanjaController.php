<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Hutang;
use App\Models\Kontak;
use App\Models\Produk;
use App\Models\Belanja;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\ProdukStok;
use Illuminate\Http\Request;
use App\Models\BelanjaDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Intervention\Image\Facades\Image;

class BelanjaController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('belanja_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->dari == null && $request->sampai == null && $request->nota == null && $request->kontak_id == null && $request->produk_id == null) {
            $belanjas = Belanja::orderBy('id', 'desc')->paginate(10);
        } else {
            $belanjas = Belanja::query()
                ->when($request->dari && $request->sampai, function ($query) use ($request) {
                    $query->whereBetween('belanjas.created_at', [$request->dari, $request->sampai]);
                })
                ->when($request->nota, function ($query) use ($request) {
                    $query->where('belanjas.nota', 'LIKE', '%' . $request->nota . '%');
                })
                ->when($request->kontak_id, function ($query) use ($request) {
                    $query->where('belanjas.kontak_id', $request->kontak_id);
                })
                ->when($request->produk_id, function ($query) use ($request) {
                    $query->whereHas('belanjaDetail', function ($query) use ($request) {
                        $query->where('produk_id', $request->produk_id);
                    });
                })
                ->orderBy('belanjas.id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai, 'nota' => $request->nota, 'kontak_id' => $request->kontak_id, 'produk_id' => $request->produk_id]);
        }

        return view('admin.belanjas.index', compact('belanjas'));
    }

    public function create()
    {
        abort_if(Gate::denies('belanja_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $kas = AkunDetail::where('akun_kategori_id', 1)->pluck('nama', 'id');
        return view('admin.belanjas.create', compact('kas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kontak_id' => 'required',
            'tanggal_beli' => 'required',
        ]);


        DB::transaction(function () use ($request) {

            if ($request->hasFile('gambar')) {
                $img = $request->file('gambar');
                $filename = time() . '.' . $request->gambar->extension();
                $img_resize = Image::make($img->getRealPath());
                $img_resize->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $save_path = public_path('uploads/belanja/');
                if (!file_exists($save_path)) {
                    try {
                        mkdir($save_path, 0755, true);
                    } catch (\Exception $e) {
                        throw new \Exception('Unable to create directory. Please check folder permissions.');
                    }
                }
                $img_resize->save($save_path . $filename);
                $gambar = $filename;
            }
            //insert into belanja table
            $belanja = Belanja::create([
                'nota' => $request->nota ? $request->nota : rand(1000000, 100),
                'diskon' => $request->nota ? $request->nota : 0,
                'total' => $request->total,
                'kontak_id' => $request->kontak_id,
                'akun_detail_id' => $request->akun_detail_id,
                'pembayaran' => $request->pembayaran,
                'tanggal_beli' => $request->tanggal_beli,
                'gambar' => $gambar ?? null,
            ]);

            if ($request->pembayaran > 0 && $request->pembayaran <= $request->total) {
                //get supplier
                $supplier = Kontak::where('id', $request->kontak_id)->first();

                if ($request->akun_detail_id) {
                    //insert into buku besar table
                    BukuBesar::create([
                        'akun_detail_id' => $request->akun_detail_id,
                        'ket' => 'pembelian ke ' . $supplier->nama,
                        'kredit' => $request->pembayaran,
                        'debet' => 0,
                        'kode' => 'blj',
                        'detail_id' => $belanja->id,
                    ]);
                }
            } else {
                $supplier = Kontak::where('id', $request->kontak_id)->first();
                Hutang::create([
                    'kontak_id' => $request->kontak_id,
                    'tanggal' => $request->tanggal_beli,
                    'jumlah' => $request->total,
                    'keterangan' => 'pembelian ke ' . $supplier->nama,
                    'jenis' => 'belanja',
                    'detail_id' => $belanja->id,
                ]);
            }

            if (count($request->barang_beli_id) > 0) {
                //insert belanja details
                foreach ($request->barang_beli_id as $item => $v) {
                    if ($v != null) {
                        //insert belanja detail
                        BelanjaDetail::create([
                            'belanja_id' => $belanja->id,
                            'produk_id' => $request->barang_beli_id[$item],
                            'harga' => $request->harga[$item],
                            'jumlah' => $request->jumlah[$item],
                            'keterangan' => $request->keterangan[$item],
                        ]);

                        $produk = Produk::find($request->barang_beli_id[$item]);
                        $produk->update([
                            'harga' => $request->harga[$item],
                        ]);

                        if ($produk->produkModel->stok == 1) {
                            $produk->updateHpp($request->harga[$item], $request->jumlah[$item]);

                            ProdukStok::create([
                                'produk_id' => $request->barang_beli_id[$item],
                                'tambah' => $request->jumlah[$item],
                                'kurang' => 0,
                                'keterangan' => 'belanja nota:' . $belanja->nota,
                                'kode' => 'blj',
                                'user_id' => auth()->user()->id,
                                'detail_id' => $belanja->id,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('belanja.index')->withSuccess(__('Belanja created successfully.'));
    }

    public function detail($belanja)
    {
        abort_if(Gate::denies('belanja_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $belanjaDetail = BelanjaDetail::where('belanja_id', $belanja)->get();
        $belanja = Belanja::find($belanja);

        return view('admin.belanjas.detail', compact('belanjaDetail', 'belanja'));
    }

    public function destroy($belanja)
    {
        $belanja = Belanja::findOrFail($belanja);

        DB::transaction(function () use ($belanja) {
            // Reverse BukuBesar jika ada pembayaran
            if ($belanja->pembayaran > 0 && $belanja->pembayaran <= $belanja->total && $belanja->akun_detail_id) {
                // Hapus atau reverse entry BukuBesar
                $bukuBesar = BukuBesar::where('kode', 'blj')
                    ->where('detail_id', $belanja->id)
                    ->where('akun_detail_id', $belanja->akun_detail_id)
                    ->first();

                if ($bukuBesar) {
                    // Buat entry reversal untuk balance kredit sebelumnya
                    BukuBesar::create([
                        'akun_detail_id' => $belanja->akun_detail_id,
                        'ket' => 'pembatalan pembelian nota: ' . $belanja->nota,
                        'debet' => $belanja->pembayaran,
                        'kredit' => 0,
                        'kode' => 'batal',
                        'detail_id' => $belanja->id,
                    ]);
                }
            }

            // Hapus Hutang jika tidak bayar atau bayar sebagian
            if ($belanja->pembayaran < $belanja->total) {
                // Gunakan kriteria yang sama seperti relasiHutang di model Belanja
                $hutang = Hutang::where('detail_id', $belanja->id)
                    ->first();

                if ($hutang) {
                    $hutang->delete();
                }
            }

            // Reverse ProdukStok untuk setiap detail belanja
            $belanjaDetails = BelanjaDetail::where('belanja_id', $belanja->id)->get();

            foreach ($belanjaDetails as $detail) {
                $produk = Produk::find($detail->produk_id);

                if ($produk && $produk->produkModel->stok == 1) {
                    // Buat reversal entry ProdukStok (kurang = jumlah yang ditambah sebelumnya)
                    // Stok akan berkurang, tetapi HPP tetap karena adalah weighted average historis
                    ProdukStok::create([
                        'produk_id' => $detail->produk_id,
                        'tambah' => 0,
                        'kurang' => $detail->jumlah,
                        'keterangan' => 'pembatalan belanja nota: ' . $belanja->nota,
                        'kode' => 'batal',
                        'user_id' => auth()->user()->id,
                        'detail_id' => $belanja->id,
                    ]);
                }
            }

            // Hapus BelanjaDetail
            BelanjaDetail::where('belanja_id', $belanja->id)->delete();

            // Hapus gambar jika ada
            if ($belanja->gambar) {
                $gambar_path = public_path('uploads/belanja/' . $belanja->gambar);
                if (file_exists($gambar_path)) {
                    unlink($gambar_path);
                }
            }

            // Hapus Belanja
            $belanja->delete();
        });

        return redirect()->route('belanja.index')->withSuccess(__('Belanja deleted successfully.'));
    }

}
