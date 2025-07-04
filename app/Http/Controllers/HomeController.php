<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Sistem;
use App\Models\Whattodo;
use App\Jobs\DeleteOrders;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tglGaji = Whattodo::where('nama','gaji')->first()->isi;
        $tgl_skr = date('d');
        if ($tglGaji != $tgl_skr) {
            if ($tglGaji < $tgl_skr) {
                $members = Member::where('tgl_gajian', $tgl_skr)->get();
            }
            if ($tglGaji == "31") {
                $members = Member::where('tgl_gajian', $tgl_skr)->get();
            }
            if (!empty($members)) {
                foreach ($members as $row) {
                    $isi = $row->nama_lengkap . " gajian tanggal " . $row->tgl_gajian;
                    Whattodo::create([
                        'isi' => $isi,
                        'nama' => 'gajian'
                    ]);
                }
            }
            $what = Whattodo::where('nama','gaji')->first();
            $what->update([
                'isi' => $tgl_skr
            ]);
        }

        $member = Member::where('user_id', auth()->id())->first();
        if ($member) {
            $whatMember = Whattodo::where('member_id', $member->id)
                ->where('nama', 'tugas')
                ->get();
        }

        $whattodos = Whattodo::where('nama','!=','gaji')->get();
        $sistems = Sistem::get()->pluck('isi', 'nama');
        $whatMember = $whatMember ?? collect(); // Initialize if not set
        return view('admin.whattodos.home', compact('whattodos', 'whatMember'));
    }

    public function create()
    {
        $id = request()->get('member_id');
        $member = Member::find($id);
        return view('admin.whattodos.create', compact('member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'isi' => 'required',
        ]);

        Whattodo::create([
            'isi' => $request->isi,
            'nama' => 'tugas',
            'member_id' => $request->member_id,
            'user_id' => $request->user_id
        ]);

        return redirect('/whattodo')->withSuccess(__('Whattodo created successfully.'));
    }

    public function edit(Whattodo $what)
    {
        return view('admin.whattodos.edit', compact('what'));
    }

    public function update(Whattodo $what, Request $request)
    {
        $what->update($request->all());
        return redirect('/whattodo')->withSuccess(__('Whattodo updated successfully.'));
    }

    public function destroy(Whattodo $what)
    {
        $what->delete();
        return back()->withDanger(__('Whattodo deleted successfully.'));
    }

    public function DeleteOrders()
    {
        $tafio = new DeleteOrders;
        $tafio->deleteOrders();
    }
}
