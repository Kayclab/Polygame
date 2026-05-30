<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluasi;
use App\Models\DetailEvaluasi;
use App\Models\SoalEvaluasi;
use App\Models\karyawan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Notification;

class EvaluasiController extends Controller
{
    public function index(Request $request)
    {
        // Evaluasi dibuat per periode bulanan dengan format YYYY-MM.
        // Default memakai bulan berjalan, misalnya 2026-05.
        // Jika nanti blade diberi filter ?periode=2026-06, halaman juga bisa membaca periode tersebut.
        $requestedPeriode = $request->input('periode');
        $periode = preg_match('/^\d{4}-\d{2}$/', (string) $requestedPeriode)
            ? $requestedPeriode
            : Carbon::now()->format('Y-m');

        $currentKaryawanId = optional(Auth::user())->id_kry;
        $currentKaryawan = $currentKaryawanId ? karyawan::where('id_kry', $currentKaryawanId)->first() : null;
        $currentRole = strtolower((string) ($currentKaryawan->role ?? optional(Auth::user())->role ?? ''));
        $canManageAudit = in_array($currentRole, ['owner', 'admin'], true);

        $metrics = SoalEvaluasi::orderBy('target_role')
            ->orderBy('id_soal')
            ->get();

        $ownersList = karyawan::where('role', 'owner')->get();
        $staffsList = karyawan::where('role', '!=', 'owner')->get();

        $allKaryawan = karyawan::all();

        $completedEvaluations = Evaluasi::where('periode', $periode)
            ->where('status', 'selesai');

        $responseVolume = (clone $completedEvaluations)->count();

        $systemIndex = $responseVolume > 0
            ? round((clone $completedEvaluations)->avg('skor_total'), 1)
            : 0;

        $staffData = [];

        foreach ($allKaryawan as $kry) {
            $evaluations = Evaluasi::where('periode', $periode)
                ->where('id_kry', $kry->id_kry)
                ->where('status', 'selesai')
                ->get();

            $evaluationIds = $evaluations->pluck('id_evl')->filter()->values();

            $responsesCount = $evaluations->count();

            $avgScore = $responsesCount > 0
                ? round($evaluations->avg('skor_total'), 1)
                : 0;

            $targetRole = $kry->role === 'owner' ? 'owner' : 'staff';

            $roleMetrics = $metrics->where('target_role', $targetRole)->values();

            $metricDetails = [];

            foreach ($roleMetrics as $metric) {
                $query = DetailEvaluasi::where('id_soal', $metric->id_soal);

                if ($evaluationIds->isNotEmpty()) {
                    $query->whereIn('id_evl', $evaluationIds->toArray());
                } else {
                    $query->whereRaw('1 = 0');
                }

                $metricResponses = $query->count();

                $metricAvg = $metricResponses > 0
                    ? round((float) $query->avg('jawaban'), 1)
                    : null;

                $metricDetails[] = [
                    'id_soal' => $metric->id_soal,
                    'metric' => $metric->pertanyaan,
                    'kategori' => $metric->kategori,
                    'score' => $metricAvg,
                    'responses' => $metricResponses,
                ];
            }

            $staffData[] = [
                'id_kry' => $kry->id_kry,
                'n_kry' => $kry->n_kry,
                'role' => $kry->role,
                'initial' => strtoupper(substr($kry->n_kry, 0, 1)),
                'score' => $avgScore,
                'responses' => $responsesCount . ' Responses',
                'responses_count' => $responsesCount,
                'metrics' => $metricDetails,
            ];
        }

        $activeAuditOwner = $this->isAuditUnlocked($periode, 'owner');
        $activeAuditStaff = $this->isAuditUnlocked($periode, 'staff');

        // Data ini dipakai blade agar tidak muncul error "Undefined array key alreadySubmitted".
        // Logika: user hanya punya 1 record evaluasi per target dalam 1 periode.
        // Kalau sudah pernah submit, form yang dibuka kembali akan menjadi mode edit dan update record yang sama.
        $alreadySubmitted = [
            'owner' => false,
            'staff' => false,
        ];

        $myEvaluationScores = [
            'owner' => [],
            'staff' => [],
        ];

        if ($currentKaryawanId) {
            $myEvaluations = Evaluasi::where('periode', $periode)
                ->where('id_penilai', $currentKaryawanId)
                ->whereIn('target_role', ['owner', 'staff'])
                ->get();

            foreach ($myEvaluations as $evaluation) {
                $role = $evaluation->target_role === 'owner' ? 'owner' : 'staff';

                if ($evaluation->status === 'selesai') {
                    $alreadySubmitted[$role] = true;
                }

                $details = DetailEvaluasi::where('id_evl', $evaluation->id_evl)->get();

                foreach ($details as $detail) {
                    $key = $detail->id_soal . '-' . $evaluation->id_kry;

                    $myEvaluationScores[$role][$key] = [
                        'personId' => (string) $evaluation->id_kry,
                        'score' => (float) $detail->jawaban,
                    ];
                }
            }
        }

        return view('owner.evaluations', compact(
            'metrics',
            'ownersList',
            'staffsList',
            'staffData',
            'systemIndex',
            'responseVolume',
            'activeAuditOwner',
            'activeAuditStaff',
            'alreadySubmitted',
            'myEvaluationScores',
            'currentKaryawanId',
            'canManageAudit',
            'periode'
        ));
    }

