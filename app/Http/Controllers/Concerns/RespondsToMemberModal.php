<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

trait RespondsToMemberModal
{
    protected function memberModalValidationMessages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'date' => 'Format :attribute tidak valid.',
            'string' => ':attribute harus berupa teks.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'min.numeric' => ':attribute minimal :min.',
            'max.numeric' => ':attribute maksimal :max.',
            'in' => 'Pilihan :attribute tidak valid.',
            'exists' => ':attribute tidak valid.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'ket.required' => 'Keterangan wajib diisi.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'no_telp.required' => 'No. telepon wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'jenis.required' => 'Jenis wajib dipilih.',
            'member_id.required' => 'Data member tidak ditemukan.',
            'member_id.exists' => 'Data member tidak valid.',
            'cuti.required' => 'Silakan pilih cuti atau ijin.',
            'cuti.in' => 'Pilihan cuti/ijin tidak valid.',
            'bagian_id.required' => 'Bagian wajib dipilih.',
            'level_id.required' => 'Level wajib dipilih.',
            'performance.required' => 'Performance wajib dipilih.',
            'performance.in' => 'Performance wajib dipilih.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'akun_detail_id.required' => 'Kas/rekening wajib dipilih.',
            'akun_detail_id.exists' => 'Kas/rekening tidak valid.',
            'jam.required' => 'Jam lembur wajib diisi.',
            'jam.numeric' => 'Jam lembur harus berupa angka.',
            'isi.required' => 'Isi tugas wajib diisi.',
            'freelance_tagihan_id.required' => 'Data tagihan tidak ditemukan.',
            'jumlah_hari.required' => 'Jumlah hari wajib diisi.',
        ];
    }

    protected function validateMemberModal(Request $request, array $rules): array
    {
        return $request->validate($rules, $this->memberModalValidationMessages());
    }

    protected function memberModalResponse(Request $request, string $message, string $redirectUrl): JsonResponse|RedirectResponse
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect($redirectUrl)->withSuccess($message);
    }
}
