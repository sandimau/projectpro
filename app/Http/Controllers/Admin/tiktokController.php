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
                $terakhir = Order::where('kontak_id', $id_shopee)->latest('id')->first();

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
                    // Karena data dibaca dari bawah ke atas, perlu membalik urutan sebelum insert
                    // agar urutan data di database tetap sesuai dengan urutan asli file
                    $order = array_reverse($order);
                    $orderdetil = array_reverse($orderdetil);

                    DB::table('orders')->insert($order);
                    DB::table('order_details')->insert($orderdetil);

                    ////ambil orderdetil yg pertama akan diinput (sekarang adalah yang terakhir dalam array)
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
}
