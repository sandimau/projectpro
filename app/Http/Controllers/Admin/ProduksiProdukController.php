<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chat;
use App\Models\Hutang;
use App\Models\Kontak;
use App\Models\Member;
use App\Models\Belanja;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\ProdukStok;
use Illuminate\Http\Request;
use App\Models\ProduksiBahan;
use App\Models\ProduksiProduk;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProduksiProdukController extends Controller
{
    public function index()
    {
        $produksis = ProduksiProduk::orderBy('id', 'desc')->get();
        return view('admin.produksiProduk.index', compact('produksis'));
    }

    public function create()
    {
        return view('admin.produksiProduk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required',
            'target' => 'required',
        ]);

        ProduksiProduk::create([
            'produk_id' => $request->produk_id,
            'target' => $request->target,
            'ket' => $request->ket,
            'user_id' => auth()->user()->id,
            'status' => 'proses',
        ]);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil ditambahkan');
    }

    public function show(ProduksiProduk $produksi)
    {
        $chats = Chat::where('order_id', $produksi->id)->get();

        return view('admin.produksiProduk.show', compact('produksi', 'chats'));
    }

    public function storeChat(Request $request, ProduksiProduk $produksi)
    {
        $member = Member::where('user_id', auth()->user()->id)->first();

        Chat::create([
            'isi' => $request->isi,
            'member_id' => $member->id ?? null,
            'order_id' => $produksi->id
        ]);
        return redirect('admin/produksi/' . $produksi->id)->withSuccess(__('chat created successfully.'));
    }

    public function edit(ProduksiProduk $produksi)
    {
        return view('admin.produksiProduk.edit', compact('produksi'));
    }

    public function update(Request $request, ProduksiProduk $produksi)
    {
        $produksi->update($request->all());
        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil diupdate');
    }

    public function belanja(ProduksiProduk $produksi)
    {
        $kas = AkunDetail::where('akun_kategori_id', 1)->pluck('nama', 'id');
        return view('admin.produksiProduk.belanja', compact('produksi', 'kas'));
    }

    public function ambilBahan(ProduksiProduk $produksi)
    {
        return view('admin.produksiProduk.bahanCreate', compact('produksi'));
    }

    public function belanjaStore(Request $request, ProduksiProduk $produksi)
    {
        $request->validate([
            'kontak_id' => 'required',
            'barang_beli_id.0' => 'required',
            'jumlah.*' => 'required_with:barang_beli_id.*',
            'diskon' => 'lte:total|nullable',
            'pembayaran' => 'lte:total',
        ]);

        DB::transaction(function () use ($produksi, $request) {

            $total = $request->total;
            //insert into belanja table
            $belanja = Belanja::create([
                'nota' => $request->nota ? $request->nota : rand(1000000, 100),
                'diskon' => ($request->diskon ?? 0),
                'total' => $total,
                'kontak_id' => $request->kontak_id,
                'akun_detail_id' => $request->akun_detail_id,
                'tanggal_beli' => date("Y-m-d"),
            ]);

            foreach ($request->barang_beli_id as $item => $v) {
                if ($v != null) {

                    $belanja->belanjaDetail()->create([
                        'produk_id' => $request->barang_beli_id[$item],
                        'harga' => $request->harga[$item],
                        'jumlah' => $request->jumlah[$item],
                        'keterangan' => $request->keterangan[$item],
                    ]);

                    //belanja produksi sync
                    $belanja->produksi()->sync($produksi->id);
                }
            }

            $pembayaran = $request->pembayaran;

            // Logika pencatatan pembayaran/hutang
            if ($request->akun_detail_id && $pembayaran > 0) {
                //get supplier
                $supplier = Kontak::where('id', $request->kontak_id)->first();

                // Masukkan ke buku besar
                BukuBesar::create([
                    'akun_detail_id' => $request->akun_detail_id,
                    'kode' => 'blj',
                    'ket' => 'pembelian ke ' . $supplier->nama,
                    'debet' => 0,
                    'kredit' => $pembayaran,
                    'detail_id' => $belanja->id,
                ]);
            } else {
                // Masukkan ke hutang
                Hutang::create([
                    'kontak_id' => $request->kontak_id,
                    'tanggal' => now(),
                    'jumlah' => $total,
                    'keterangan' => 'Hutang belanja produksi',
                    'jenis' => 'belanja produksi',
                    'akun_detail_id' => $request->akun_detail_id,
                ]);
            }

            $produksi->hitungBiaya();
        });

        return redirect()->route('produksi.show', $produksi->id)
            ->with('success', 'Produk bahan berhasil ditambah');
    }

    public function ambilBahanStore(Request $request, ProduksiProduk $produksi)
    {
        $request->validate([
            'produk_id' => 'required',
            'jumlah' => 'required',
        ]);

        $model = ProduksiBahan::create([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'produksi_id' => $produksi->id,
        ]);
        $model->produksi->hitungBiaya();
        $stok = ProdukStok::create([
            'produk_id' => $model->produk_id,
            'kode' => 'bahanProduksi',
            'detail_id' => $model->produksi_id,
            'kurang' => $model->jumlah,
            'keterangan' => $model->keterangan,
        ]);

        $model->update([
            'produk_stok_id' => $stok->id,
        ]);
        return redirect()->route('produksi.show', $produksi->id)->with('success', 'Produk bahan berhasil ditambah');
    }

    public function belanjaDestroy(ProduksiProduk $produksi, Belanja $belanja)
    {
        foreach ($belanja->belanjaDetail as $detail) {
            $detail->delete();
        }
        $belanja->delete();
        $produksi->hitungBiaya();
        return redirect()->route('produksi.show', $produksi->id)->with('danger', 'Produk Belanja berhasil dihapus');
    }

    public function ambilBahanDestroy(ProduksiProduk $produksi, ProduksiBahan $bahan)
    {
        $bahan->produkStok->forceDelete();
        $bahan->delete();
        $produksi->hitungBiaya();
        return redirect()->route('produksi.show', $produksi->id)->with('danger', 'Produk bahan berhasil dihapus');
    }

    public function selesai(ProduksiProduk $produksi)
    {
        return view('admin.produksiProduk.selesai', compact('produksi'));
    }

    public function selesaiStore(Request $request, ProduksiProduk $produksi)
    {
        $request->validate([
            'hasil' => 'required',
        ]);

        $hasilTotal = $request->hasil + ($produksi->hasil ?? 0);
        $hpp = floor($produksi->biaya / $hasilTotal);

        $produksi->update([
            'status' => $hasilTotal >= $produksi->target ? 'finish' : $produksi->status,
            'hasil' => $hasilTotal,
            'hpp' => $hpp,
        ]);

        $produk = $produksi->produk;
        ///////////////////hitung hpp
        // $total = $produk->lastStok()->first()->pivot->saldo ?? 0;
        // if ($total > 0)
        //     $hpp = (($total * $produk->hpp) + ($produksi->hpp * $produksi->hasil)) / ($produksi->hasil + $total);
        // else
        //     $hpp = $produksi->hpp;
        $produk->update(['hpp' => $hpp]);

        ProdukStok::create([
            'produk_id' => $produk->id,
            'tambah' => $request->hasil,
            'kurang' => 0,
            'keterangan' => 'hasil produksi',
            'kode' => 'hasilProduksi',
            'detail_id' => $produksi->id
        ]);

        return redirect()->route('produksi.index')->with('success', 'Produksi berhasil diselesaikan');
    }
}
