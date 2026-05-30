<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\SlipGaji;
use App\Models\Pinjaman;
use App\Models\Lembur;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id_kry;

        $announcements = Announcement::with('karyawan')
            ->latest('created_at')
            ->get();

        // Slip Gaji terbaru
        $latestSlip = SlipGaji::where('id_kry', $userId)
            ->latest()
            ->first();

        // Kasbon terbaru
        $latestPinjaman = Pinjaman::where('karyawan_id', $userId)
            ->latest()
            ->first();

        // Lembur terbaru
        $latestLembur = Lembur::where('id_kry', $userId)
            ->latest()
            ->first();

        return view('staff.dashboard', compact(
            'announcements',
            'latestSlip',
            'latestPinjaman',
            'latestLembur'
        ));
    }

    public function markAllNotificationsRead()
    {
        Notification::where('id_kry', Auth::user()->id_kry)
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function markNotificationRead($id)
    {
        $notification = Notification::where('id_notification', $id)
            ->where('id_kry', Auth::user()->id_kry)
            ->first();

        if ($notification) {
            $notification->update([
                'is_read' => true
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function deleteNotification($id)
    {
        $notification = Notification::where('id_notification', $id)
            ->where('id_kry', Auth::user()->id_kry)
            ->first();

        if ($notification) {
            $notification->delete();
        }

        return response()->json([
            'success' => true
        ]);
    }
}