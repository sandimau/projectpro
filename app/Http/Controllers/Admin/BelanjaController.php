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

class BelanjaController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('belanja_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->dari == null && $request->sampai == null && $request->nota == null && $request->kontak_id == null) {
            $belanjas = Belanja::orderBy('id', 'desc')->paginate(10);
        } else {
            $belanjas = Belanja::query()
                ->when($request->dari && $request->sampai, function ($query) use ($request) {
                    $query->whereBetween('created_at', [$request->dari, $request->sampai]);
                })
                ->when($request->nota, function ($query) use ($request) {
                    $query->where('nota', 'LIKE', '%' . $request->nota . '%');
                })
                ->when($request->kontak_id, function ($query) use ($request) {
                    $query->where('kontak_id', $request->kontak_id);
                })
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai, 'nota' => $request->nota, 'kontak_id' => $request->kontak_id]);
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
            //insert into belanja table
            $belanja = Belanja::create([
                'nota' => $request->nota ? $request->nota : rand(1000000, 100),
                'diskon' => $request->nota ? $request->nota : 0,
                'total' => $request->total,
                'kontak_id' => $request->kontak_id,
                'akun_detail_id' => $request->akun_detail_id,
                'pembayaran' => $request->pembayaran,
                'tanggal_beli' => $request->tanggal_beli,
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
                            $total = $produk->lastStok()->where('produk_id', $produk->id)->latest('id')->first();
                            if ($total) {
                                $hpp = (($total->pivot->saldo * $produk->hpp) + ($request->harga[$item] * $request->jumlah[$item])) / ($request->jumlah[$item] + $total->pivot->saldo);
                            } else {
                                $hpp = $request->harga[$item];
                            }

                            $produk->update(['hpp' => $hpp]);

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
}
