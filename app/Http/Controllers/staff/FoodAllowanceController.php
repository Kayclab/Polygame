<?php

namespace App\Http\Controllers\staff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\Auth;

class FoodAllowanceController extends Controller
{
    public function index()
    {
        $menuItem = Menu::all();

        $recentActivity = Pinjaman::where(
            'karyawan_id',
            Auth::user()->id_kry
        )
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->latest()
            ->take(10)
            ->get();

        $totalPinjaman = Pinjaman::where(
            'karyawan_id',
            Auth::user()->id_kry
        )
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total');

        return view('staff.food-allowance', compact(
            'menuItem',
            'recentActivity',
            'totalPinjaman'
        ));
    }

    public function store(Request $request)
    {
        Pinjaman::create([
            'karyawan_id' => Auth::user()->id_kry,
            'type' => $request->type,
            'total' => $request->total,
            'keterangan' => $request->keterangan,
            'tanggal' => $request->tanggal,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}