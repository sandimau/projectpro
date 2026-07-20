<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Services\AbsensiHrService;
use Illuminate\Support\Facades\Artisan;

class AbsensiController extends Controller
{
    public function __construct(
        protected AbsensiHrService $absensiHrService
    ) {}

    /**
     * @deprecated Digantikan penyimpanan lokal via scan absensi. Tetap tersedia untuk migrasi data lama.
     */
    public function syncFromApi()
    {
        $apiUrls = config('services.absensi.api_urls', []);
        if ($apiUrls === []) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada URL API absensi yang dikonfigurasi (ABSENSI_API_URLS)',
                'saved' => 0,
            ], 500);
        }

        $attendances = [];
        $errors = [];

        foreach ($apiUrls as $url) {
            $response = Http::timeout(30)->get($url);
            if (! $response->successful()) {
                $errors[] = "{$url}: HTTP {$response->status()}";
                continue;
            }

            $data = $response->json();
            $rows = $data['attendances'] ?? [];
            if (! is_array($rows)) {
                $errors[] = "{$url}: format response tidak valid";
                continue;
            }

            $attendances = array_merge($attendances, $rows);
        }

        if ($attendances === [] && $errors !== []) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari semua API absensi',
                'errors' => $errors,
                'saved' => 0,
            ], 502);
        }

        $saved = 0;
        $allowedJenis = ['sakit', 'ijin', 'terlambat', 'cuti', 'alpha', 'hadir'];

        // Kelompokkan per user email + tanggal (satu absensi per member per hari)
        // Jika ada beberapa clock_in, prioritas: yang terlambat (minutes_late > 0), else ambil yang pertama
        $byMemberAndDate = [];
        foreach ($attendances as $row) {
            $userEmail = $row['user']['email'] ?? null;
            if (! $userEmail) {
                continue;
            }
            $tanggal = Carbon::parse($row['attendance_date'])->format('Y-m-d');
            $key = $userEmail.'|'.$tanggal;
            $minutesLate = (float) ($row['minutes_late'] ?? 0);
            if (! isset($byMemberAndDate[$key]) || $minutesLate > 0) {
                $byMemberAndDate[$key] = $row;
                $byMemberAndDate[$key]['_tanggal'] = $tanggal;
            }
        }

        foreach ($byMemberAndDate as $row) {
            $userEmail = $row['user']['email'] ?? null;
            if (! $userEmail) {
                continue;
            }
            $member = Member::whereHas('user', function ($q) use ($userEmail) {
                $q->where('email', $userEmail);
            })->first();
            if (! $member) {
                continue;
            }

            $tanggal = $row['_tanggal'] ?? Carbon::parse($row['attendance_date'])->format('Y-m-d');
            $jamMasuk = $row['attendance_time'] ?? null;
            $minutesLate = (float) ($row['minutes_late'] ?? 0);
            $status = $row['status'] ?? null;

            if ($minutesLate > 0) {
                $jenis = 'terlambat';
                $keterangan = "Terlambat {$minutesLate} menit";
            } elseif (($cutiIjin = $this->absensiHrService->getCutiAtauIjinDariModel($member->id, $tanggal))) {
                $jenis = $cutiIjin['jenis'];
                $keterangan = $cutiIjin['keterangan'];
            } elseif (in_array($status, ['sakit', 'alpha'], true)) {
                $jenis = $status;
                $keterangan = $row['keterangan'] ?? null;
            } else {
                $jenis = 'hadir';
                $keterangan = $row['keterangan'] ?? null;
            }

            if (! in_array($jenis, $allowedJenis, true)) {
                $jenis = 'hadir';
            }

            $result = $this->absensiHrService->recordIfNotExists($member, [
                'tanggal' => $tanggal,
                'jenis' => $jenis,
                'keterangan' => $keterangan,
                'sumber' => 'api',
                'minutes_late' => $minutesLate,
                'jam_masuk' => $jamMasuk,
            ]);
            if ($result) {
                $saved++;
            }
        }

        $message = "{$saved} absensi berhasil disimpan";
        if ($errors !== []) {
            $message .= ' ('.count($errors).' sumber gagal diambil)';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'saved' => $saved,
            'sources' => count($apiUrls),
            'errors' => $errors,
        ]);
    }

    public function index(Request $request)
    {
        $query = Absensi::with('member')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->member_id) {
            $query->where('member_id', $request->member_id);
        }
        if ($request->bulan) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $absensis = $query->paginate(20);
        $members = Member::where('status', 1)->orderBy('nama_lengkap')->get();

        return view('admin.absensi.index', compact('absensis', 'members'));
    }

    public function create()
    {
        $members = Member::where('status', 1)->orderBy('nama_lengkap')->get();
        return view('admin.absensi.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'tanggal' => 'required|date',
            'jenis' => 'required|in:sakit,ijin,terlambat,cuti,alpha,hadir',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $member = Member::findOrFail($validated['member_id']);

        $saved = $this->absensiHrService->recordIfNotExists($member, [
            'tanggal' => $validated['tanggal'],
            'jenis' => $validated['jenis'],
            'keterangan' => $validated['keterangan'] ?? null,
            'sumber' => 'manual',
        ]);

        if (! $saved) {
            return back()->withInput()->withErrors(['tanggal' => 'Absensi untuk member dan tanggal ini sudah ada.']);
        }

        return redirect()->route('absensi.index')->withSuccess(__('Absensi berhasil disimpan.'));
    }

    public function settings()
    {
        $settings = config('company');

        return view('admin.absensi.settings', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'clock_in_time' => 'required|string',
            'clock_out_time' => 'required|string',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'office_latitude' => 'required|numeric',
            'office_longitude' => 'required|numeric',
            'max_distance_radius' => 'required|integer|min:1',
            'qr_code_secret' => 'required|string',
            'fonnte_token' => 'nullable|string',
            'whatsapp_group_target' => 'nullable|string',
        ]);

        $lines = ["<?php\n", "return [\n"];
        foreach ($validated as $key => $value) {
            if (in_array($key, ['late_tolerance_minutes', 'max_distance_radius'], true)) {
                $lines[] = "    '{$key}' => ".(int) $value.",\n";
            } elseif (in_array($key, ['office_latitude', 'office_longitude'], true)) {
                $lines[] = "    '{$key}' => {$value},\n";
            } else {
                $lines[] = "    '{$key}' => ".var_export($value, true).",\n";
            }
        }
        $lines[] = "];\n";

        file_put_contents(config_path('company.php'), implode('', $lines));
        Artisan::call('config:clear');

        return back()->withSuccess(__('Pengaturan absensi berhasil diperbarui.'));
    }

    public function destroy(Absensi $absensi)
    {
        // Hapus freelance tagihan jika ada
        $absensi->freelanceTagihan?->delete();
        $absensi->delete();

        return back()->withSuccess(__('Absensi berhasil dihapus.'));
    }
}
