<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Chat;
use App\Models\Spek;
use App\Models\Order;
use App\Models\Member;
use App\Models\Produk;
use App\Models\Produksi;
use App\Models\ProdukStok;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

class OrderDetailController extends Controller
{
    public function index(Order $order)
    {
        abort_if(Gate::denies('order_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $orderDetails = OrderDetail::where('order_id', $order->id)->get();
        $produksi = Produksi::orderBy('urutan')->get();
        $chats = Chat::where('order_id',$order->id)->get();

        return view('admin.orderDetails.index', compact('orderDetails', 'order', 'produksi','chats'));
    }

    public function create(Order $order)
    {
        abort_if(Gate::denies('order_detail_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $speks = Spek::all();
        return view('admin.orderDetails.create', compact('order', 'speks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required',
            'harga' => 'required',
            'jumlah' => 'required',
            'deathline' => 'required',
        ]);

        $produksi = Produksi::where('nama', 'persiapan')->first();

        //insert project detail
        $dataDetail['order_id'] = $request->order_id;
        $dataDetail['produk_id'] = $request->produk_id;
        $dataDetail['tema'] = $request->tema;
        $dataDetail['jumlah'] = $request->jumlah;
        $dataDetail['harga'] = $request->harga;
        $dataDetail['keterangan'] = $request->keterangan;
        $dataDetail['produksi_id'] = $produksi->id;
        $dataDetail['deathline'] = $request->deathline;
        $dataDetail['created_at'] = Carbon::now();

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
        return redirect('/admin/order/' . $request->order_id . '/detail')->withSuccess(__('Order Detail created successfully.'));
    }

    public function gambar(OrderDetail $detail)
    {
        abort_if(Gate::denies('order_detail_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.orderDetails.gambar', compact('detail'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'gambar' => 'required|mimes:jpeg,png,jpg',
        ]);

        $orderDetail = OrderDetail::find($request->order_detail_id);
        $gambar = null;
        if ($request->hasFile('gambar')) {
            $img = $request->file('gambar');
            $filename = time() . '.' . $request->gambar->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = public_path('uploads/order/');
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

        $orderDetail->update([
            'gambar' => $gambar,
        ]);

        return redirect('/admin/order/' . $orderDetail->order->id . '/detail')->withSuccess(__('Gambar detail updated successfully.'));
    }

    public function updateStatus(Request $request, OrderDetail $detail)
    {
        DB::transaction(function () use ($detail, $request) {
            //update stok produk
            if ($detail->produk->produkModel->stok == 1) {
                $awal = Produksi::find($detail->produksi_id)->grup;
                $perubahan = Produksi::find($request->produksi_id)->grup;

                if ($detail->order->username) {
                    $username = '('.$detail->order->username.')';
                } else {
                    $username = '';
                }

                if ($awal == 'awal' and $perubahan != 'awal' and $perubahan != 'batal') {
                    //ngurangi stok
                    ProdukStok::create([
                        'tambah' => 0,
                        'kurang' => $detail->jumlah,
                        'keterangan' => 'barang dijual ke ' .$detail->order->kontak->nama.' '.$username,
                        'kode' => 'jual',
                        'produk_id' => $detail->produk->id,
                    ]);
                }
                if ($awal == 'selesai' and $perubahan == 'batal') {
                    //tambah stok
                    ProdukStok::create([
                        'tambah' => $detail->jumlah,
                        'kurang' => 0,
                        'keterangan' => 'barang dikembalikan dari ' .$detail->order->kontak->nama.' '.$username,
                        'kode' => 'btl',
                        'produk_id' => $detail->produk->id,
                    ]);
                }

            }

            //update status produksi
            $detail->update([
                'produksi_id' => $request->produksi_id,
                'hpp' => $detail->produk->hpp,
            ]);
        });

        return redirect('/admin/order/' . $detail->order->id . '/detail')->withSuccess(__('Status updated successfully.'));
    }

    public function edit(OrderDetail $detail)
    {
        abort_if(Gate::denies('order_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $speks = Spek::all();
        return view('admin.orderDetails.edit', compact('detail', 'speks'));
    }

    public function update(Request $request, $detail)
    {
        $orderDetail = OrderDetail::find($detail);
        $produk = $request->produk_id ? $request->produk_id : $orderDetail->produk_id;
        $orderDetail->update([
            'produk_id' => $produk,
            'tema' => $request->tema,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
            'keterangan' => $request->keterangan,
            'deathline' => $request->deathline,
        ]);
        $speks = Spek::all();

        $sync = [];
        foreach ($speks as $spek) {
            if ($request->{$spek->nama}) {
                $sync[$spek->id] = ['keterangan' => $request->{$spek->nama}];
            }
        }
        $orderDetail->spek()->sync($sync);
        return redirect('/admin/order/' . $orderDetail->order->id . '/detail')->withSuccess(__('Order Detail updated successfully.'));
    }

    public function editGambar(OrderDetail $detail)
    {
        abort_if(Gate::denies('order_detail_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.orderDetails.editGambar', compact('detail'));
    }

    public function updateGambar(Request $request)
    {
        $request->validate([
            'gambar' => 'required|mimes:jpeg,png,jpg',
        ]);

        $orderDetail = OrderDetail::find($request->order_detail_id);
        $gambar = null;
        if ($request->hasFile('gambar')) {
            $img = $request->file('gambar');
            $filename = time() . '.' . $request->gambar->extension();
            $img_resize = Image::make($img->getRealPath());
            $img_resize->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $save_path = public_path('uploads/order/');
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

        if ($orderDetail->gambar) {
            unlink("uploads/order/" . $orderDetail->gambar);
        }

        $orderDetail->update([
            'gambar' => $gambar,
        ]);

        return redirect('/admin/order/' . $orderDetail->order->id . '/detail')->withSuccess(__('Gambar detail updated successfully.'));
    }
}
