<?php

namespace App\Http\Controllers;

use App\Models\karyawan;
use App\Models\Lembur;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $karyawans = karyawan::latest()->paginate(5);
        $employeeDetails = $this->buildEmployeeDetails($karyawans);

        return view('owner.employees', compact('karyawans', 'employeeDetails'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'n_kry' => 'required',
            'email' => 'required|email|unique:karyawans,email',
            'password' => 'required|min:6',
            'jab' => 'required',
        ]);

        $data['password'] = Hash::make($request->password);

        karyawan::create($data + $request->only([
            'alamat',
            'tmpt_lahir',
            'tgl_lahir',
            'tgl_mulai_kerja',
            'telp',
            'role',
        ]));

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(karyawan $karyawan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(karyawan $karyawan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $karyawan = karyawan::findOrFail($id);

        $data = $request->validate([
            'n_kry' => 'required',
            'email' => 'required|email|unique:karyawans,email,' . $id . ',id_kry',
            'jab' => 'required',
            'telp' => 'nullable',
            'tmpt_lahir' => 'nullable',
            'tgl_lahir' => 'nullable|date',
            'tgl_mulai_kerja' => 'nullable|date',
            'alamat' => 'nullable',
            'role' => 'nullable',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
        ]);

        $passwordChanged = $request->filled('password');

        if ($passwordChanged) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $karyawan->update($data);

        return redirect()->back()->with(
            'success',
            $passwordChanged ? 'Data dan password karyawan berhasil diperbarui' : 'Data karyawan berhasil diperbarui'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        karyawan::destroy($id);

        return redirect()->back()->with('success', 'Karyawan dihapus');
    }

    /**
     * Menyiapkan payload detail per karyawan untuk slide panel di halaman Blade.
     * Data operasional dibuat defensif: jika tabel/kolom belum ada, halaman tetap aman.
     */
    private function buildEmployeeDetails(LengthAwarePaginator $karyawans): array
    {
        $details = [];

        foreach ($karyawans->getCollection() as $karyawan) {
            $id = $karyawan->id_kry;
            $evaluation = $this->getAverageEvaluation($id);
            $loan = $this->getActiveLoanTotal($id);
            $overtime = $this->getOvertimeHours($id);
            $status = $this->getEmployeeStatus($karyawan);

            $details[$id] = [
                'id' => $id,
                'name' => $karyawan->n_kry ?: '-',
                'email' => $karyawan->email ?: '-',
                'role' => $karyawan->jab ?: '-',
                'system_role' => $karyawan->role ?: '-',
                'initial' => $this->initial($karyawan->n_kry),
                'phone' => $karyawan->telp ?: '-',
                'address' => $karyawan->alamat ?: '-',
                'pob' => $karyawan->tmpt_lahir ?: '-',
                'dob' => $this->formatDate($karyawan->tgl_lahir),
                'join_date' => $this->formatDate($karyawan->tgl_mulai_kerja ?: $karyawan->created_at),
                'created_at' => $this->formatDate($karyawan->created_at),
                'updated_at' => $this->formatDate($karyawan->updated_at),
                'rating' => $evaluation['value'],
                'rating_note' => $evaluation['note'],
                'kasbon' => $this->formatRupiah($loan['total']),
                'kasbon_note' => $loan['note'],
                'overtime' => $this->formatHour($overtime['hours']),
                'overtime_note' => $overtime['note'],
                'status' => $status['value'],
                'status_note' => $status['note'],
                'recent_actions' => $this->buildRecentActions($karyawan, $evaluation, $loan, $overtime),
            ];
        }

        return $details;
    }

    private function getAverageEvaluation($employeeId): array
    {
        $tables = [
            'evaluasis',
            'evaluasi',
            'hasil_evaluasis',
            'nilai_evaluasis',
            'penilaian_karyawans',
            'jawaban_evaluasis',
        ];
        $scoreColumns = ['skor', 'score', 'nilai', 'total_skor', 'total_score', 'avg_score', 'rating', 'nilai_akhir', 'hasil'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $idColumn = $this->firstExistingColumn($table, $this->employeeIdColumns());
            $scoreColumn = $this->firstExistingColumn($table, $scoreColumns);

            if (!$idColumn || !$scoreColumn) {
                continue;
            }

            try {
                $query = DB::table($table)->where($idColumn, $employeeId);
                $count = (clone $query)->whereNotNull($scoreColumn)->count();
                $average = (clone $query)->avg($scoreColumn);

                if ($average !== null) {
                    return [
                        'value' => $this->formatDecimal((float) $average),
                        'note' => $count > 0 ? $count . ' data evaluasi' : 'Belum ada data',
                    ];
                }
            } catch (QueryException $e) {
                continue;
            }
        }

        return ['value' => '-', 'note' => 'Belum ada data'];
    }

    private function getActiveLoanTotal($employeeId): array
    {
        $tables = ['pinjamans', 'pinjaman', 'kasbons', 'kasbon', 'loans', 'loan'];
        $amountColumns = ['nominal', 'jumlah', 'total', 'amount', 'jml_pinjaman', 'besar_pinjaman', 'nilai', 'sisa_pinjaman', 'sisa', 'sisa_hutang'];
        $inactiveStatuses = ['lunas', 'selesai', 'ditolak', 'rejected', 'batal', 'cancelled', 'dibatalkan', 'paid', 'terbayar'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $idColumn = $this->firstExistingColumn($table, $this->employeeIdColumns());
            $amountColumn = $this->firstExistingColumn($table, $amountColumns);

            if (!$idColumn || !$amountColumn) {
                continue;
            }

            try {
                $query = DB::table($table)->where($idColumn, $employeeId);
                $statusColumn = $this->firstExistingColumn($table, ['status', 'status_pinjaman', 'status_pengajuan']);

                if ($statusColumn) {
                    $query->where(function ($q) use ($statusColumn, $inactiveStatuses) {
                        $q->whereNull($statusColumn)
                            ->orWhereNotIn(DB::raw('LOWER(' . $statusColumn . ')'), $inactiveStatuses);
                    });
                }

                $total = (float) $query->sum($amountColumn);

                return [
                    'total' => $total,
                    'note' => $statusColumn ? 'Pinjaman aktif' : 'Total pinjaman',
                ];
            } catch (QueryException $e) {
                continue;
            }
        }

        return ['total' => 0, 'note' => 'Pinjaman aktif'];
    }

    private function getOvertimeHours($employeeId): array
    {
        // Pastikan hanya tabel lemburs yang dipakai karena sudah ada model Lembur
        try {
            $hours = Lembur::where('id_kry', $employeeId)
                ->whereNotIn('sts_lbr', ['ditolak', 'rejected', 'batal', 'cancelled', 'dibatalkan'])
                ->whereBetween('tgl_lbr', [
                    Carbon::now()->startOfMonth()->toDateString(),
                    Carbon::now()->endOfMonth()->toDateString()
                ])
                ->sum('qty_jam');

            return [
                'hours' => (float) $hours,
                'note' => 'Total lembur bulan ini'
            ];
        } catch (\Exception $e) {
            return ['hours' => 0, 'note' => 'Total lembur bulan ini'];
        }
    }

    private function buildRecentActions($karyawan, array $evaluation, array $loan, array $overtime): array
    {
        $actions = [];

        if (!empty($karyawan->updated_at)) {
            $actions[] = [
                'title' => 'Data karyawan diperbarui',
                'date' => $this->formatDate($karyawan->updated_at),
            ];
        }

        if (!empty($karyawan->created_at)) {
            $actions[] = [
                'title' => 'Akun karyawan dibuat',
                'date' => $this->formatDate($karyawan->created_at),
            ];
        }

        if ($evaluation['value'] !== '-') {
            $actions[] = [
                'title' => 'Skor evaluasi dihitung',
                'date' => Carbon::now()->format('Y-m-d'),
            ];
        }

        if ((float) $loan['total'] > 0) {
            $actions[] = [
                'title' => 'Data pinjaman aktif ditemukan',
                'date' => Carbon::now()->format('Y-m-d'),
            ];
        }

        if ((float) $overtime['hours'] > 0) {
            $actions[] = [
                'title' => 'Data lembur periode ini ditemukan',
                'date' => Carbon::now()->format('Y-m-d'),
            ];
        }

        return array_slice($actions, 0, 3);
    }

    private function getEmployeeStatus($karyawan): array
    {
        $rawStatus = $karyawan->status ?? $karyawan->status_karyawan ?? 'aktif';
        $status = strtolower((string) $rawStatus);
        $inactive = ['nonaktif', 'non-aktif', 'inactive', 'resign', 'keluar', 'berhenti'];

        return [
            'value' => in_array($status, $inactive, true) ? 'NONAKTIF' : 'AKTIF',
            'note' => 'Kepegawaian',
        ];
    }

    private function employeeIdColumns(): array
    {
        return ['id_kry', 'karyawan_id', 'id_karyawan', 'kry_id', 'staff_id', 'id_staff', 'employee_id'];
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function formatDate($date): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    private function formatRupiah($value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }

    private function formatHour($value): string
    {
        return $this->formatDecimal((float) $value);
    }

    private function formatDecimal(float $value): string
    {
        if (floor($value) == $value) {
            return number_format($value, 0, ',', '.');
        }

        return number_format($value, 1, ',', '.');
    }

    private function initial($name): string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return 'K';
        }

        return strtoupper(mb_substr($name, 0, 1));
    }
}
