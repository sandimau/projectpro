<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Cuti;
use App\Models\FreelanceTagihan;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class AbsensiHrService
{
    /**
     * Simpan absensi dari scan clock-in; upsert jika sudah ada record scan/hadir.
     */
    public function recordFromScan(Member $member, array $data): bool
    {
        $existing = Absensi::where('member_id', $member->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->first();

        if ($existing) {
            if ($existing->sumber === 'manual' && ! in_array($existing->jenis, ['hadir', 'terlambat'], true)) {
                return true;
            }

            $existing->update([
                'jenis' => $data['jenis'],
                'keterangan' => $data['keterangan'] ?? null,
                'sumber' => $data['sumber'] ?? 'scan',
                'minutes_late' => $data['minutes_late'] ?? null,
                'jam_masuk' => $data['jam_masuk'] ?? null,
            ]);

            return true;
        }

        return $this->createAbsensi($member, $data);
    }

    /**
     * Simpan absensi manual atau dari API (skip jika duplikat).
     */
    public function recordIfNotExists(Member $member, array $data): bool
    {
        $exists = Absensi::where('member_id', $member->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->exists();

        if ($exists) {
            return false;
        }

        return $this->createAbsensi($member, $data);
    }

    public function getCutiAtauIjinDariModel(int $memberId, string $tanggal): ?array
    {
        $cuti = Cuti::where('member_id', $memberId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if (! $cuti) {
            return null;
        }

        return [
            'jenis' => (int) $cuti->cuti === 1 ? 'cuti' : 'ijin',
            'keterangan' => $cuti->keterangan,
        ];
    }

    protected function createAbsensi(Member $member, array $data): bool
    {
        return DB::transaction(function () use ($member, $data) {
            $absensi = Absensi::create([
                'member_id' => $member->id,
                'tanggal' => $data['tanggal'],
                'jenis' => $data['jenis'],
                'keterangan' => $data['keterangan'] ?? null,
                'sumber' => $data['sumber'] ?? 'manual',
                'minutes_late' => $data['minutes_late'] ?? null,
                'jam_masuk' => $data['jam_masuk'] ?? null,
            ]);

            if ($member->jenis === 'freelance' && $member->upah) {
                FreelanceTagihan::create([
                    'member_id' => $member->id,
                    'absensi_id' => $absensi->id,
                    'tanggal' => $data['tanggal'],
                    'nominal_upah' => (int) $member->upah,
                    'dibayar' => 'belum',
                    'keterangan' => 'Tagihan upah harian - absensi '.$data['jenis'],
                ]);
            }

            return true;
        });
    }
}