    /**
     * Route lama owner.evaluasi.launch tetap dipakai.
     * action=unlock -> owner/admin membuka evaluasi dengan SoalEvaluasi.is_active = 1.
     * action=lock   -> owner/admin mengunci evaluasi dengan SoalEvaluasi.is_active = 0.
     *
     * Catatan: kolom is_active berada di tabel soal_evaluasis, bukan di tabel evaluasis.
     */
    public function launchAudit(Request $request)
    {
        $request->validate([
            'role' => 'required|in:owner,staff',
            'action' => 'nullable|in:unlock,lock',
        ]);

        if (!$this->currentUserIsOwner()) {
            return $this->auditResponse($request, false, 'Hanya owner yang bisa melakukan lock atau unlock evaluasi.', 403);
        }

        $role = $request->role;
        $action = $request->input('action', 'unlock');
        $periode = now()->format('Y-m');

        if ($action === 'lock') {
            // Status lock/unlock disimpan langsung ke tabel soal_evaluasis.
            // Dipakai DB::table agar tidak tergantung fillable/model dan pasti mengubah kolom is_active.
            $updatedRows = $this->setAuditActive($role, false);

            return $this->auditResponse($request, true, 'Evaluasi berhasil dikunci. Jumlah soal yang diubah: ' . $updatedRows . '.', 200, [
                'unlocked' => false,
                'role' => $role,
                'updated_rows' => $updatedRows,
            ]);
        }

        // Jika is_active = 1, kartu/form evaluasi akan tampil untuk staff.
        $updatedRows = $this->setAuditActive($role, true);

        $targets = $role === 'owner'
            ? karyawan::where('role', 'owner')->get()
            : karyawan::where('role', '!=', 'owner')->get();

        $evaluators = $role === 'owner'
            ? karyawan::where('role', 'owner')->get()
            : karyawan::where('role', '!=', 'owner')->get();

        foreach ($targets as $target) {
            foreach ($evaluators as $penilai) {
                if ((int) $penilai->id_kry === (int) $target->id_kry) {
                    continue;
                }

                $evaluasi = $this->findEvaluationRow($periode, $target->id_kry, $penilai->id_kry);

                if (!$evaluasi) {
                    $evaluasi = new Evaluasi();
                    $evaluasi->periode = $periode;
                    $evaluasi->id_kry = $target->id_kry;
                    $evaluasi->id_penilai = $penilai->id_kry;
                    $evaluasi->status = 'pending';
                    $evaluasi->tgl_evl = now();
                }

                if (!$evaluasi->status) {
                    $evaluasi->status = 'pending';
                }

                $evaluasi->target_role = $role;
                $evaluasi->save();
            }
        }

        if (in_array($role, ['staff', 'owner'])) {
            $users = $role === 'owner'
                ? karyawan::where('role', 'owner')->get()
                : karyawan::where('role', 'staff')->get();

            foreach ($users as $user) {
                Notification::create([
                    'id_kry' => $user->id_kry,
                    'title' => 'Evaluasi Dibuka',
                    'message' => 'Evaluasi bulan ini sudah tersedia untuk dikerjakan.',
                    'type' => 'evaluation',
                    'priority' => 'high',
                ]);
            }
        }

        return $this->auditResponse($request, true, 'Evaluasi berhasil dibuka. Jumlah soal yang diubah: ' . $updatedRows . '.', 200, [
            'unlocked' => true,
            'role' => $role,
            'updated_rows' => $updatedRows,
        ]);
    }

