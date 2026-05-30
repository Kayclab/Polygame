<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use App\Models\karyawan;
use Illuminate\Http\Request;
use App\Models\Notification;

class OvertimeController extends Controller
{
    public function index()
    {
        $allLemburs = Lembur::with('karyawan')->orderBy('tgl_lbr', 'desc')->get();
        $submissionsStatus = [];

        foreach ($allLemburs as $l) {
            $date = $l->tgl_lbr;
            $status = strtolower($l->sts_lbr);

            if (!isset($submissionsStatus[$date]) || $status === 'menunggu') {
                $submissionsStatus[$date] = $status;
            } elseif ($submissionsStatus[$date] !== 'menunggu' && $status === 'ditolak') {
                $submissionsStatus[$date] = 'ditolak';
            }
        }

        return view('owner.overtime', compact('allLemburs', 'submissionsStatus'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak,menunggu',
        ]);

        $status = strtolower($request->status);
        $lembur = Lembur::findOrFail($id);
        $lembur->update(['sts_lbr' => $status]);

        if ($status == 'disetujui') {
            Notification::create([
                'id_kry'   => $lembur->id_kry,
                'title'    => 'Lembur Disetujui',
                'message'  => 'Pengajuan lembur kamu pada tanggal ' . $lembur->tgl_lbr . ' telah disetujui.',
                'type'     => 'info', 'priority' => 'high',
            ]);
        } elseif ($status == 'ditolak') {
            Notification::create([
                'id_kry'   => $lembur->id_kry,
                'title'    => 'Lembur Ditolak',
                'message'  => 'Pengajuan lembur kamu pada tanggal ' . $lembur->tgl_lbr . ' ditolak.',
                'type'     => 'info', 'priority' => 'high',
            ]);
        }

        return response()->json(['message' => 'Status berhasil diperbarui!']);
    }
}