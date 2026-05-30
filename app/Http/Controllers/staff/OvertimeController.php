<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index()
    {
        $idKaryawan = Auth::user()->id_kry;

        $overtimes = Lembur::where('id_kry', $idKaryawan)
            ->orderBy('tgl_lbr', 'desc')
            ->get();

        $thisMonthOT = Lembur::where('id_kry', $idKaryawan)
            ->whereMonth('tgl_lbr', now()->month)
            ->whereYear('tgl_lbr', now()->year) 
            ->where('sts_lbr', 'disetujui')
            ->sum('qty_jam');

        return view('staff.overtime', compact('overtimes', 'thisMonthOT'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date',
            'duration' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string|max:1000',
            'proof' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = null;

        if ($request->hasFile('proof')) {
            $path = $request->file('proof')->store('bukti_lembur', 'public');
        }

        Lembur::create([
            'id_kry' => Auth::user()->id_kry,
            'tgl_lbr' => $request->work_date,
            'qty_jam' => $request->duration,
            'keterangan' => $request->reason,
            'bukti_foto' => $path,
            'sts_lbr' => 'menunggu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan lembur berhasil dikirim'
        ]);
    }
}