    public function submitEvaluation(Request $request)
    {
        $request->validate([
            'scores' => 'required',
        ]);

        $scores = json_decode($request->scores, true);

        if (!is_array($scores)) {
            return redirect()
                ->route('owner.evaluasi.index')
                ->with('error', 'Format skor tidak valid.');
        }

        $penilai = optional(Auth::user())->id_kry;
        $periode = now()->format('Y-m');

        if (!$penilai) {
            return redirect()
                ->route('owner.evaluasi.index')
                ->with('error', 'User penilai tidak ditemukan.');
        }

        $groupedScores = [];

        foreach ($scores as $key => $value) {
            $parts = explode('-', (string) $key);
            $idSoal = $parts[0] ?? null;
            $personId = $value['personId'] ?? null;
            $score = $value['score'] ?? null;

            if (!$idSoal || !$personId || $score === null || !is_numeric($score)) {
                continue;
            }

            $score = (float) $score;

            if ($score < 10 || $score > 100) {
                continue;
            }

            // Tidak boleh menilai diri sendiri.
            if ((int) $personId === (int) $penilai) {
                continue;
            }

            $groupedScores[$personId][] = [
                'id_soal' => $idSoal,
                'nilai' => $score,
            ];
        }

        if (empty($groupedScores)) {
            return redirect()
                ->route('owner.evaluasi.index')
                ->with('error', 'Tidak ada nilai valid yang dikirim.');
        }

        foreach ($groupedScores as $personId => $details) {
            if (empty($details)) {
                continue;
            }

            $target = karyawan::where('id_kry', $personId)->first();

            if (!$target) {
                continue;
            }

            $targetRole = $target->role === 'owner' ? 'owner' : 'staff';

            if (!$this->isAuditUnlocked($periode, $targetRole)) {
                return redirect()
                    ->route('owner.evaluasi.index')
                    ->with('error', 'Evaluasi masih dikunci oleh owner. Staff hanya bisa mengisi ketika evaluasi sudah di-unlock.');
            }

            $validMetricIds = SoalEvaluasi::where('target_role', $targetRole)
                ->where('is_active', 1)
                ->pluck('id_soal')
                ->map(fn($id) => (string) $id)
                ->toArray();

            $details = collect($details)
                ->filter(fn($detail) => in_array((string) $detail['id_soal'], $validMetricIds, true))
                ->values();

            if ($details->isEmpty()) {
                continue;
            }

            $avg = round($details->avg('nilai'), 1);

            // Kunci utamanya: cari record dengan periode + target + penilai.
            // Jangan pakai status sebagai kondisi pencarian, supaya submit kedua mengedit record selesai yang sama.
            $evaluasi = $this->findEvaluationRow($periode, $personId, $penilai);

            if (!$evaluasi) {
                $evaluasi = new Evaluasi();
                $evaluasi->periode = $periode;
                $evaluasi->id_kry = $personId;
                $evaluasi->id_penilai = $penilai;
            }

            $evaluasi->tgl_evl = now();
            $evaluasi->skor_total = $avg;
            $evaluasi->rating = $avg;
            $evaluasi->status = 'selesai';
            $evaluasi->target_role = $targetRole;
            $evaluasi->save();

            DetailEvaluasi::where('id_evl', $evaluasi->id_evl)->delete();

            foreach ($details as $detail) {
                DetailEvaluasi::create([
                    'id_evl' => $evaluasi->id_evl,
                    'id_soal' => $detail['id_soal'],
                    'jawaban' => $detail['nilai'],
                ]);
            }
        }

        return redirect()
            ->route('owner.evaluasi.index')
            ->with('success', 'Evaluasi berhasil disimpan. Jika sebelumnya sudah pernah menilai, nilai lama berhasil diperbarui.');
    }

