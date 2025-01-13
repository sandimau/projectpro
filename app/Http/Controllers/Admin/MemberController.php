<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Gaji;
use App\Models\Kasbon;
use App\Models\Lembur;
use App\Models\Member;
use App\Models\Penggajian;
use App\Models\Tunjangan;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{

    public function index()
    {
        $members = Member::with(['user'])->aktif()->orderBy('id','asc')->get();

        return view('admin.members.index', compact('members'));
    }

    public function nonaktif()
    {
        $members = Member::with(['user'])->nonaktif()->orderBy('id','desc')->get();
        return view('admin.members.nonaktif', compact('members'));
    }

    public function create()
    {
        $users = User::pluck('name', 'id');
        for ($i = 1; $i < 32; $i++) {
            $num = (string) $i;
            if (strlen($num) == 1) {
                $num = '0' . $num;
                $tglGaji[$num] = $num;
            }
            $tglGaji[(string) $num] = $num;
        }
        return view('admin.members.create', compact('users','tglGaji'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'no_telp' => 'required',
            'status' => 'required',
        ]);

        $member = Member::create($request->all());

        return redirect()->route('members.index')->withSuccess(__('Member created successfully.'));
    }

    public function edit(Member $member)
    {
        $users = User::pluck('name', 'id');
        for ($i = 1; $i < 32; $i++) {
            $num = (string) $i;
            if (strlen($num) == 1) {
                $num = '0' . $num;
                $tglGaji[$num] = $num;
            }
            $tglGaji[(string) $num] = $num;
        }

        $member->load('user');

        return view('admin.members.edit', compact('member', 'users','tglGaji'));
    }

    public function update(Request $request, Member $member)
    {
        $member->update($request->all());

        return redirect()->route('members.index')->withSuccess(__('Member created successfully.'));
    }

    public function show(Member $member)
    {
        $member->load('user');

        $cutis = Cuti::where('member_id', $member->id)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        $lemburs = Lembur::where('member_id', $member->id)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        $kasbons = Kasbon::where('member_id', $member->id)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        $tunjangans = Tunjangan::where('member_id', $member->id)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        $gajis = Gaji::where('member_id', $member->id)->with(['member', 'bagian', 'level'])->orderBy('id','desc')->paginate(10);
        $penggajians = Penggajian::where('member_id', $member->id)->orderBy('id','desc')->paginate(10);
        $gajian = Penggajian::where('member_id', $member->id)->latest('id')->first();
        return view('admin.members.show', compact('member', 'cutis', 'lemburs', 'kasbons', 'tunjangans', 'gajis', 'penggajians', 'gajian'));
    }

    public function destroy(Member $member)
    {
        $member->delete();

        return back();
    }

    public function cuti(Member $member)
    {
        $cutis = Cuti::where('member_id', $member->id)->where('cuti', 1)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        return view('admin.members.cuti', compact('cutis','member'));
    }

    public function ijin(Member $member)
    {
        $cutis = Cuti::where('member_id', $member->id)->where('cuti', 0)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        return view('admin.members.ijin', compact('cutis','member'));
    }

    public function kasbon(Member $member)
    {
        $kasbons = Kasbon::where('member_id', $member->id)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        return view('admin.members.kasbon', compact('kasbons','member'));
    }

    public function lembur(Member $member)
    {
        $lemburs = Lembur::where('member_id', $member->id)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        return view('admin.members.lembur', compact('lemburs','member'));
    }

    public function tunjangan(Member $member)
    {
        $tunjangans = Tunjangan::where('member_id', $member->id)->orderBy('created_at', 'desc')->orderBy('id','desc')->paginate(10);
        return view('admin.members.tunjangan', compact('tunjangans','member'));
    }

    public function penggajian(Member $member)
    {
        $gajis = Gaji::where('member_id', $member->id)->with(['member', 'bagian', 'level'])->orderBy('id','desc')->paginate(10);
        $penggajians = Penggajian::where('member_id', $member->id)->orderBy('id','desc')->paginate(10);
        $gajian = Penggajian::where('member_id', $member->id)->latest('id')->first();
        return view('admin.members.penggajian', compact('gajis','penggajians','gajian','member'));
    }

    public function gaji(Member $member)
    {
        $gajis = Gaji::where('member_id', $member->id)->with(['member', 'bagian', 'level'])->orderBy('id','desc')->paginate(10);
        return view('admin.members.gaji', compact('gajis','member'));
    }

}
