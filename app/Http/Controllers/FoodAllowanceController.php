<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\karyawan;
use App\Models\Pinjaman;

class FoodAllowanceController extends Controller
{
    public function index()
    {
        // 1. Batas maksimal kasbon per bulan
        $allowanceCap = 300000;

        // 2. Ambil bulan dan tahun saat ini
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        // 3. Ambil data staff dan hitung akumulasi kasbon approved hanya bulan ini
        // Jadi ketika masuk bulan baru, total peminjaman otomatis tampil dari 0 lagi.
        $staffRanking = karyawan::where('role', 'staff')
            ->get()
            ->map(function ($karyawan) use ($currentMonth, $currentYear) {
                $totalSpent = Pinjaman::where('karyawan_id', $karyawan->id_kry)
                    ->where('status', 'approved')
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->sum('total');

                $karyawan->total_spent = $totalSpent;

                return $karyawan;
            })
            ->sortByDesc('total_spent');

        $menus = Menu::all();

        return view('owner.food_allowance', compact('allowanceCap', 'staffRanking', 'menus'));
    }

    public function store(Request $request)
    {
        // 1. Validasi inputan dari form langsung dengan string pilihan
        $validated = $request->validate([
            'food_name'  => 'required|string|max:255',
            'category'   => 'required|in:Makanan Berat,Minuman,Cemilan',
            'cost_price' => 'required|numeric|min:0',
        ]);

        // 2. Insert data baru ke tabel MySQL
        Menu::create($validated);

        // 3. Redirect kembali ke halaman utama dengan pesan sukses
        return redirect()->route('owner.allowance.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        // 1. Cari data menu berdasarkan ID di database
        $menu = Menu::findOrFail($id);

        // 2. Validasi data yang diubah langsung dengan string pilihan
        $validated = $request->validate([
            'food_name'  => 'required|string|max:255',
            'category'   => 'required|in:Makanan Berat,Minuman,Cemilan',
            'cost_price' => 'required|numeric|min:0',
        ]);

        // 3. Update data di tabel MySQL
        $menu->update($validated);

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->route('owner.allowance.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // 1. Cari datanya terlebih dahulu
        $menu = Menu::findOrFail($id);

        // 2. Hapus data dari tabel MySQL
        $menu->delete();

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('owner.allowance.index')->with('success', 'Menu berhasil dihapus!');
    }
}
