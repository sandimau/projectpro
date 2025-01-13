<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

/**
 * Auth Routes
 */
Auth::routes();


Route::group(['namespace' => 'App\Http\Controllers'], function()
{
    Route::middleware('auth')->group(function () {

        Route::get('/whattodo', 'HomeController@index')->name('whattodo');
        Route::get('/profile', 'ProfileController@show')->name('profile.show');
        Route::get('/profile/{id}/cuti', 'ProfileController@cuti')->name('profile.cuti');
        Route::get('/profile/{id}/gaji', 'ProfileController@gaji')->name('profile.gaji');
        Route::patch('/profile/{id}/update', 'ProfileController@update')->name('profile.update');

        //whattodo
        Route::get('/whattodo/create', 'HomeController@create')->name('whattodo.create');
        Route::post('/whattodo/create', 'HomeController@store')->name('whattodo.store');
        Route::get('/whattodo/{what}/edit', 'HomeController@edit')->name('whattodo.edit');
        Route::patch('/whattodo/{what}/update', 'HomeController@update')->name('whattodo.update');
        Route::delete('/whattodo/{what}/delete', 'HomeController@destroy')->name('whattodo.destroy');

        Route::resource('roles', RolesController::class);

        Route::resource('permissions', PermissionsController::class);

        Route::group(['prefix' => 'users'], function() {
            Route::get('/', 'UserController@index')->name('users.index');
            Route::get('/create', 'UserController@create')->name('users.create');
            Route::post('/create', 'UserController@store')->name('users.store');
            Route::get('/{user}/edit', 'UserController@edit')->name('users.edit');
            Route::patch('/{user}/update', 'UserController@update')->name('users.update');
            Route::delete('/{user}/delete', 'UserController@destroy')->name('users.destroy');
        });

        Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
            Route::resource('akuns', 'AkunController');

            // akun details
            Route::resource('akunDetails', 'AkunDetailController');
            Route::get('/akunDetails/{akunDetail}/bukuBesar', 'AkunDetailController@bukubesar')->name('akundetail.bukubesar');
            Route::get('/akunDetails/{akunDetail}/transfer', 'AkunDetailController@transfer')->name('akundetail.transfer');
            Route::post('/transfer/create', 'AkunDetailController@transferStore')->name('transfer.store');
            Route::get('/akunDetails/{akunDetail}/transferLain', 'AkunDetailController@transferLain')->name('akundetail.transferLain');
            Route::post('/transferLain/create', 'AkunDetailController@transferStoreLain')->name('transferLain.store');

            Route::resource('akunKategoris', 'AkunKategoriController');

            // member
            Route::resource('members', 'MemberController');
            Route::get('/nonaktif', 'MemberController@nonaktif')->name('members.nonaktif');
            Route::get('/cuti/{member}', 'MemberController@cuti')->name('members.cuti');
            Route::get('/kasbon/{member}', 'MemberController@kasbon')->name('members.kasbon');
            Route::get('/lembur/{member}', 'MemberController@lembur')->name('members.lembur');
            Route::get('/tunjangan/{member}', 'MemberController@tunjangan')->name('members.tunjangan');
            Route::get('/penggajian/{member}', 'MemberController@penggajian')->name('members.penggajian');
            Route::get('/gaji/{member}', 'MemberController@gaji')->name('members.gaji');
            Route::get('/ijin/{member}', 'MemberController@ijin')->name('members.ijin');

            //cuti
            Route::get('/members/{member}/cuti', 'CutiController@create')->name('cuti.create');
            Route::get('/members/{member}/ijin', 'CutiController@createIjin')->name('ijin.create');
            Route::post('/cuti/create', 'CutiController@store')->name('cuti.store');
            Route::post('/ijin/create', 'CutiController@storeIjin')->name('ijin.store');
            Route::get('/cuti/{cuti}/edit', 'CutiController@edit')->name('cuti.edit');
            Route::patch('/cuti/{cuti}/update', 'CutiController@update')->name('cuti.update');

            //lembur
            Route::get('/members/{member}/lembur', 'LemburController@create')->name('lembur.create');
            Route::post('/lembur/create', 'LemburController@store')->name('lembur.store');
            Route::get('/lembur/{lembur}/edit', 'LemburController@edit')->name('lembur.edit');
            Route::patch('/lembur/{lembur}/update', 'LemburController@update')->name('lembur.update');

            //kasbon
            Route::get('/members/{member}/kasbon', 'KasbonController@create')->name('kasbon.create');
            Route::post('/kasbon/create', 'KasbonController@store')->name('kasbon.store');
            Route::get('/kasbon/{kasbon}/edit', 'KasbonController@edit')->name('kasbon.edit');
            Route::patch('/kasbon/{kasbon}/update', 'KasbonController@update')->name('kasbon.update');
            Route::get('/members/{member}/bayar', 'KasbonController@bayar')->name('kasbon.bayar');
            Route::post('/kasbon/bayarStore', 'KasbonController@bayarStore')->name('kasbon.bayarStore');

            // tunjangan
            Route::get('/members/{member}/tunjangan', 'TunjanganController@create')->name('tunjangan.create');
            Route::post('/tunjangan/create', 'TunjanganController@store')->name('tunjangan.store');
            Route::get('/tunjangan/{tunjangan}/edit', 'TunjanganController@edit')->name('tunjangan.edit');
            Route::patch('/tunjangan/{tunjangan}/update', 'TunjanganController@update')->name('tunjangan.update');

            //level
            Route::resource('level', 'LevelController');

            //bagian
            Route::resource('bagian', 'BagianController');

            // gaji
            Route::get('/members/{member}/gaji', 'GajiController@create')->name('gaji.create');
            Route::post('/gaji/create', 'GajiController@store')->name('gaji.store');

            // penggajian
            Route::get('/members/{member}/penggajian', 'PenggajianController@create')->name('penggajian.create');
            Route::get('/penggajian/{penggajian}/slip', 'PenggajianController@slip')->name('penggajian.slip');
            Route::post('/penggajian/create', 'PenggajianController@store')->name('penggajian.store');

            // produk
            Route::get('/kategori/{kategori}/produk', 'ProdukController@index')->name('produks.index');
            Route::get('/kategori/{kategori}/produk/create', 'ProdukController@create')->name('produks.create');
            Route::post('/produk', 'ProdukController@store')->name('produks.store');
            Route::get('/produk/{produk}/edit', 'ProdukController@edit')->name('produks.edit');
            Route::patch('/produk/{produk}/update', 'ProdukController@update')->name('produks.update');
            Route::get('/aset', 'ProdukController@aset')->name('produk.aset');

            //produkStoks
            Route::get('/produk/{produk}/produkStok', 'ProdukStokController@index')->name('produkStok.index');
            Route::get('/produk/{produk}/produk/create', 'ProdukStokController@create')->name('produkStok.create');
            Route::post('/produkStok', 'ProdukStokController@store')->name('produkStok.store');

            // kontak
            Route::resource('kontaks', 'KontakController');

            // produksi
            Route::resource('produksis', 'ProduksiController');

            // speks
            Route::resource('speks', 'SpekController');

            // ars
            Route::resource('ars', 'ArController');

            //kategori
            Route::get('/kategori', 'KategoriController@index')->name('kategori.index');
            Route::get('/kategori/create', 'KategoriController@create')->name('kategori.create');
            Route::post('/kategori', 'KategoriController@store')->name('kategori.store');
            Route::get('/kategori/{kategori}/edit', 'KategoriController@edit')->name('kategori.edit');
            Route::patch('/kategori/{kategori}/update', 'KategoriController@update')->name('kategori.update');

            // sistem
            Route::get('/sistem', 'SistemController@index')->name('sistem.index');
            Route::get('/sistem/create', 'SistemController@create')->name('sistem.create');
            Route::post('/sistem', 'SistemController@store')->name('sistem.store');
            Route::get('/sistem/edit', 'SistemController@edit')->name('sistem.edit');
            Route::post('/sistem/update', 'SistemController@update')->name('sistem.update');

            // belanja
            Route::get('/belanja', 'BelanjaController@index')->name('belanja.index');
            Route::get('/belanja/create', 'BelanjaController@create')->name('belanja.create');
            Route::post('/belanja', 'BelanjaController@store')->name('belanja.store');
            Route::get('/belanja/{belanja}', 'BelanjaController@detail')->name('belanja.detail');

            //order
            Route::get('/order', 'OrderController@index')->name('order.index');
            Route::get('/order/create', 'OrderController@create')->name('order.create');
            Route::post('/order', 'OrderController@store')->name('order.store');
            Route::get('/konsumen/api', 'OrderController@apiKonsumen')->name('order.konsumen');
            Route::get('/supplier/api', 'OrderController@apiSupplier')->name('order.supplier');
            Route::get('/produk/api', 'OrderController@apiProduk')->name('order.produk');
            Route::get('/produkBeli/api', 'OrderController@apiProdukBeli')->name('order.produkBeli');
            Route::get('/order/dashboard', 'OrderController@dashboard')->name('order.dashboard');
            Route::get('/order/{order}/edit', 'OrderController@edit')->name('order.edit');
            Route::patch('/order/{order}/update', 'OrderController@update')->name('order.update');
            Route::get('/order/{order}/invoice', 'OrderController@invoice')->name('order.invoice');
            Route::get('/order/belumLunas', 'OrderController@unpaid')->name('order.unpaid');
            Route::get('/order/{order}/bayar', 'OrderController@bayar')->name('order.bayar');
            Route::post('/order/bayar', 'OrderController@storeBayar')->name('order.storeBayar');
            Route::post('/order/{order}/chat', 'OrderController@storeChat')->name('order.chatStore');
            Route::get('/order/omzet', 'OrderController@omzet')->name('order.omzet');
            Route::get('/order/omzetBulan', 'OrderController@omzetBulan')->name('order.omzetBulan');
            Route::get('/order/arsip', 'OrderController@arsip')->name('order.arsip');

            //order detail
            Route::get('/order/{order}/detail', 'OrderDetailController@index')->name('order.detail');
            Route::get('/order/{order}', 'OrderDetailController@create')->name('orderDetail.add');
            Route::post('/orderDetail/create', 'OrderDetailController@store')->name('orderDetail.store');
            Route::get('/orderDetail/{detail}/gambar', 'OrderDetailController@gambar')->name('orderDetail.gambar');
            Route::post('/orderDetail/upload', 'OrderDetailController@upload')->name('orderDetail.upload');
            Route::patch('/orderDetail/{detail}/status', 'OrderDetailController@updateStatus')->name('orderDetail.status');
            Route::get('/orderDetail/{detail}/edit', 'OrderDetailController@edit')->name('orderDetail.edit');
            Route::patch('/orderDetail/{detail}/update', 'OrderDetailController@update')->name('orderDetail.update');
            Route::get('/orderDetail/{detail}/editGambar', 'OrderDetailController@editGambar')->name('orderDetail.editGambar');
            Route::patch('/orderDetail/{detail}/updateGambar', 'OrderDetailController@updateGambar')->name('orderDetail.updateGambar');

            //marketplaces
            Route::resource('marketplaces', 'MarketplaceController');
            Route::post('/marketplaces/{id}/uploadKeuangan', 'MarketplaceController@uploadKeuangan')->name('marketplaces.uploadKeuangan');
            Route::post('/marketplaces/{id}/uploadOrder', 'MarketplaceController@uploadOrder')->name('marketplaces.uploadOrder');
        });
    });
});

