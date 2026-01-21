<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chat;
use App\Models\Order;
use App\Models\Member;
use App\Models\Produksi;
use App\Models\ProjectMp;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use App\Models\ProjectMpDetail;
use App\Models\MarketplaceBuffer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ProjectMpController extends Controller
{
    /**
     * Dashboard untuk order marketplace custom (seperti OrderController dashboard)
     */
    public function dashboard()
    {
        abort_if(Gate::denies('marketplace_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Ambil produksi untuk tab
        $produksi = Produksi::orderBy('urutan')->get();

        // Ambil list marketplace untuk filter
        $mps = ['semua' => 'Semua'];
        $config = Marketplace::pluck('nama', 'nama');
        foreach ($config as $key => $value) {
            $mps[str_replace(' ', '_', $key)] = str_replace(' ', '_', $value);
        }

        return view('admin.projectmps.dashboard', compact('produksi', 'mps'));
    }

    /**
     * Dashboard untuk packing (non-custom) berdasarkan status
     */
    public function packing()
    {
        abort_if(Gate::denies('marketplace_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Ambil data marketplace buffer dengan relasi (packing = non-custom)
        $bufferData = MarketplaceBuffer::detail()->packing()->get();

        // Group data by status dan project_id
        $marketplaces = $this->group2level($bufferData, 'statusMp', 'project_id');

        // Ambil list marketplace untuk filter
        $mps = ['semua' => 'Semua'];
        $config = Marketplace::pluck('nama', 'nama');
        foreach ($config as $key => $value) {
            $mps[str_replace(' ', '_', $key)] = str_replace(' ', '_', $value);
        }

        // Status yang akan ditampilkan sebagai tab
        $statuses = [
            'READY_TO_SHIP' => ['nama' => 'Perlu diProses', 'warna' => '#28a745'],
            'PROCESSED' => ['nama' => 'Telah diproses', 'warna' => '#ffc107'],
        ];

        return view('admin.projectmps.packing', compact('marketplaces', 'mps', 'statuses'));
    }

    /**
     * Group collection by 2 level keys
     */
    private function group2level($obj, $key, $key2)
    {
        $hasil = [];
        foreach ($obj as $detail) {
            $hasil[$detail->$key][$detail->$key2][] = $detail;
        }
        return $hasil;
    }

    public function storeChat(Request $request, ProjectMpDetail $projectmp)
    {
        $member = Member::where('user_id', auth()->user()->id)->first();

        Chat::create([
            'isi' => $request->isi,
            'member_id' => $member->id ?? null,
            'order_id' => $projectmp->id
        ]);
        return redirect('/admin/projectMpDetail/' . $projectmp->id)->withSuccess(__('chat created successfully.'));
    }
}
