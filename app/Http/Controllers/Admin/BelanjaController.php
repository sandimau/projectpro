<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AkunDetail;
use App\Models\Belanja;
use App\Models\BelanjaDetail;
use App\Models\BukuBesar;
use App\Models\Kontak;
use App\Models\Produk;
use App\Models\ProdukStok;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BelanjaController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('belanja_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $belanjas = Belanja::orderBy('id','desc')->paginate(12);

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
            'pembayaran' => 'equal:total|required',
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
                            'harga_beli' => $request->harga[$item],
                        ]);
                        if ($produk->stok == 1) {
                            ProdukStok::create([
                                'tanggal' => Carbon::now(),
                                'produk_id' => $request->barang_beli_id[$item],
                                'tambah' => $request->jumlah[$item],
                                'kurang' => 0,
                                'keterangan' => $request->keterangan[$item],
                                'kode' => 'blj',
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

        $belanjaDetail = BelanjaDetail::where('belanja_id',$belanja)->get();
        $belanja = Belanja::find($belanja);

        return view('admin.belanjas.detail', compact('belanjaDetail','belanja'));
    }
}
