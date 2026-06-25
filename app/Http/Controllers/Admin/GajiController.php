<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsToMemberModal;
use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Models\Gaji;
use App\Models\Level;
use App\Models\Member;
use Illuminate\Http\Request;

class GajiController extends Controller
{
    use RespondsToMemberModal;

    public function create(Member $member)
    {
        $gaji = Gaji::where('member_id', $member->id)->latest('id')->first();
        $bagians = Bagian::pluck('nama', 'id')->prepend('select bagian', '');
        $levels = Level::pluck('nama', 'id')->prepend('select level', '');
        return view('admin.gajis.create', compact('bagians', 'levels','member','gaji'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bagian_id' => 'required',
            'level_id' => 'required',
            'performance' => 'required',
        ]);

        $gaji = Gaji::create([
            'member_id' => $request->member_id,
            'bagian_id' => $request->bagian_id,
            'level_id' => $request->level_id,
            'performance' => $request->performance,
            'transportasi' => $request->transportasi == 'on' ? 1 : 0 ,
            'lain_lain' => $request->lain_lain,
            'jumlah_lain' => $request->jumlah_lain,
        ]);

        return $this->memberModalResponse(
            $request,
            __('Gaji created successfully.'),
            route('members.show', $request->member_id)
        );
    }
}
