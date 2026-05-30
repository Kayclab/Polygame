<?php

namespace App\Http\Controllers;

use App\Models\SlipGaji;
use App\Models\karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use Carbon\Carbon;

class SlipGajiController extends Controller
{
    public function index()
    {
        $employees = karyawan::where('role', 'staff')->get();
        $slips = SlipGaji::with('karyawan')->latest()->get();

        return view('owner.payroll', compact('employees', 'slips'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kry'     => 'required',
            'periode'    => 'required',
            'total_gaji' => 'required|numeric',
            'file_slip'  => 'required|file|mimes:pdf|max:10240',
        ]);

        try {
            $filePath = null;

            if ($request->hasFile('file_slip')) {
                $file = $request->file('file_slip');
                $months = [
                    '01' => 'januari', '02' => 'februari', '03' => 'maret', '04' => 'april',
                    '05' => 'mei', '06' => 'juni', '07' => 'juli', '08' => 'agustus',
                    '09' => 'september', '10' => 'oktober', '11' => 'november', '12' => 'desember',
                ];

                [$year, $month] = explode('-', $request->periode);
                $fileName = 'slip-gaji-' . $months[$month] . '-' . $year . '.pdf';
                $filePath = $file->storeAs('slip-gaji', $fileName, 'public');
            }

            SlipGaji::create([
                'id_kry'     => $request->id_kry,
                'periode'    => $request->periode,
                'gaji_pokok' => 0, 'bonus' => 0, 'potongan' => 0,
                'total_gaji' => $request->total_gaji,
                'file_slip'  => $filePath,
                'status'     => 'terkirim'
            ]);

            $formattedPeriod = Carbon::createFromFormat('Y-m', $request->periode)->translatedFormat('F Y');

            Notification::create([
                'id_kry'   => $request->id_kry,
                'title'    => 'Slip Gaji Tersedia',
                'message'  => 'Slip gaji periode ' . $formattedPeriod . ' telah tersedia.',
                'type'     => 'payroll', 'priority' => 'high',
            ]);

            return redirect()->back()->with('success', 'Slip gaji berhasil diupload');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan ke database: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kry'     => 'required',
            'periode'    => 'required',
            'total_gaji' => 'required|numeric',
            'file_slip'  => 'nullable|file|mimes:pdf|max:10240',
        ]);

        try {
            $slip = SlipGaji::findOrFail($id);
            $slip->total_gaji = $request->total_gaji;

            if ($request->hasFile('file_slip')) {
                if ($slip->file_slip && Storage::disk('public')->exists($slip->file_slip)) {
                    Storage::disk('public')->delete($slip->file_slip);
                }

                $file = $request->file('file_slip');
                $months = [
                    '01' => 'januari', '02' => 'februari', '03' => 'maret', '04' => 'april',
                    '05' => 'mei', '06' => 'juni', '07' => 'juli', '08' => 'agustus',
                    '09' => 'september', '10' => 'oktober', '11' => 'november', '12' => 'desember',
                ];

                [$year, $month] = explode('-', $request->periode);
                $fileName = 'slip-gaji-' . $months[$month] . '-' . $year . '.pdf';
                $slip->file_slip = $file->storeAs('slip-gaji', $fileName, 'public');
            }

            $slip->save();
            $formattedPeriod = Carbon::createFromFormat('Y-m', $request->periode)->translatedFormat('F Y');

            Notification::create([
                'id_kry'   => $slip->id_kry,
                'title'    => 'Slip Gaji Diperbarui',
                'message'  => 'Slip gaji periode ' . $formattedPeriod . ' telah diperbarui.',
                'type'     => 'payroll', 'priority' => 'high',
            ]);

            return redirect()->back()->with('success', 'Slip gaji berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengubah data: ' . $e->getMessage()]);
        }
    }
}