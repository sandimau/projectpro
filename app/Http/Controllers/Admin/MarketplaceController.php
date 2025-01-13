<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Produksi;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\ProdukStok;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class MarketplaceController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('marketplace_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $marketplaces = Marketplace::with('kontak', 'kas')->get();

        return view('admin.marketplaces.index', compact('marketplaces'));
    }

    public function show(Marketplace $marketplace)
    {
        $kasMarketplace = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', 'marketplace');
            })
            ->get();
        $kasPenarikan = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', '!=', 'marketplace');
            })
            ->get();
        return view('admin.marketplaces.show', compact('marketplace', 'kasMarketplace', 'kasPenarikan'));
    }

    public function create()
    {
        abort_if(Gate::denies('marketplace_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $kasMarketplace = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', 'marketplace');
            })
            ->get();
        $kasPenarikan = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', '!=', 'marketplace');
            })
            ->get();
        return view('admin.marketplaces.create', compact('kasMarketplace', 'kasPenarikan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'marketplace' => 'required',
            'kas_id' => 'required',
            'penarikan_id' => 'required',
            'kontak_id' => 'required',
        ]);
        Marketplace::create($request->all());

        return redirect()->route('marketplaces.index')->withSuccess(__('Toko created berhasil'));
    }

    public function edit(Marketplace $marketplace)
    {
        abort_if(Gate::denies('marketplace_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $kasMarketplace = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', 'marketplace');
            })
            ->get();
        $kasPenarikan = AkunDetail::with('akun_kategori')
            ->whereHas('akun_kategori', function ($q) {
                $q->where('nama', '!=', 'marketplace');
            })
            ->get();
        return view('admin.marketplaces.edit', compact('marketplace', 'kasMarketplace', 'kasPenarikan'));
    }

    public function update(Request $request, Marketplace $marketplace)
    {
        $marketplace->update($request->all());

        return redirect()->route('marketplaces.index')->withSuccess(__('Toko updated berhasil'));
    }

    public function destroy(Marketplace $marketplace)
    {
        abort_if(Gate::denies('marketplace_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $marketplace->delete();
        return back();
    }

    public function uploadKeuangan(Request $request, Marketplace $id)
    {
        $request->validate([
            'keuangan' => 'required|mimes:csv',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $file_excel = fopen(request()->keuangan, "r");
                $i = 0;
                $config = $id;
                $marketplace = DB::table('marketplace_formats')->where('jenis', 'keuangan')->where('marketplace', $config->marketplace)->first();

                $header = $marketplace->barisHeader ?? 1;

                $keuangan = [];
                $input = false;
                if ($config->baruKeuangan == 1)
                    $input = true;
                else
                    //////ambil yg terakhir terinput
                    $terakhir = bukuBesar::where('akun_detail_id', $config->kas_id)->latest()->first();

                while (($baris = fgetcsv($file_excel, 1000, ",")) !== false) {

                    $i++;
                    array_unshift($baris, $i);

                    if ($i < $header)
                        continue;
                    else if ($i == $header) {
                        if ($baris[1] != $marketplace->kolom1 or $baris[2] != $marketplace->kolom2 or $baris[3] != $marketplace->kolom3)
                            throw new \Exception('file excel tidak sesuai dengan template');
                        continue;
                    }

                    $pattern = '/\.0$/';
                    $pattern2 = '/\.00$/';
                    $saldo = $baris[$marketplace->saldo];
                    $saldo = preg_replace($pattern, '', $saldo);
                    $saldo = preg_replace($pattern2, '', $saldo);
                    $saldo = str_replace(",", "", $saldo);
                    $saldo = $baris[$marketplace->saldo] = str_replace(".", "", $saldo);

                    $tanggal = $baris[$marketplace->tanggal];
                    $tanggal = $baris[$marketplace->tanggal] = Carbon::createFromFormat($marketplace->formatTanggal, $tanggal)->toDateTimeString();

                    $tema = $baris[$marketplace->tema];
                    $harga = $baris[$marketplace->harga];
                    $harga = preg_replace($pattern, '', $harga);
                    $harga = preg_replace($pattern2, '', $harga);
                    $harga = str_replace(",", "", $harga);
                    $harga = $baris[$marketplace->harga] = str_replace(".", "", $harga);

                    if ($i == $header + 1) {
                        /////////ambil tanggal dan saldo terakhir di excel yg diupload
                        $tanggal_terakhir = $tanggal;
                        $saldo_terakhir = $saldo;
                        $ket_terakhir = $tema;
                        $dana_terakhir = $harga;
                    }

                    ////////jika ketemu dengan tanggal terakhir yg terupload sebelumnya, start mulai input

                    if (!$input and $tanggal == $terakhir->created_at and $saldo == $terakhir->saldo) {
                        $input = true;
                        break;
                    }

                    if ($harga < 0 and strpos($baris[$marketplace->tema], $marketplace->batal) !== false) {
                        $keuangan[] = $baris;
                    }
                }

                if ($input) {
                    //////////////////////proses masukin dana yg ditarik
                    foreach (array_reverse($keuangan) as $baris) {

                        $harga = $baris[$marketplace->harga];
                        $kredit = abs($harga);

                        $tanggal = $baris[$marketplace->tanggal];

                        BukuBesar::create([
                            'akun_detail_id' => $config->penarikan_id,
                            'kode' => 'trf',
                            'created_at' => $tanggal,
                            'detail_id' => 123,
                            'ket' => 'penarikan dari ' . $config->nama,
                            'debet' => $kredit
                        ]);
                    }


                    $kredit = $debet = 0;
                    if ($dana_terakhir < 0)
                        $kredit = abs($dana_terakhir);
                    else
                        $debet = $dana_terakhir;

                    DB::table('buku_besars')->where('akun_detail_id', $config->kas_id)->delete();

                    DB::table('buku_besars')->insert([
                        'akun_detail_id' => $config->kas_id,
                        'kode' => 'byr',
                        'created_at' => $tanggal_terakhir,
                        'detail_id' => 123,
                        'ket' => $ket_terakhir,
                        'debet' => $debet,
                        'kredit' => $kredit,
                        'saldo' => $saldo_terakhir
                    ]);

                    DB::table('akun_details')->where('id', $config->kas_id)->update(['saldo' => $saldo_terakhir]);

                    if ($config->baruKeuangan == 1) {
                        $config->update(['baruKeuangan' => 0]);
                    }


                    $config->update(['tglUploadKeuangan' => now()]);
                } else
                    throw new \Exception('tanggal pengambilan rentangnya kurang panjang');
            });
            return redirect()->route('marketplaces.show', $id->id)->withSuccess(__('Upload keuangan berhasil'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Upload keuangan gagal: ' . $e->getMessage()]);
        }
    }

    public function uploadOrder(Request $request, Marketplace $id)
    {
        $request->validate([
            'order' => 'required|mimes:csv',
        ]);
        try {
            DB::transaction(function () use ($request, $id) {

                $file_excel = fopen(request()->order, "r");

                $no_baris = 0;
                $input = false;

                $config = $id;
                $marketplace = DB::table('marketplace_formats')->where('jenis', 'order')->where('marketplace', $config->marketplace)->first();

                $id_shopee = $config->kontak_id;

                ////ambil data semua produk di company
                $ambil = DB::table('produks')->get();

                ////bikin array data produk dengan key id dan id_produk(id project yg lama)
                $produks = $ambil->keyBy('id');

                //////posisi header di baris brapa
                $header = $marketplace->barisHeader ?? 1;

                $order = $orderdetil = $stok = $inputStok = $inputBatal =  $batal = [];
                $input = $notaTerakhir = false;
                $awal = true;
                $nota_skr = 0;
                //////jika marketplace baru, langsung input, ga usah dicek dulu
                if ($config->baruOrder == 1) {
                    $input = true;
                    $notaTerakhir = true;
                }

                /////ambil ida project_flow
                $batal_id = Produksi::ambilFlow('batal');
                $finish_id = Produksi::ambilFlow('finish');
                $awal_id = Produksi::ambilFlow('Persiapan');

                //////ambil nota terakhir yg udah terinput
                $terakhir = Order::where('kontak_id', $id_shopee)->latest('id')->first();

                while (($baris = fgetcsv($file_excel, 1000, ",")) !== false) {

                    $no_baris++;
                    /////tambahin 1 kolom didepan
                    array_unshift($baris, $no_baris);

                    //////cari posisi header
                    if ($no_baris < $header)
                        continue;
                    else if ($no_baris == $header) {
                        if ($baris[1] != $marketplace->kolom1 or $baris[2] != $marketplace->kolom2 or $baris[3] != $marketplace->kolom3)
                            throw new \Exception('excel salah');

                        continue;
                    }
                    if ($no_baris == $header + 1)
                        continue;

                    $nota = $baris[$marketplace->nota];
                    $status = $baris[$marketplace->status];
                    $barang = $baris[$marketplace->sku_anak];
                    if (empty($barang))
                        $barang = $baris[$marketplace->sku];

                    if (empty($barang))
                        continue;

                    if (!preg_match('/CUSTOM_/', $barang) && !preg_match('/\d/', $barang)) {
                        continue;
                    }

                    //////pengecekan order yg udah terinput sebelumnya
                    if (!$input) {

                        ////////jika statusnya batal, masukin ke array batal
                        if ($status == $marketplace->batal and strpos($barang, 'CUSTOM_') !== false)
                            $batal[$nota] = 1;

                        /////jika ketemu dgn nota terakhir, set nota terakhir true
                        if ($nota == $terakhir->nota) {
                            $notaTerakhir = true;
                            continue;
                        }
                        /////////jika nota terakhir udah selesai, dan ketemu nota baru, baru bisa mulai input
                        else   if ($notaTerakhir and $nota != $terakhir->nota)
                            $input = true;
                    }


                    if ($input) {

                        $tanggal = $baris[$marketplace->tanggal];
                        $tanggal = carbon::createFromFormat($marketplace->formatTanggal, $tanggal)->toDateTimeString();
                        $nama = $baris[$marketplace->nama];
                        $tema = $baris[$marketplace->tema];
                        $total = $baris[$marketplace->saldo];
                        $total = str_replace(".", "", $total);


                        $jumlah = $baris[$marketplace->jumlah];
                        $harga = str_replace("Rp ", "", $baris[$marketplace->harga]);
                        $harga = str_replace(".", "", $harga);
                        $jumlah = str_replace(".", "", $jumlah);


                        if ($status == $marketplace->batal) {
                            $produksi_id = $batal_id;
                            $total = 0;
                        } else {
                            $produksi_id = $finish_id;
                        }

                        //jika ganti nota
                        if ($nota != $nota_skr) {

                            if ($awal) {  //////simpen nota yg diinput pertama kali
                                $nota_awal = $nota;
                                $awal = false;
                            }

                            $order[] = array(
                                'kontak_id' => $id_shopee,
                                'total' => $total,
                                'nota' => $nota,
                                'created_at' => $tanggal,
                                'username' => $nama
                            );
                        }
                        ////jika sku NON_PRODUK, skip penginputan
                        if ($barang == "NON_PRODUK")
                            continue;

                        $custom = '';
                        $orderCustom = false;

                        //////jika sku depannya ada CUSTOM_ , hapus tulisan itu, sisain sku nya
                        if (strpos($barang, 'CUSTOM_') !== false) {
                            $produksi_id = $awal_id;
                            $barang = str_replace('CUSTOM_', "", $barang);

                            $orderCustom = true;
                            $custom = $tema;
                        }

                        /////////////////cek, apakah sku udah sesuai dgn produk_id
                        $produk = $produks[$barang] ?? false;
                        if (!$produk)
                            throw new \Exception('sku: ' . $barang . ', nama: ' . $baris[$marketplace->produk] . ', tidak ada di sistem');


                        /////mulai input orderdetil ke array
                        $orderdetil[] = array(
                            'produk_id' => $produk->id,
                            'jumlah' => $jumlah,
                            'tema' => $custom,
                            'harga' => $harga,
                            'produksi_id' => $produksi_id,
                            'nota' => $nota,
                            'created_at' => $tanggal,
                        );

                        ///////////////////kalo ordernya ga batal, dan produknya ada stoknya, input brapa yg terjual
                        if ($status != $marketplace->batal and !$orderCustom)
                            $stok[$produk->id] = $jumlah + ($stok[$produk->id] ?? 0);
                    }

                    $nota_skr = $nota;
                }

                if (!$notaTerakhir)
                    throw new \Exception('rentang tgl kurang panjang');

                ////////order yg udah terinput, tp cek apakah ada yg berubah dl batal
                if ($batal) {

                    $batal = array_keys($batal);

                    ////////////////cari di db, yg di excel nya batal, tp di table order_details msh blm batal
                    $batalx = DB::table('order_details')->whereIn('nota', $batal)->where('produksi_id', $finish_id)->get();

                    $diubahBatal = $produkBatal = $projectBatal = [];
                    //////kalo ada order_details yg blm dirubah ke batal, maka proses utk rubah
                    foreach ($batalx as $yy) {
                        ///project_detail yg blm dirubah ke batal
                        $diubahBatal[] = $yy->id;
                        ////project yg blm dirubah ke batal
                        $projectBatal[$yy->order_id] = 1;
                        //////jumlah produk yg batal dibeli
                        $produk = $produks[$yy->produk_id];
                        if ($produk->stok == 1)
                            $produkBatal[$yy->produk_id] = $yy->jumlah + ($produkBatal[$yy->produk_id] ?? 0);
                    }

                    ////proses perubahan ke db
                    if ($diubahBatal) {
                        DB::table('order_details')->whereIn('id', $diubahBatal)->update(['produksi_id' => $batal_id]);

                        DB::table('orders')->whereIn('id', array_keys($projectBatal))->update(['total' => 0]);
                    }

                    /////jika ada produk yg dikembalikan
                    if ($produkBatal) {
                        foreach ($produkBatal as $produk_id => $stokx) {

                            $produk = $produks[$produk_id];

                            $lastStok = ProdukStok::lastStok($produk_id);

                            $saldo = $lastStok + $stokx;
                            $inputBatal[] = array(
                                'produk_id' => $produk_id,
                                'tambah' => $stokx,
                                'saldo' => $saldo,
                                'keterangan' => $config->nama . ' tdk jd beli',
                                'kode' => 'jual',
                                'created_at' => now()
                            );
                        }

                        DB::table('produk_stoks')->insert($inputBatal);
                    }
                }

                //////////////jika ada order baru/////////////////////////////////////////////////////////////
                if ($input) {
                    DB::table('orders')->insert($order);
                    DB::table('order_details')->insert($orderdetil);

                    ////ambil orderdetil yg pertama akan diinput
                    $orderdetil_awal = DB::table('order_details')->where('nota', $nota_awal)->orderBy('id', 'desc')->first()->id;


                    //////update order_id ke table order_details (pas pertama input msh kosong)
                    DB::statement("UPDATE order_details
                        SET order_id = (
                            SELECT id
                            FROM orders
                            WHERE orders.nota=order_details.nota
                                and kontak_id=" . $id_shopee . "
                                limit 1
                        ) where id>=" . $orderdetil_awal . " and order_details.nota is not Null");


                    //////ngurangi stok yg terjual/////////////////////////////////////////////////////////
                    if ($stok) {
                        foreach ($stok as $produk_id => $stokx) {

                            $produk = $produks[$produk_id];

                            $lastStok = ProdukStok::lastStok($produk_id);

                            $saldo = $lastStok - $stokx;
                            $inputStok[] = array(
                                'produk_id' => $produk_id,
                                'kurang' => $stokx,
                                'saldo' => $saldo,
                                'keterangan' => 'upload ' . $config->nama,
                                'kode' => 'jual',
                                'created_at' => now()
                            );
                        }

                        DB::table('produk_stoks')->insert($inputStok);
                    }
                }


                if ($config->baruOrder == 1)
                    $config->update(['baruOrder' => 0]);

                $config->update(['tglUploadOrder' => now()]);
            });
            return redirect()->route('marketplaces.show', $id->id)->withSuccess(__('Upload Order berhasil'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Upload Order gagal: ' . $e->getMessage()]);
        }
    }
}
