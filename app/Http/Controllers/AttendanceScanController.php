<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Services\AbsensiHrService;
use App\Services\UserDeviceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AttendanceScanController extends Controller
{
    public function __construct(
        protected AbsensiHrService $absensiHrService,
        protected UserDeviceService $userDeviceService
    ) {}

    public function scan()
    {
        $user = Auth::user();
        $member = $this->resolveActiveMember($user);

        if (! $member) {
            return redirect()->route('dashboard')
                ->withErrors(['Akun Anda belum terhubung ke data Member aktif. Hubungi admin.']);
        }

        if (! $this->userDeviceService->validateToken(request(), $user)) {
            return redirect()->route('dashboard')
                ->withErrors(['Perangkat tidak terdaftar untuk absensi. Login ulang di HP Anda atau hubungi admin untuk reset perangkat.']);
        }

        $today = now()->toDateString();
        $userId = $user->id;

        $clockInToday = Attendance::where('user_id', $userId)
            ->where('attendance_date', $today)
            ->where('type', 'clock_in')
            ->first();

        $clockOutToday = Attendance::where('user_id', $userId)
            ->where('attendance_date', $today)
            ->where('type', 'clock_out')
            ->first();

        $history = Attendance::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $currentMonth = date('m');
        $currentYear = date('Y');

        $attendancesThisMonth = Attendance::where('user_id', $userId)
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear)
            ->get();

        $stats = [
            'hadir' => $attendancesThisMonth->whereIn('status', ['hadir', 'bekerja', 'terlambat'])->unique('attendance_date')->count(),
            'terlambat' => $attendancesThisMonth->where('status', 'terlambat')->count(),
            'absen' => $attendancesThisMonth->where('status', 'absen')->count(),
        ];

        return view('absensi.scan', compact(
            'clockInToday',
            'clockOutToday',
            'history',
            'stats',
            'member'
        ));
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();
        $member = $this->resolveActiveMember($user);

        if (! $member) {
            return redirect()->route('dashboard')
                ->withErrors(['Akun Anda belum terhubung ke data Member aktif. Hubungi admin.']);
        }

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('attendance_date', $bulan)
            ->whereYear('attendance_date', $tahun)
            ->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc')
            ->paginate(30)
            ->withQueryString();

        return view('absensi.riwayat', compact('attendances', 'bulan', 'tahun', 'member'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'qr_code_result' => 'required|string',
        ]);

        $user = Auth::user();
        $member = $this->resolveActiveMember($user);

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum terhubung ke data Member aktif.',
            ]);
        }

        if (! $this->userDeviceService->validateToken($request, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak terdaftar. Login ulang di HP Anda atau hubungi admin untuk reset perangkat.',
            ], 403);
        }

        $secretCode = config('company.qr_code_secret');
        if ($request->qr_code_result !== $secretCode) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid!']);
        }

        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            config('company.office_latitude'),
            config('company.office_longitude')
        );

        if ($distance > config('company.max_distance_radius')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius kantor yang diizinkan. Jarak Anda: '.round($distance).' meter.',
            ]);
        }

        $today = now()->toDateString();
        $currentTime = now();

        $scheduledClockIn = Carbon::createFromTimeString(config('company.clock_in_time', '08:00:00'));
        $scheduledClockOut = Carbon::createFromTimeString(config('company.clock_out_time', '17:00:00'));

        $clockIn = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->where('type', 'clock_in')
            ->first();

        $clockOut = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->where('type', 'clock_out')
            ->first();

        if (! $clockIn) {
            if ($currentTime->isAfter($scheduledClockOut)) {
                return response()->json(['success' => false, 'message' => 'Sesi absensi untuk hari ini telah ditutup.']);
            }

            $lateThreshold = $scheduledClockIn->copy()->addMinutes(config('company.late_tolerance_minutes', 15));
            $minutesLate = $currentTime->isAfter($lateThreshold)
                ? $currentTime->diffInMinutes($scheduledClockIn)
                : 0;

            $status = 'bekerja';
            $message = 'Absen Masuk berhasil (Status: Bekerja).';
            $notificationMessage = "✅ *ABSEN MASUK*\n\n*Nama:* {$user->name}\n*Waktu:* {$currentTime->format('H:i:s')} WIB";

            if ($minutesLate > 30) {
                $status = 'terlambat';
                $message = 'Absen Masuk berhasil (Status: Terlambat).';
                $notificationMessage = "⏰ *ABSEN MASUK (TERLAMBAT)*\n\n*Nama:* {$user->name}\n*Waktu:* {$currentTime->format('H:i:s')} WIB\n*Keterangan:* Terlambat {$minutesLate} menit.";
            }

            Attendance::create([
                'user_id' => $user->id,
                'type' => 'clock_in',
                'status' => $status,
                'attendance_date' => $today,
                'attendance_time' => $currentTime->toTimeString(),
                'minutes_late' => $minutesLate,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            $jenis = $minutesLate > 0 ? 'terlambat' : 'hadir';
            $keterangan = $minutesLate > 0 ? "Terlambat {$minutesLate} menit" : null;

            if (($cutiIjin = $this->absensiHrService->getCutiAtauIjinDariModel($member->id, $today))) {
                $jenis = $cutiIjin['jenis'];
                $keterangan = $cutiIjin['keterangan'];
            }

            $this->absensiHrService->recordFromScan($member, [
                'tanggal' => $today,
                'jenis' => $jenis,
                'keterangan' => $keterangan,
                'sumber' => 'scan',
                'minutes_late' => $minutesLate,
                'jam_masuk' => $currentTime->toTimeString(),
            ]);

            $this->sendWhatsAppNotification($notificationMessage);

            return response()->json(['success' => true, 'message' => $message]);
        }

        if (! $clockOut) {
            if ($currentTime->isBefore($scheduledClockOut)) {
                return response()->json(['success' => false, 'message' => 'Belum waktunya absen pulang.']);
            }

            Attendance::create([
                'user_id' => $user->id,
                'type' => 'clock_out',
                'status' => 'pulang',
                'attendance_date' => $today,
                'attendance_time' => $currentTime->toTimeString(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            if ($clockIn->status === 'bekerja') {
                $clockIn->update(['status' => 'hadir']);
            }

            $notificationMessage = "🚪 *ABSEN PULANG*\n\n*Nama:* {$user->name}\n*Waktu:* {$currentTime->format('H:i:s')} WIB";
            $this->sendWhatsAppNotification($notificationMessage);

            return response()->json(['success' => true, 'message' => 'Absen Pulang berhasil dicatat!']);
        }

        return response()->json(['success' => false, 'message' => 'Anda sudah lengkap absen masuk dan pulang hari ini.']);
    }

    protected function resolveActiveMember($user): ?Member
    {
        return Member::where('user_id', $user->id)
            ->where('status', 1)
            ->first();
    }

    private function sendWhatsAppNotification(string $message): void
    {
        $token = config('company.fonnte_token');
        $target = config('company.whatsapp_group_target');

        if (! $token || ! $target) {
            Log::warning('Fonnte token atau target grup WhatsApp tidak diatur.');

            return;
        }

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'target' => $target,
                    'message' => $message,
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: '.$token,
                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                Log::error('Gagal mengirim WA via cURL: '.curl_error($curl));
            }

            curl_close($curl);

            Log::info('Respons dari Fonnte (cURL): '.$response);
        } catch (\Exception $e) {
            Log::error('Exception saat mengirim WA: '.$e->getMessage());
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }

        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
