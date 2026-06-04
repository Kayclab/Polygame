<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\karyawan;
use App\Models\SlipGaji;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\Pinjaman;
use App\Models\Evaluasi;
use App\Models\Lembur;

class AnnouncementController extends Controller
{
    public function index()
    {
        // SEKARANG DINAMIS: Mengambil waktu lokal saat ini dengan format 'YYYY-MM' (Contoh: '2026-05')
        $currentPeriod = Carbon::now()->format('Y-m');

        // 1. DATA KARYAWAN & PERTUMBUHAN
        $employees = karyawan::where('role', 'staff')->get();
        $totalEmployees = $employees->count();

        // Menggunakan Carbon untuk mencari awal bulan ini secara otomatis
        $previousCount = karyawan::where('role', 'staff')
            ->where('created_at', '<', Carbon::now()->startOfMonth())
            ->count();

        $growthPercentage = 0;
        if ($previousCount > 0) {
            $growthPercentage = (($totalEmployees - $previousCount) / $previousCount) * 100;
        }

        // 2. LOGIKA KARTU RINGKASAN (Mengikuti bulan berjalan secara otomatis)
        $slips = SlipGaji::where('periode', $currentPeriod)->get();

        // Hitung Monthly Payroll
        $monthlyPayroll = $slips->where('status', 'terkirim')->sum('total_gaji');

        // Hitung Pending Reviews
        $publishedSlipsCount = $slips->where('status', 'terkirim')->count();
        $pendingReviews = $totalEmployees - $publishedSlipsCount;

        // 3. DATA ANNOUNCEMENT
        $announcements = Announcement::with('karyawan')->latest()->get();

        // 4. DATA PINJAMAN BULAN INI
        // Dashboard hanya menghitung pinjaman yang disetujui pada bulan berjalan.
        // Jadi ketika masuk bulan baru, nilai pinjaman otomatis kembali 0.
        $pinjamans = Pinjaman::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->get();

        $totalPinjamanBulanan = $pinjamans->sum('total');

        $avgEvaluasi = Evaluasi::avg('skor_total') ?? 0;

        $lemburs = Lembur::all();

        $totalLemburPending = Lembur::where('sts_lbr', 'menunggu')
            ->sum('qty_jam');

        try {
            DB::connection()->getPdo();
            $systemHealth = 100;
            $systemStatus = 'OPERATIONAL';
        } catch (\Exception $e) {
            $systemHealth = 0;
            $systemStatus = 'CRITICAL ERROR';
        }

        return view('owner.dashboard', compact(
            'employees',
            'totalEmployees',
            'growthPercentage',
            'announcements',
            'monthlyPayroll',
            'pendingReviews',
            'currentPeriod',
            'systemHealth',
            'systemStatus',
            'pinjamans',
            'totalPinjamanBulanan',
            'avgEvaluasi',
            'totalLemburPending'
        ));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // AMBIL ID KARYAWAN DARI ADMIN YANG SEDANG LOGIN SECARA OTOMATIS
        $validated['id_kry'] = Auth::user()->id_kry;

        Announcement::create($validated);
        $staffs = karyawan::where('role', 'staff')->get();

        foreach ($staffs as $staff) {
            Notification::create([
                'id_kry' => $staff->id_kry,
                'title' => 'Pengumuman Baru',
                'message' => $validated['title'],
                'type' => 'announcement',
                'priority' => 'medium',
            ]);
        }

        return redirect()->back()->with('success', 'Pengumuman berhasil diterbitkan!');
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement->update($validated);

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui!');
    }

    /**
     * Hapus pengumuman dari database
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus!');
    }
}
