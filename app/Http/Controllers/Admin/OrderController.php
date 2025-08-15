<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Chat;
use App\Models\Spek;
use App\Models\Order;
use App\Models\Kontak;
use App\Models\Member;
use App\Models\Produk;
use App\Models\Sistem;
use App\Models\Produksi;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\Pembayaran;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function apiKonsumen()
    {
        $kontak = Kontak::select('nama', 'id', 'perusahaan')->where('konsumen', 1)->where('nama', 'LIKE', '%' . $_GET['q'] . '%')->get();
        return response()->json($kontak);
    }

    public function apiKontak()
    {
        $kontak = Kontak::select('nama', 'id', 'perusahaan')->where('nama', 'LIKE', '%' . $_GET['q'] . '%')->get();
        return response()->json($kontak);
    }

    public function apiSupplier()
    {
        $kontak = Kontak::select('nama', 'id')->where('supplier', 1)->where('nama', 'LIKE', '%' . $_GET['q'] . '%')->get();
        return response()->json($kontak);
    }

    public function apiProduk()
    {
        $produk = Produk::select('produk_models.nama', 'produk_models.harga', 'produks.nama as varian', 'produks.id', 'produk_kategoris.nama as kategori')
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->join('produk_kategoris', 'produk_models.kategori_id', '=', 'produk_kategoris.id')
            ->where('produk_models.jual', 1)
            ->where('produks.status', 1)
            ->where(function ($query) {
                $query->where('produks.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_models.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_kategoris.nama', 'LIKE', '%' . $_GET['q'] . '%');
            })
            ->get();
        return response()->json($produk);
    }

    public function apiProdukBeli()
    {
        $produk = Produk::select(
            'produk_models.nama',
            'produk_models.satuan',
            'produks.nama as varian',
            'produks.id',
            'produk_kategoris.nama as kategori',
            DB::raw('COALESCE((SELECT harga FROM belanja_details WHERE produk_id = produks.id ORDER BY created_at DESC LIMIT 1), produk_models.harga) as harga')
        )
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->join('produk_kategoris', 'produk_models.kategori_id', '=', 'produk_kategoris.id')
            ->where('produk_models.beli', 1)
            ->where('produks.status', 1)
            ->where(function ($query) {
                $query->where('produks.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_models.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_kategoris.nama', 'LIKE', '%' . $_GET['q'] . '%');
            })
            ->get();
        return response()->json($produk);
    }

    public function apiProdukProduksi()
    {
        $produk = Produk::select(
            'produk_models.nama',
            'produk_models.satuan',
            'produks.nama as varian',
            'produks.id',
            'produk_kategoris.nama as kategori',
            DB::raw('COALESCE((SELECT harga FROM belanja_details WHERE produk_id = produks.id ORDER BY created_at DESC LIMIT 1), produk_models.harga) as harga')
        )
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->join('produk_kategoris', 'produk_models.kategori_id', '=', 'produk_kategoris.id')
            ->where('produk_models.produksi', 1)
            ->where('produks.status', 1)
            ->where(function ($query) {
                $query->where('produks.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_models.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_kategoris.nama', 'LIKE', '%' . $_GET['q'] . '%');
            })
            ->get();
        return response()->json($produk);
    }

    public function apiProdukStok()
    {
        $produk = Produk::select(
            'produk_models.nama',
            'produk_models.satuan',
            'produks.nama as varian',
            'produks.id',
            'produk_kategoris.nama as kategori',
            DB::raw('COALESCE((SELECT harga FROM belanja_details WHERE produk_id = produks.id ORDER BY created_at DESC LIMIT 1), produk_models.harga) as harga')
        )
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->join('produk_kategoris', 'produk_models.kategori_id', '=', 'produk_kategoris.id')
            ->where('produk_models.stok', 1)
            ->where('produks.status', 1)
            ->where(function ($query) {
                $query->where('produks.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_models.nama', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('produk_kategoris.nama', 'LIKE', '%' . $_GET['q'] . '%');
            })
            ->get();
        return response()->json($produk);
    }

    public function index(Request $request)
    {
        if ($request->dari == null && $request->sampai == null && $request->nota == null && $request->kontak_id == null && $request->produk_id == null) {
            $orders = Order::whereNull('marketplace')->orderBy('id', 'desc')->paginate(10);
        } else {
            $orders = Order::query()
                ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
                ->leftJoin('kontaks', 'orders.kontak_id', '=', 'kontaks.id')
                ->when($request->dari && $request->sampai, function ($query) use ($request) {
                    $query->whereBetween('orders.created_at', [$request->dari, $request->sampai]);
                })
                ->when($request->nota, function ($query) use ($request) {
                    $query->where('orders.nota', 'LIKE', '%' . $request->nota . '%');
                })
                ->when($request->kontak_id, function ($query) use ($request) {
                    $query->where('orders.kontak_id', $request->kontak_id);
                })
                ->when($request->produk_id, function ($query) use ($request) {
                    $query->where('order_details.produk_id', $request->produk_id);
                })
                ->select('orders.*')
                ->whereNull('kontaks.marketplace')
                ->distinct()
                ->orderBy('orders.id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai, 'nota' => $request->nota, 'kontak_id' => $request->kontak_id, 'produk_id' => $request->produk_id]);
        }
        return view('admin.orders.index', compact('orders'));
    }

    public function marketplace(Request $request)
    {
        if ($request->dari == null && $request->sampai == null && $request->nota == null && $request->kontak_id == null && $request->produk_id == null) {
            $orders = Order::whereNotNull('marketplace')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $orders = Order::query()
                ->leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
                ->leftJoin('kontaks', 'orders.kontak_id', '=', 'kontaks.id')
                ->when($request->dari && $request->sampai, function ($query) use ($request) {
                    $query->whereBetween('orders.created_at', [$request->dari, $request->sampai]);
                })
                ->when($request->nota, function ($query) use ($request) {
                    $query->where('orders.nota', 'LIKE', '%' . $request->nota . '%');
                })
                ->when($request->kontak_id, function ($query) use ($request) {
                    $query->where('orders.kontak_id', $request->kontak_id);
                })
                ->when($request->produk_id, function ($query) use ($request) {
                    $query->where('order_details.produk_id', $request->produk_id);
                })
                ->select('orders.*')
                ->where('kontaks.marketplace', 1)
                ->distinct()
                ->orderBy('orders.created_at', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai, 'nota' => $request->nota, 'kontak_id' => $request->kontak_id, 'produk_id' => $request->produk_id]);
        }
        return view('admin.orders.marketplace', compact('orders'));
    }

    public function create()
    {
        $speks = Spek::all();
        return view('admin.orders.create', compact('speks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kontak_id' => 'required',
            'produk_id' => 'required',
            'harga' => 'required',
            'jumlah' => 'required',
            'deathline' => 'required',
        ]);

        $request->ongkir ? $ongkir = $request->ongkir : $ongkir = 0;

        $order['kontak_id'] = $request->kontak_id;
        $order['total'] = $request->jumlah * $request->harga;
        $order['jasa'] = $request->jasa;
        $order['keterangan'] = $request->keterangan;
        $order['ongkir'] = $ongkir;
        $order['pengiriman'] = $request->pengiriman;
        $order['invoice'] = $request->invoice;
        $order['jenis_pembayaran'] = $request->jenis_pembayaran;
        $order['ket_kirim'] = $request->ket_kirim;
        $order['deathline'] = $request->deathline;
        $order['nota'] = $request->nota;

        // ambil order flow setiap perusahaan
        $produksi = Produksi::where('grup', 'awal')->first();

        $dataOrder = Order::create($order);

        //insert order detail
        $dataDetail['order_id'] = $dataOrder->id;
        $dataDetail['produk_id'] = $request->produk_id;
        $dataDetail['tema'] = $request->tema;
        $dataDetail['jumlah'] = $request->jumlah;
        $dataDetail['harga'] = $request->harga;
        $dataDetail['keterangan'] = $request->keterangan;
        $dataDetail['produksi_id'] = $produksi->id;
        $dataDetail['deathline'] = $request->deathline;

        $produk = Produk::find($request->produk_id);
        $dataDetail['hpp'] = $produk->hpp;

        $orderDetail = OrderDetail::create($dataDetail);

        $speks = Spek::all();

        $sync = [];
        foreach ($speks as $spek) {
            if ($request->{$spek->nama}) {
                $sync[$spek->id] = ['keterangan' => $request->{$spek->nama}];
            }
        }
        $orderDetail->spek()->sync($sync);
        return redirect('/admin/order/' . $dataOrder->id . '/detail')->withSuccess(__('Order created successfully.'));
    }

    public function dashboard()
    {
        $produksi = Produksi::orderBy('urutan')->get();
        return view('admin.orders.dashboard', compact('produksi'));
    }

    public function edit(Order $order)
    {
        $speks = Spek::all();
        return view('admin.orders.edit', compact('order', 'speks'));
    }

    public function update(Request $request, Order $order)
    {
        $order->update($request->all());

        return redirect('admin/order/' . $order->id . '/detail')->withSuccess(__('Order updated successfully.'));
    }

    public function invoice($order)
    {
        $order = Order::where('id', $order)->with(['orderDetail'])->first();
        $sistems = Sistem::get()->pluck('isi', 'nama');
        $member = Member::where('user_id', auth()->user()->id)->first();
        return view('admin.orders.invoice', compact('order', 'sistems', 'member'));
    }

    public function unpaid(Request $request)
    {
        if ($request->dari == null && $request->sampai == null && $request->nota == null && $request->kontak_id == null) {
            $orders = Order::belumLunas()->orderBy('id', 'desc')->paginate(10);
        } else {
            $orders = Order::query()
                ->when($request->dari && $request->sampai, function ($query) use ($request) {
                    $query->whereBetween('created_at', [$request->dari, $request->sampai]);
                })
                ->when($request->nota, function ($query) use ($request) {
                    $query->where('nota', 'LIKE', '%' . $request->nota . '%');
                })
                ->when($request->kontak_id, function ($query) use ($request) {
                    $query->where('kontak_id', $request->kontak_id);
                })
                ->whereRaw('total > bayar')
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->appends(['dari' => $request->dari, 'sampai' => $request->sampai, 'nota' => $request->nota, 'kontak_id' => $request->kontak_id]);
        }
        return view('admin.orders.unpaid', compact('orders'));
    }

    public function bayar(Order $order)
    {
        $kas = AkunDetail::where('akun_kategori_id', 1)->pluck('nama', 'id');
        return view('admin.orders.bayar', compact('order', 'kas'));
    }

    public function storeBayar(Request $request)
    {
        $request->validate([
            'jumlah' => 'required',
            'akun_detail_id' => 'required',
            'tanggal' => 'required',
        ]);

        date_default_timezone_set("Asia/Jakarta");
        $time = date("h:i:s");
        $tanggal = request()->tanggal . ' ' . $time;

        DB::transaction(function () use ($tanggal) {
            //insert pembayarans table
            pembayaran::create([
                'akun_detail_id' => request()->akun_detail_id,
                'order_id' => request()->order_id,
                'jumlah' => request()->jumlah,
                'status' => 'approve',
                'ket' => request()->ket,
                'created_at' => $tanggal,
            ]);

            //update bayar order table
            $order = Order::where('id', request()->order_id)->first();
            $updatePembayaran = $order->bayar + request()->jumlah;
            $updateDiskon = $order->diskon + request()->diskon;
            $order->update([
                'bayar' => $updatePembayaran,
                'diskon' => $updateDiskon,
            ]);

            //insert buku besar table
            bukuBesar::create([
                'akun_detail_id' => request()->akun_detail_id,
                'ket' => 'pembayaran dari ' . $order->kontak->nama,
                'kredit' => 0,
                'kode' => 'byr',
                'debet' => request()->jumlah,
                'detail_id' => $order->id,
            ]);
        });

        return redirect('admin/order/belumLunas')->withSuccess(__('Pembayaran created successfully.'));
    }

    public function storeChat(Request $request, Order $order)
    {
        $member = Member::where('user_id', auth()->user()->id)->first();

        Chat::create([
            'isi' => $request->isi,
            'member_id' => $member->id ?? null,
            'order_id' => $order->id
        ]);
        return redirect('admin/order/' . $order->id . '/detail')->withSuccess(__('chat created successfully.'));
    }

    public function omzet()
    {
        abort_if(Gate::denies('omzet_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $orders = Order::omzetTahun()->get();
        return view('admin.orders.omzet', compact('orders'));
    }

    public function omzetBulan()
    {
        abort_if(Gate::denies('omzet_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $years = Order::omzetBulan(date('Y'))->get();
        return view('admin.orders.omzetBulan', compact('years'));
    }

    public function hapusCancel()
    {
        Order::where('total', 0)
            ->where('marketplace', 1)
            ->where('created_at', '<=', now()->subDay())
            ->delete();
        return redirect()->back()->withSuccess(__('Order canceled telah dihapus.'));
    }
}