    public function storeMetric(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'kategori' => 'required',
            'target_role' => 'required|in:owner,staff',
        ]);

        $soal = new SoalEvaluasi();
        $soal->pertanyaan = $request->pertanyaan;
        $soal->kategori = $request->kategori;
        $soal->target_role = $request->target_role;
        $soal->bobot_nilai = 1;
        // Ikuti status role saat ini. Jika role sedang dikunci, soal baru ikut terkunci.
        $soal->is_active = $this->isAuditUnlocked(now()->format('Y-m'), $request->target_role) ? 1 : 0;
        $soal->save();

        return redirect()->back()->with('success', 'Soal evaluasi berhasil ditambahkan');
    }

    public function updateMetric(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'kategori' => 'required',
            'target_role' => 'required|in:owner,staff',
        ]);

        $soal = SoalEvaluasi::findOrFail($id);

        $soal->pertanyaan = $request->pertanyaan;
        $soal->kategori = $request->kategori;
        $soal->target_role = $request->target_role;
        $soal->bobot_nilai = 1;
        // is_active tidak diubah saat edit teks/kategori agar status lock/unlock tetap aman.
        $soal->save();

        return redirect()->back()->with('success', 'Soal evaluasi berhasil diupdate');
    }

    public function destroyMetric($id)
    {
        $soal = SoalEvaluasi::findOrFail($id);
        $soal->delete();

        return redirect()->back()->with('success', 'Soal evaluasi berhasil dihapus');
    }

    private function currentUserIsOwner(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if (in_array(strtolower((string) ($user->role ?? '')), ['owner', 'admin'], true)) {
            return true;
        }

        if (!isset($user->id_kry)) {
            return false;
        }

        return karyawan::where('id_kry', $user->id_kry)
            ->whereRaw('LOWER(TRIM(role)) IN (?, ?)', ['owner', 'admin'])
            ->exists();
    }

    private function setAuditActive(string $role, bool $active): int
    {
        $role = strtolower(trim($role));

        if (!Schema::hasTable('soal_evaluasis') || !Schema::hasColumn('soal_evaluasis', 'is_active')) {
            return 0;
        }

        $data = ['is_active' => $active ? 1 : 0];

        if (Schema::hasColumn('soal_evaluasis', 'updated_at')) {
            $data['updated_at'] = now();
        }

        // LOWER(TRIM(target_role)) membuat update tetap jalan walaupun isi target_role ada spasi/huruf besar.
        return DB::table('soal_evaluasis')
            ->whereRaw('LOWER(TRIM(target_role)) = ?', [$role])
            ->update($data);
    }

    private function isAuditUnlocked(string $periode, string $role): bool
    {
        // Parameter $periode dipertahankan agar pemanggilan lama tidak perlu diubah.
        // Status lock/unlock dibaca dari tabel soal_evaluasis.
        // is_active = 1 berarti evaluasi untuk role tersebut dibuka.
        // is_active = 0 berarti evaluasi untuk role tersebut dikunci/disembunyikan dari staff.
        $role = strtolower(trim($role));

        if (!Schema::hasTable('soal_evaluasis') || !Schema::hasColumn('soal_evaluasis', 'is_active')) {
            return false;
        }

        return DB::table('soal_evaluasis')
            ->whereRaw('LOWER(TRIM(target_role)) = ?', [$role])
            ->where('is_active', 1)
            ->exists();
    }

    private function findEvaluationRow(string $periode, $targetId, $penilaiId): ?Evaluasi
    {
        $done = Evaluasi::where('periode', $periode)
            ->where('id_kry', $targetId)
            ->where('id_penilai', $penilaiId)
            ->where('status', 'selesai')
            ->first();

        if ($done) {
            return $done;
        }

        return Evaluasi::where('periode', $periode)
            ->where('id_kry', $targetId)
            ->where('id_penilai', $penilaiId)
            ->first();
    }

    private function auditResponse(Request $request, bool $success, string $message, int $status = 200, array $extra = [])
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array_merge([
                'success' => $success,
                'message' => $message,
            ], $extra), $status);
        }

        return redirect()
            ->back()
            ->with($success ? 'success' : 'error', $message);
    }
}
