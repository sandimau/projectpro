<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Belanja;
use App\Models\Produksi;
use App\Models\BukuBesar;
use App\Models\AkunDetail;
use App\Models\Pembayaran;
use App\Models\ProdukStok;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use App\Models\BelanjaDetail;
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
        $marketplace->load('produk.produkModel.kategori');
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

                // Get existing orders for this marketplace contact
                $existingOrders = DB::table('orders')
                    ->where('kontak_id', $config->kontak_id)
                    ->get();
                $orders = $existingOrders->keyBy('nota');

                $keuangan = $order = $iklan = [];
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

                    if (strpos($baris[$marketplace->tema], 'Isi Ulang Saldo Iklan/Koin Penjual') !== false) {
                        $iklan[] = $baris;
                    }

                    if (strlen($baris[4]) > 8) {
                        if (isset($orders[$baris[4]])) {
                            $order[] = $baris;
                        }
                    }
                }

                if ($input) {

                    foreach (array_reverse($iklan) as $baris) {
                        $belanja = Belanja::create([
                            'nota' => $request->nota ? $request->nota : rand(1000000, 100),
                            'total' => abs($baris[6]),
                            'kontak_id' => $config->kontak_id,
                            'akun_detail_id' => $config->kas_id,
                            'pembayaran' => abs($baris[6]),
                            'created_at' => $baris[1],
                        ]);

                        BelanjaDetail::create([
                            'belanja_id' => $belanja->id,
                            'produk_id' => $config->iklan,
                            'harga' => abs($baris[6]),
                            'jumlah' => 1,
                            'keterangan' => $baris[3],
                        ]);
                    }

                    //proses update order sudah dibayar
                    foreach ($order as $baris) {
                        Order::where('nota', $baris[4])->update([
                            'bayar' => $baris[6]
                        ]);
                        Pembayaran::create([
                            'order_id' => Order::where('nota', $baris[4])->first()->id,
                            'jumlah' => $baris[6],
                            'created_at' => $baris[$marketplace->tanggal],
                            'status' => 'lunas',
                            'akun_detail_id' => $config->penarikan_id,
                            'ket' => 'upload keuangan',
                        ]);
                    }
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

                $toko = $config->cabang_id;
                $id_shopee = $config->kontak_id;

                ////ambil data semua produk di company
                $ambil = DB::table('produks')->select('produks.id', 'hpp', 'stok', 'harga')->where('produks.status', 1)
                    ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
                    ->get();

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
                    if ($no_baris == $header)
                        continue;

                    $nota = $baris[$marketplace->nota];
                    $status = $baris[$marketplace->status];
                    $barang = $baris[$marketplace->sku_anak];
                    if (empty($barang))
                        $barang = $baris[$marketplace->sku];

                    //////pengecekan order yg udah terinput sebelumnya
                    if (!$input) {

                        ////////jika statusnya batal, masukin ke array batal
                        if ($status == $marketplace->batal and strpos($barang, 'CUSTOM_') !== false)
                            $batal[$nota] = 1;

                        /////jika ketemu dgn nota terakhir, set nota terakhir true
                        if ($terakhir && $nota == $terakhir->nota) {
                            $notaTerakhir = true;
                            continue;
                        }
                        /////////jika nota terakhir udah selesai, dan ketemu nota baru, baru bisa mulai input
                        else if ($notaTerakhir && $terakhir && $nota != $terakhir->nota)
                            $input = true;
                    }


                    if ($input) {

                        $tanggal = $baris[$marketplace->tanggal];
                        $tanggal = Carbon::createFromFormat($marketplace->formatTanggal, $tanggal)->toDateTimeString();
                        $nama = $baris[$marketplace->nama];
                        $tema = $baris[$marketplace->tema];
                        $total = $baris[$marketplace->saldo];
                        $total = str_replace(".", "", $total);

                        $jumlah = $baris[$marketplace->jumlah];
                        $harga = str_replace("Rp ", "", $baris[$marketplace->harga]);
                        $harga = str_replace(".", "", $harga);

                        if ($status == $marketplace->batal) {
                            $produksi_id = $batal_id;
                            $total = 0;
                        } else
                            $produksi_id = $finish_id;

                        //jika ganti nota
                        if ($nota != $nota_skr) {

                            if ($awal) {  //////simpen nota yg diinput pertama kali
                                $nota_awal = $nota;
                                $awal = false;
                            }

                            $ongkir = str_replace(".", "", $baris[$marketplace->ongkir]);
                            $deathline = $baris[$marketplace->deathline];

                            $order[] = array(
                                'kontak_id' => $id_shopee,
                                'total' => $total,
                                'nota' => $nota,
                                'created_at' => $tanggal,
                                'konsumen_detail' => $nama,
                                'deathline' => $deathline,
                                'marketplace' => 1,
                                'ongkir' => $ongkir
                            );
                        }
                        ////jika sku NON_PRODUK, skip penginputan
                        if ($barang == "NON_PRODUK")
                            continue;

                        $custom = '';
                        $orderCustom = false;

                        //////jika sku depannya ada CUSTOM_ , hapus tulisan itu, sisain sku nya
                        if (strpos($barang, 'CUSTOM_') !== false) {
                            $barang = str_replace('CUSTOM_', "", $barang);
                            $orderCustom = true;
                            $custom = $tema;

                            if ($status == $marketplace->batal) {
                                $produksi_id = $batal_id;
                            } else {
                                $produksi_id = $awal_id;
                            }
                        }

                        $paket = 1;
                        if (strpos($barang, '_') !== false) {
                            $skuParts = explode('_', $barang);
                            $barang = $skuParts[0]; // Mengambil bagian pertama dari SKU
                            $paket = $skuParts[1]; // Menambahkan paket dengan bagian kedua dari SKU
                            $jumlah = $jumlah * $paket;
                        }

                        /////////////////cek, apakah sku udah sesuai dgn produk_id
                        $produk = $produks[$barang] ?? false;
                        if (!$produk)
                            throw new \Exception('sku: ' . $barang . ', nama: ' . $baris[$marketplace->produk] . ', tidak ada di sistem');

                        $hpp = Produk::find($produk->id);

                        /////mulai input orderdetil ke array
                        $orderdetil[] = array(
                            'produk_id' => $produk->id,
                            'jumlah' => $baris[$marketplace->jumlah],
                            'tema' => $custom,
                            'harga' => $harga,
                            'hpp' => $hpp->hpp,
                            'produksi_id' => $produksi_id,
                            'nota' => $nota,
                            'created_at' => $tanggal,
                            'deathline' => $deathline
                        );

                        ///////////////////kalo ordernya ga batal, dan produknya ada stoknya, input brapa yg terjual
                        if ($status != $marketplace->batal and $produk->stok == 1 and !$orderCustom)
                            $stok[] = array(
                                'produk_id' => $produk->id,
                                'jumlah' => $jumlah,
                                'keterangan' => 'dibeli oleh ' . $nama
                            );
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

                            ProdukStok::create([
                                'produk_id' => $produk_id,
                                'tambah' => $stokx,
                                'kurang' => 0,
                                'keterangan' => 'upload ' . $config->nama,
                                'kode' => 'batal'
                            ]);
                        }
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
                        foreach ($stok as $value) {
                            ProdukStok::create([
                                'produk_id' => $value['produk_id'],
                                'tambah' => 0,
                                'kurang' => $value['jumlah'],
                                'keterangan' => $value['keterangan'],
                                'kode' => 'jual'
                            ]);
                        }
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

    public function uploadStok(Request $request, Marketplace $id)
    {
        $request->validate([
            'stok' => 'required|mimes:csv',
        ]);

        try {
            $file_excel = fopen(request()->stok, "r");

            $i = 0;


            $config = $id;
            $marketplace = DB::table('marketplace_formats')->where('jenis', 'stok')->where('marketplace', $config->marketplace)->first();

            $header = $marketplace->barisHeader ?? 1;


            $table = '<table border="1" cellpadding="0" cellspacing="0" width=100% class=table>';


            /////////////ambil semua produk, masukin ke array
            $ambil = DB::table('produks')->select('produks.id',  'stok', 'harga')->where('produks.status', 1)
                ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
                ->get();
            $produks = $ambil->keyBy('id');


            while (($baris = fgetcsv($file_excel, 1000, ",")) !== false) {

                $i++;
                array_unshift($baris, $i);

                if ($i < $header)
                    continue;
                else if ($i == $header) {
                    if ($baris[1] != $marketplace->kolom1 or $baris[2] != $marketplace->kolom2 or $baris[3] != $marketplace->kolom3)
                        throw new \Exception('excel salah');

                    continue;
                } else if ($i < ($header + $marketplace->status))
                    continue;

                $varian = $baris[$marketplace->tema] ?? '';
                $produk = $baris[$marketplace->produk];
                $sku_induk = $baris[$marketplace->sku];
                $sku_anak = $baris[$marketplace->sku_anak] ?? '';
                $harga = $baris[$marketplace->harga];
                $stok = $baris[$marketplace->saldo] ?? 0;

                $custom = false;


                $sku = !empty($sku_anak) ? $sku_anak : $sku_induk;


                $table .= "<tr ><td>" . $i . '<td>' . $produk . '<td>' . $varian . "<td>" . $sku_induk . '<td>' . $sku_anak . '<td>' . $harga;
                if (empty($sku)) {
                    $table .= "<td colspan=4><h2><font color=red>error!! sku yg di shopee blm diisi";
                    break;
                }

                if ($sku != 'NON_PRODUK') {
                    if (strpos($sku, 'CUSTOM_') !== false) {
                        $custom = true;
                        $sku = str_replace('CUSTOM_', "", $sku);
                    }
                    if (strpos($sku, '_') !== false) {
                        $skuParts = explode('_', $sku);
                        $paket = $skuParts[1];
                        $sku = $skuParts[0]; // Mengambil bagian pertama dari SKU
                    } else {
                        $paket = 1;
                    }

                    // //////// sampe sini hapusnya


                    $produk = $produks[$sku] ?? false;

                    //////// sampe sini hapusnya


                    if ($produk) {

                        if ($produk->stok == 1) {

                            $stok = ProdukStok::lastStok($produk->id);

                            if ($stok < 0)
                                $stok = 0;
                            if ($paket) {
                                $stok = floor($stok / $paket);
                            }
                        } else
                            $stok = 10000;



                        if (!$custom)
                            $harga_baru = $produk->harga;
                        else
                            $harga_baru = (float)$harga;


                        if (empty($harga_baru)) {
                            $table .= "<td colspan=4><h2><font color=red>error!! harga di project masih kosong";
                            break;
                        }
                        $harga_baru = floor($harga_baru * (100 + $config->harga) / 100);

                        if ((float)$harga == 0) {
                            $table .= "<td colspan=4><h2><font color=red>error!! harga tidak boleh 0";
                            break;
                        }

                        $perbedaan_persen = abs((float)$harga - $harga_baru) / (float)$harga * 100;

                        if ($perbedaan_persen > 20)
                            $harga = "<h4><font color=red>" . $harga_baru;
                        else if ((float)$harga != $harga_baru)
                            $harga = "<h4><font color=green>" . $harga_baru;
                        else
                            $harga = $harga_baru;
                    } else {
                        $table .= "<td colspan=4><h2><font color=red>error!! sku tidak ada di project";
                        break;
                    }
                }

                $table .= '<td>'

                    //////// kalo semua migrasi udah beres, ini hapus
                    . ($produk->id ?? '') . '<td>'
                    //////// sampe sini hapusnya

                    . $harga . '<td>' . $stok;
            }

            $table .= '</table>';

            $config->update(['tglUploadStok' => now()]);

            echo $table;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Upload Stok gagal: ' . $e->getMessage()]);
        }
    }

    public function analisa(Request $request)
    {
        $marketplaces = Marketplace::with(['kontak' => function ($query) {
            $query->whereNotNull('marketplace');
        }])->get();

        $tahun_skr = date('Y');
        $bulan_skr = date('n');
        $data = [];

        for ($i = 1; $i <= $bulan_skr; $i++) {
            $bulan = str_pad($i, 2, '0', STR_PAD_LEFT);
            $bulan_nama = date('F', mktime(0, 0, 0, $i, 1));

            $omzet = DB::table('orders')
                ->selectRaw('sum(total) as omzet, kontak_id')
                ->whereYear('created_at', $tahun_skr)
                ->whereMonth('created_at', $i)
                ->groupBy('kontak_id')
                ->get()
                ->pluck('omzet', 'kontak_id');

            $bayar = DB::table('orders')
                ->selectRaw('sum(total) as total,sum(bayar) as bayar, kontak_id')
                ->whereYear('created_at', $tahun_skr)
                ->whereMonth('created_at', $i)
                ->where('bayar', '>', 0)
                ->groupBy('kontak_id')
                ->get();
            $total = $bayar->pluck('total', 'kontak_id');
            $bayar = $bayar->pluck('bayar', 'kontak_id');

            $hpp = DB::table('orders')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->selectRaw('sum(order_details.hpp*order_details.jumlah) as hpp, orders.kontak_id')
                ->whereYear('orders.created_at', $tahun_skr)
                ->whereMonth('orders.created_at', $i)
                ->where('orders.bayar', '>', 0)
                ->groupBy('orders.kontak_id')
                ->get()
                ->pluck('hpp', 'kontak_id');

            $produkIklan = DB::table('produks')
                ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
                ->where('produks.status', 1)
                ->where('produk_models.nama', 'like', '%Biaya Iklan%')
                ->orWhere('produk_models.nama', 'like', '%iklan%')
                ->pluck('produks.id');

            $iklan = DB::table('belanjas')
                ->selectRaw('sum(belanja_details.harga * belanja_details.jumlah) as potongan, belanjas.kontak_id as kontak_id')
                ->join('belanja_details', 'belanjas.id', '=', 'belanja_details.belanja_id')
                ->whereYear('belanjas.created_at', $tahun_skr)
                ->whereMonth('belanjas.created_at', $i)
                ->whereIn('belanja_details.produk_id', $produkIklan)
                ->groupBy('belanjas.kontak_id')
                ->get();
            $iklan = $iklan->pluck('potongan', 'kontak_id');

            $data[$bulan] = [
                'nama' => $bulan_nama,
                'omzet' => $omzet,
                'bayar' => $bayar,
                'total' => $total,
                'hpp' => $hpp,
                'iklan' => $iklan
            ];
        }

        return view('admin.marketplaces.analisa', compact('marketplaces', 'data'));
    }

    public function uploadOrderTiktok(Request $request, Marketplace $id)
    {
        $request->validate([
            'order' => 'required|mimes:csv',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $config = $id;
                $marketplace = DB::table('marketplace_formats')->where('jenis', 'order')->where('marketplace', $config->marketplace)->first();

                $toko = $config->cabang_id;
                $id_shopee = $config->kontak_id;

                ////ambil data semua produk di company
                $ambil = DB::table('produks')->select('produks.id', 'hpp', 'stok', 'harga')->where('produks.status', 1)
                    ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
                    ->get();

                ////bikin array data produk dengan key id dan id_produk(id project yg lama)
                $produks = $ambil->keyBy('id');

                //////posisi header di baris brapa
                $header = $marketplace->barisHeader ?? 1;

                // Baca seluruh file ke array terlebih dahulu
                $file_excel = fopen(request()->order, "r");
                $all_rows = [];
                while (($baris = fgetcsv($file_excel, 1000, ",")) !== false) {
                    $all_rows[] = $baris;
                }
                fclose($file_excel);

                // Validasi header file terlebih dahulu
                if (count($all_rows) < $header) {
                    throw new \Exception('File Excel tidak memiliki header yang valid');
                }

                $header_row = $all_rows[$header - 1]; // array index dimulai dari 0
                if ($header_row[0] != $marketplace->kolom1 or $header_row[1] != $marketplace->kolom2 or $header_row[2] != $marketplace->kolom3) {
                    throw new \Exception('Format header Excel salah. Expected: ' . $marketplace->kolom1 . ', ' . $marketplace->kolom2 . ', ' . $marketplace->kolom3);
                }

                $order = $orderdetil = $stok = $inputStok = $inputBatal =  $batal = [];
                $input = false;
                $notaTerakhir = false;
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
                $terakhir = Order::where('kontak_id', $id_shopee)->latest('created_at')->first();

                // Proses data dari bawah ke atas (reverse array), skip header
                $data_rows = array_slice($all_rows, $header); // ambil data setelah header
                $data_rows = array_reverse($data_rows); // balik urutan dari bawah ke atas

                $total_rows = count($all_rows);

                foreach ($data_rows as $index => $baris) {

                    // Hitung nomor baris yang sebenarnya (dari bawah ke atas)
                    $no_baris = $total_rows - $index;

                    /////tambahin 1 kolom didepan dengan nomor baris
                    array_unshift($baris, $no_baris);

                    $nota = $baris[$marketplace->nota];
                    $status = $baris[$marketplace->status];
                    $barang = $baris[$marketplace->sku_anak];
                    if (empty($barang))
                        $barang = $baris[$marketplace->sku];

                    //////pengecekan order yg udah terinput sebelumnya
                    if (!$input) {

                        ////////jika statusnya batal, masukin ke array batal
                        if ($status == $marketplace->batal and strpos($barang, 'CUSTOM_') !== false)
                            $batal[$nota] = 1;

                        /////karena baca dari bawah ke atas, jika ketemu nota yang belum ada di database, mulai input
                        if (!$terakhir) {
                            // Jika belum ada order sama sekali, langsung mulai input
                            $input = true;
                            $notaTerakhir = true;
                        } else {
                            // Cek apakah nota ini sudah ada di database
                            $cek_nota_exist = Order::where('kontak_id', $id_shopee)->where('nota', $nota)->exists();

                            if (!$cek_nota_exist) {
                                // Nota belum ada di database, mulai input
                                $input = true;
                                $notaTerakhir = true;
                            } else {
                                // Nota sudah ada, skip (karena sudah diinput sebelumnya)
                                continue;
                            }
                        }
                    }


                    if ($input) {

                        $tanggal = $baris[$marketplace->tanggal];
                        $tanggal = Carbon::createFromFormat($marketplace->formatTanggal, $tanggal)->toDateTimeString();
                        $nama = $baris[$marketplace->nama];
                        $tema = $baris[$marketplace->tema];
                        $total = $baris[$marketplace->saldo];
                        $total = str_replace(".", "", $total);

                        $jumlah = $baris[$marketplace->jumlah];
                        $harga = str_replace("Rp ", "", $baris[$marketplace->harga]);
                        $harga = str_replace(".", "", $harga);

                        if ($status == $marketplace->batal) {
                            $produksi_id = $batal_id;
                            $total = 0;
                        } else
                            $produksi_id = $finish_id;

                        //jika ganti nota
                        if ($nota != $nota_skr) {

                            // Karena baca dari bawah ke atas, nota_awal akan selalu diupdate dengan nota terbaru
                            $nota_awal = $nota;

                            $ongkir = str_replace(".", "", $baris[$marketplace->ongkir]);
                            $deathline = $baris[$marketplace->deathline] ?? '';

                            $order[] = array(
                                'kontak_id' => $id_shopee,
                                'total' => $total,
                                'nota' => $nota,
                                'created_at' => $tanggal,
                                'konsumen_detail' => $nama,
                                'deathline' => $deathline,
                                'marketplace' => 1,
                                'ongkir' => $ongkir
                            );
                        }
                        ////jika sku NON_PRODUK, skip penginputan
                        if ($barang == "NON_PRODUK")
                            continue;

                        $custom = '';
                        $orderCustom = false;

                        //////jika sku depannya ada CUSTOM_ , hapus tulisan itu, sisain sku nya
                        if (strpos($barang, 'CUSTOM_') !== false) {
                            $barang = str_replace('CUSTOM_', "", $barang);
                            $orderCustom = true;
                            $custom = $tema;

                            if ($status == $marketplace->batal) {
                                $produksi_id = $batal_id;
                            } else {
                                $produksi_id = $awal_id;
                            }
                        }

                        $paket = 1;
                        if (strpos($barang, '_') !== false) {
                            $skuParts = explode('_', $barang);
                            $barang = $skuParts[0]; // Mengambil bagian pertama dari SKU
                            $paket = $skuParts[1]; // Menambahkan paket dengan bagian kedua dari SKU
                            $jumlah = $jumlah * $paket;
                        }

                        /////////////////cek, apakah sku udah sesuai dgn produk_id
                        $produk = $produks[$barang] ?? false;
                        if (!$produk)
                            throw new \Exception('sku: ' . $barang . ', nama: ' . $baris[$marketplace->produk] . ', tidak ada di sistem');

                        $hpp = Produk::find($produk->id);

                        /////mulai input orderdetil ke array
                        $orderdetil[] = array(
                            'produk_id' => $produk->id,
                            'jumlah' => $baris[$marketplace->jumlah],
                            'tema' => $custom,
                            'harga' => $harga,
                            'hpp' => $hpp->hpp,
                            'produksi_id' => $produksi_id,
                            'nota' => $nota,
                            'created_at' => $tanggal,
                            'deathline' => $deathline
                        );

                        ///////////////////kalo ordernya ga batal, dan produknya ada stoknya, input brapa yg terjual
                        if ($status != $marketplace->batal and $produk->stok == 1 and !$orderCustom)
                            $stok[] = array(
                                'produk_id' => $produk->id,
                                'jumlah' => $jumlah,
                                'keterangan' => 'dibeli oleh ' . $nama
                            );
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

                            ProdukStok::create([
                                'produk_id' => $produk_id,
                                'tambah' => $stokx,
                                'kurang' => 0,
                                'keterangan' => 'upload ' . $config->nama,
                                'kode' => 'batal'
                            ]);
                        }
                    }
                }

                //////////////jika ada order baru/////////////////////////////////////////////////////////////
                if ($input) {
                    // Karena data dibaca dari bawah ke atas, perlu membalik urutan sebelum insert
                    // agar urutan data di database tetap sesuai dengan urutan asli file
                    $order = array_reverse($order);
                    $orderdetil = array_reverse($orderdetil);

                    // Filter order yang belum ada di database untuk menghindari duplikat
                    $order_filtered = [];
                    $orderdetil_filtered = [];

                    foreach ($order as $order_item) {
                        // Cek apakah order dengan nota dan kontak_id ini sudah ada
                        $existing_order = DB::table('orders')
                            ->where('nota', $order_item['nota'])
                            ->where('kontak_id', $order_item['kontak_id'])
                            ->exists();

                        if (!$existing_order) {
                            $order_filtered[] = $order_item;

                            // Ambil order detail yang sesuai dengan nota ini
                            foreach ($orderdetil as $detail_item) {
                                if ($detail_item['nota'] == $order_item['nota']) {
                                    $orderdetil_filtered[] = $detail_item;
                                }
                            }
                        }
                    }

                    // Insert hanya order yang belum ada di database
                    if (!empty($order_filtered)) {
                        DB::table('orders')->insert($order_filtered);
                    }

                    if (!empty($orderdetil_filtered)) {
                        DB::table('order_details')->insert($orderdetil_filtered);
                    }

                    // Update order_id ke table order_details hanya jika ada order detail yang baru diinsert
                    if (!empty($orderdetil_filtered)) {
                        ////ambil orderdetil yg pertama akan diinput (sekarang adalah yang terakhir dalam array)
                        $orderdetil_awal = DB::table('order_details')->where('nota', $nota_awal)->orderBy('id', 'desc')->first();

                        if ($orderdetil_awal) {
                            //////update order_id ke table order_details (pas pertama input msh kosong)
                            DB::statement("UPDATE order_details
                                SET order_id = (
                                    SELECT id
                                    FROM orders
                                    WHERE orders.nota=order_details.nota
                                        and kontak_id=" . $id_shopee . "
                                        limit 1
                                ) where id>=" . $orderdetil_awal->id . " and order_details.nota is not Null");
                        }
                    }


                    //////ngurangi stok yg terjual/////////////////////////////////////////////////////////
                    if ($stok) {
                        foreach ($stok as $value) {
                            ProdukStok::create([
                                'produk_id' => $value['produk_id'],
                                'tambah' => 0,
                                'kurang' => $value['jumlah'],
                                'keterangan' => $value['keterangan'],
                                'kode' => 'jual'
                            ]);
                        }
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
