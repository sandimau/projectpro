<?php

namespace app\Tafio\bisnis\src\Library;

use App\Tafio\bisnis\src\Models\marketplaceConfig;
use App\Tafio\bisnis\src\Models\grup;
use App\Tafio\bisnis\src\Models\produkLastStok;
use App\Tafio\bisnis\src\Models\marketplaceFormat;
use App\Tafio\bisnis\src\Models\project;
use App\Tafio\bisnis\src\Models\produkStok;
use App\Tafio\bisnis\src\Models\bukuBesar;
use Illuminate\Support\Facades\DB;
use carbon\carbon;


trait marketplaceProses
{
    public function marketplaceOrder()
    {

        DB::transaction(function () {

            $file_excel = fopen(request()->order, "r");

            $no_baris = 0;
            $input = false;
            // $kas_terakhir = $this->Kas_model->get_terakhir($kas_shopee);


            $config = marketplaceConfig::find(ambil('marketplace'));
            $marketplace = marketplaceFormat::where('jenis', 'order')->where('marketplace', $config->marketplace)->first();

            $toko = $config->cabang_id;
            $id_shopee = $config->kontak_id;

            ////ambil data semua produk di company
            $ambil = DB::table('produks')->select('produks.id', 'hpp', 'stok', 'id_produk', 'harga')->where('produks.status', 1)
                ->where('produks.company_id', session('company'))
                ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
                ->get();




            ////bikin array data produk dengan key id dan id_produk(id project yg lama)
            $produks = $ambil->keyBy('id');
            $produks2 = $ambil->keyBy('id_produk');

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
            $batal_id = grup::ambilFlow('batal');
            $finish_id = grup::ambilFlow('finish');
            $awal_id = grup::ambilFlow('awal');

            //////ambil nota terakhir yg udah terinput
            $terakhir = project::where('kontak_id', $id_shopee)->where('cabang_id', $toko)->latest('id')->first();


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


                    if ($status == $marketplace->batal) {
                        $project_flow_id = $batal_id;
                        $total = 0;
                    } else
                        $project_flow_id = $finish_id;

                    //jika ganti nota





                    if ($nota != $nota_skr) {

                        if ($awal) {  //////simpen nota yg diinput pertama kali
                            $nota_awal = $nota;
                            $awal = false;
                        }

                        $order[] = array(
                            'kontak_id' => $id_shopee,
                            'cabang_id' => $toko,
                            'total' => $total,
                            'nota' => $nota,
                            'created_at' => $tanggal,
                            'konsumen_detail' => $nama,
                            'company_id' => session('company')
                        );
                    }
                    ////jika sku NON_PRODUK, skip penginputan
                    if ($barang == "NON_PRODUK")
                        continue;

                    $custom = '';
                    $orderCustom = false;

                    //////jika sku depannya ada CUSTOM_ , hapus tulisan itu, sisain sku nya
                    if (strpos($barang, 'CUSTOM_') !== false) {
                        $project_flow_id = $awal_id;
                        $barang = str_replace('CUSTOM_', "", $barang);

                        $orderCustom = true;



                        $custom = $tema;
                    }

                    /////////////////cek, apakah sku udah sesuai dgn produk_id
                    $produk = $produks[$barang] ?? $produks2[$barang] ?? false;
                    if (!$produk)
                        throw new \Exception('sku: ' . $barang . ', nama: ' . $baris[$marketplace->produk] . ', tidak ada di sistem');


                    /////mulai input orderdetil ke array
                    $orderdetil[] = array(
                        'produk_id' => $produk->id,
                        'jumlah' => $jumlah,
                        'tema' => $custom,
                        'harga' => $harga,
                        'project_flow_id' => $project_flow_id,
                        'nota' => $nota,
                        'company_id' => session('company')
                    );

                    ///////////////////kalo ordernya ga batal, dan produknya ada stoknya, input brapa yg terjual
                    if ($status != $marketplace->batal and $produk->stok == 1 and !$orderCustom)
                        $stok[$produk->id] = $jumlah + ($stok[$produk->id] ?? 0);
                }

                $nota_skr = $nota;
            }

            if (!$notaTerakhir)
                throw new \Exception('rentang tgl kurang panjang');
            //   dd($terakhir->nota);



            ////////order yg udah terinput, tp cek apakah ada yg berubah dl batal
            if ($batal) {

                $batal = array_keys($batal);

                ////////////////cari di db, yg di excel nya batal, tp di table project_details msh blm batal
                $batalx = DB::table('project_details')->whereIn('nota', $batal)->where('project_flow_id', $finish_id)->get();

                $diubahBatal = $produkBatal = $projectBatal = [];
                //////kalo ada project_details yg blm dirubah ke batal, maka proses utk rubah
                foreach ($batalx as $yy) {
                    ///project_detail yg blm dirubah ke batal
                    $diubahBatal[] = $yy->id;
                    ////project yg blm dirubah ke batal
                    $projectBatal[$yy->project_id] = 1;
                    //////jumlah produk yg batal dibeli
                    $produk = $produks[$yy->produk_id];
                    if ($produk->stok == 1)
                        $produkBatal[$yy->produk_id] = $yy->jumlah + ($produkBatal[$yy->produk_id] ?? 0);
                }

                ////proses perubahan ke db
                if ($diubahBatal) {
                    DB::table('project_details')->whereIn('id', $diubahBatal)->update(['project_flow_id' => $batal_id]);

                    DB::table('projects')->whereIn('id', array_keys($projectBatal))->update(['total' => 0]);
                }
                /////jika ada produk yg dikembalikan
                if ($produkBatal) {
                    foreach ($produkBatal as $produk_id => $stokx) {

                        $produk = $produks[$produk_id];



                        $lastStok = produkStok::lastStok($produk_id, $toko);

                        $saldo = $lastStok + $stokx;
                        $inputBatal[] = array(
                            'produk_id' => $produk_id,
                            'tambah' => $stokx,
                            'saldo' => $saldo,
                            'hpp' => $produk->hpp,
                            'keterangan' => $config->nama . ' tdk jd beli',
                            'kode' => 'jual',
                            'cabang_id' => $toko,
                            'created_at' => now(),
                            'company_id' => session('company')
                        );
                    }

                    DB::table('produk_stoks')->insert($inputBatal);
                }
            }

            //////////////jika ada order baru/////////////////////////////////////////////////////////////
            if ($input) {



                DB::table('projects')->insert($order);
                DB::table('project_details')->insert($orderdetil);

                ////ambil orderdetil yg pertama akan diinput
                $orderdetil_awal = DB::table('project_details')->where('nota', $nota_awal)->where('company_id', session('company'))->orderBy('id', 'desc')->first()->id;


                // dd($orderdetil_awal);

                //////update project_id ke table project_details (pas pertama input msh kosong)
                DB::statement("UPDATE project_details
                SET project_id = (
                    SELECT id
                    FROM projects
                    WHERE projects.nota=project_details.nota
                        and kontak_id=" . $id_shopee . "
                        and cabang_id=" . $toko . "
                        and company_id=" . session('company') . "
                        limit 1
                ) where id>=" . $orderdetil_awal . " and project_details.nota is not Null and company_id=" . session('company') . " ");


                //////ngurangi stok yg terjual/////////////////////////////////////////////////////////
                if ($stok) {
                    foreach ($stok as $produk_id => $stokx) {

                        $produk = $produks[$produk_id];

                        $lastStok = produkStok::lastStok($produk_id, $toko);

                        $saldo = $lastStok - $stokx;
                        $inputStok[] = array(
                            'produk_id' => $produk_id,
                            'kurang' => $stokx,
                            'saldo' => $saldo,
                            'hpp' => $produk->hpp,
                            'keterangan' => 'upload ' . $config->nama,
                            'kode' => 'jual',
                            'cabang_id' => $toko,
                            'created_at' => now(),
                            'company_id' => session('company')
                        );
                    }

                    DB::table('produk_stoks')->insert($inputStok);
                }
            }


            if ($config->baruOrder == 1)
                $config->update(['baruOrder' => 0]);

            $config->update(['tglUploadOrderTerakhir' => now()]);
        });
    }
    /////////////////////////////////////////////////////////////////////////////

    public function marketplaceStok()
    {



        $file_excel = fopen(request()->stok, "r");

        $i = 0;


        $config = marketplaceConfig::find(ambil('marketplace'));
        $marketplace = marketplaceFormat::where('jenis', 'stok')->where('marketplace', $config->marketplace)->first();

        $header = $marketplace->barisHeader ?? 1;


        $table = '<table border="1" cellpadding="0" cellspacing="0" width=100% class=table>';


        /////////////ambil semua produk, masukin ke array
        $ambil = DB::table('produks')->select('produks.id',  'stok', 'id_produk', 'harga')->where('produks.status', 1)
            ->where('produks.company_id', session('company'))
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->get();
        $produks = $ambil->keyBy('id');
        $produks2 = $ambil->keyBy('id_produk');

        $toko = $config->cabang_id;


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

                // $produk = $produks->firstWhere('id',$sku);



                // //////// sampe sini hapusnya


                $produk = $produks[$sku] ?? $produks2[$sku] ?? false;

                //////// sampe sini hapusnya


                if ($produk) {

                    if ($produk->stok == 1) {

                        $stok = produkLastStok::stok($produk->id);
                        // if($produk->id==5031)
                        // dd($produk->produkModel->nama);


                        if ($stok < 0)
                            $stok = 0;
                    } else
                        $stok = 10000;



                    if (!$custom)
                        $harga_baru = $produk->harga;
                    else
                        $harga_baru = $harga;


                    if (empty($harga_baru)) {
                        $table .= "<td colspan=4><h2><font color=red>error!! harga di project masih kosong";
                        break;
                    }
                    $harga_baru = floor($harga_baru * (100 + $config->harga) / 100);

                    if ((abs($harga - $harga_baru) / $harga * 100) > 20)
                        $harga = "<h4><font color=red>" . $harga_baru;
                    else if ($harga != $harga_baru)
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


        echo $table;

        $config->update(['tglUploadStokTerakhir' => now()]);
    }


    public function marketplaceKeuangan()
    {
        DB::transaction(function () {
            $file_excel = fopen(request()->keuangan, "r");

            $i = 0;


            $config = marketplaceConfig::find(ambil('marketplace'));
            $marketplace = marketplaceFormat::where('jenis', 'keuangan')->where('marketplace', $config->marketplace)->first();

            $header = $marketplace->barisHeader ?? 1;



            $keuangan = [];
            $input = false;
            if ($config->baruKeuangan == 1)
                $input = true;
            else
                //////ambil yg terakhir terinput
                $terakhir = bukuBesar::where('akun_detail_id', $config->kas_id)->latest()->first();

            // dd($terakhir);

            // dd($terakhir);

            while (($baris = fgetcsv($file_excel, 1000, ",")) !== false) {

                $i++;
                array_unshift($baris, $i);

                if ($i < $header)
                    continue;
                else if ($i == $header) {

                    if ($baris[1] != $marketplace->kolom1 or $baris[2] != $marketplace->kolom2 or $baris[3] != $marketplace->kolom3)
                        throw new \Exception('excel salah');

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
                $tanggal = $baris[$marketplace->tanggal] = carbon::createFromFormat($marketplace->formatTanggal, $tanggal)->toDateTimeString();



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

                    bukubesar::create([
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




                DB::table('akun_buku_besars')->where('akun_detail_id', $config->kas_id)->delete();



                DB::table('akun_buku_besars')->insert([
                    'akun_detail_id' => $config->kas_id,
                    'company_id' => session('company'),
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


                $config->update(['tglUploadKeuanganTerakhir' => now()]);
            } else
                throw new \Exception('tanggal pengambilan rentangnya kurang panjang');
        });
    }


    public function marketplaceKonek()
    {

        $file_excel = fopen(request()->konek, "r");

        $i = 0;


        $config = marketplaceConfig::find(ambil('marketplace'));
        $marketplace = marketplaceFormat::where('jenis', 'stok')->where('marketplace', $config->marketplace)->first();

        $header = $marketplace->barisHeader ?? 1;


        /////////////ambil semua produk, masukin ke array
        $ambil = DB::table('produks')->select('produks.id',  'stok', 'id_produk', 'harga')->where('produks.status', 1)
            ->where('produks.company_id', session('company'))
            ->join('produk_models', 'produks.produk_model_id', '=', 'produk_models.id')
            ->get();
        $produks = $ambil->keyBy('id');

        $toko = $config->cabang_id;
        $input = [];
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




            $sku_induk = $baris[$marketplace->sku];
            $sku_anak = $baris[$marketplace->sku_anak] ?? '';

            $produk = $baris[$marketplace->produk];

            $item_id = $baris[$marketplace->nota] ?? '';
            $model_id = $baris[$marketplace->tanggal] ?? '';

            $custom = false;


            $sku = !empty($sku_anak) ? $sku_anak : $sku_induk;

            if (empty($sku))
                throw new \Exception('error!! sku produk ' . $produk . 'yg di shopee blm diisi');

            if ($sku != 'NON_PRODUK') {
                if (strpos($sku, 'CUSTOM_') !== false) {
                    $custom = true;
                    $sku = str_replace('CUSTOM_', "", $sku);
                }

                $produk = $produks[$sku] ?? false;

                //////// sampe sini hapusnya


                if ($produk) {

                    $input[] = [
                        'produk_id' => $sku,
                        'marketplace_id' => ($config->id),
                        'item_id' => $item_id,
                        'model_id' => $model_id
                    ];
                } else
                    throw new \Exception('error!! sku no:' . $sku . ', produk: ' . $produk . ' tdk ada di project');
            }
        }
        DB::table('produk_marketplaces')->where('marketplace_id', $config->id)->delete();
        DB::table('produk_marketplaces')->insert($input);
    }
}
