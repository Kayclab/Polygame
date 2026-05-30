<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use App\Models\Evaluasi;
use App\Models\karyawan;
use Illuminate\Support\Facades\Auth;
use App\Models\SoalEvaluasi;
use Illuminate\Http\Request;
use App\Models\DetailEvaluasi;
class EvaluationController extends Controller
{
    public function index()
    {
        $login = Auth::user();

        if (!$login) {
            return redirect()->route('login');
        }

        $receivedEvaluations = Evaluasi::with(['karyawanPenilai', 'details.soal'])
            ->where('id_kry', $login->id_kry)
            ->where('status', 'selesai')
            ->latest('tgl_evl')
            ->get();

        $givenEvaluations = Evaluasi::with(['karyawanDinilai', 'details.soal'])
            ->where('id_penilai', $login->id_kry)
            ->where('status', 'selesai')
            ->latest('tgl_evl')
            ->get();

        $receivedScore = round($receivedEvaluations->avg('skor_total') ?? 0, 1);

        $givenScore = round($givenEvaluations->avg('skor_total') ?? 0, 1);

        $staffList = karyawan::where('id_kry', '!=', $login->id_kry)
            ->get()
            ->map(function ($kry) use ($login) {
                $evaluations = Evaluasi::with(['details.soal'])
                    ->where('id_kry', $kry->id_kry)
                    ->where('id_penilai', $login->id_kry)
                    ->where('status', 'selesai')
                    ->get();
                $score = round($evaluations->avg('skor_total') ?? 0);

                return [
                    'id_kry' => $kry->id_kry,
                    'name' => $kry->n_kry,
                    'role' => $kry->role,
                    'jab' => $kry->jab,
                    'score' => $score,
                    'resp' => $evaluations->count() . ' RESPONSES',
                    'responses' => $evaluations->count(),
                    'initial' => strtoupper(substr($kry->n_kry, 0, 1)),
                    'img' => null,
                    'details' => $evaluations->flatMap(function ($evl) {
                        return $evl->details->map(function ($detail) {
                            return [
                                'pertanyaan' => $detail->soal->pertanyaan ?? '-',
                                'kategori' => $detail->soal->kategori ?? 'Evaluasi',
                                'nilai' => $detail->jawaban ?? 0,
                            ];
                        });
                    })->values()->toArray(),
                ];
            });
        $metrics = SoalEvaluasi::where('is_active', 1)
            ->whereIn('target_role', ['staff', 'owner'])
            ->orderBy('target_role')
            ->orderBy('id_soal')
            ->get();

        $auditTargets = karyawan::where('id_kry', '!=', $login->id_kry)
            ->get();

        $scale = [10, 20, 30, 40, 50, 60, 70, 80, 90, 100];

        $periode = now()->format('Y-m');

        $activeAuditStaff = Evaluasi::where('periode', $periode)
            ->where('target_role', 'staff')
            ->where('is_launched', 1)
            ->exists();

        $activeAuditOwner = Evaluasi::where('periode', $periode)
            ->where('target_role', 'owner')
            ->where('is_launched', 1)
            ->exists();

        $hasActiveStaffQuestions = $metrics->where('target_role', 'staff')->count() > 0;
        $hasActiveOwnerQuestions = $metrics->where('target_role', 'owner')->count() > 0;

        $canDoEvaluation =
            $hasActiveStaffQuestions &&
            $hasActiveOwnerQuestions;

        $selfDetails = $receivedEvaluations
            ->flatMap(function ($evl) {
                return $evl->details;
            })
            ->groupBy('id_soal');

        $selfSummary = SoalEvaluasi::where('is_active', 1)
            ->where('target_role', 'staff')
            ->get()
            ->map(function ($soal) use ($selfDetails) {
                $details = $selfDetails->get($soal->id_soal);

                return [
                    'kategori' => $soal->kategori ?? 'Evaluasi',
                    'pertanyaan' => $soal->pertanyaan ?? '-',
                    'nilai' => $details ? round($details->avg('jawaban'), 1) : 0,
                ];
            });

        return view('staff.evaluation', compact(
            'login',
            'receivedEvaluations',
            'givenEvaluations',
            'receivedScore',
            'givenScore',
            'staffList',
            'metrics',
            'auditTargets',
            'scale',
            'selfSummary',
            'activeAuditStaff',
            'activeAuditOwner',
            'canDoEvaluation'

        ));
    }

    public function submitEvaluation(Request $request)
    {
        $periode = now()->format('Y-m');
        $idPenilai = Auth::user()->id_kry;

        $scores = json_decode($request->scores, true);

        if (!$scores) {
            return redirect()->back()->with('error', 'Belum ada nilai yang diisi.');
        }

        $groupedScores = [];

        foreach ($scores as $key => $value) {
            $parts = explode('-', $key);
            $idSoal = $parts[0];
            $personId = $value['personId'];

            $groupedScores[$personId][] = [
                'id_soal' => $idSoal,
                'nilai' => $value['score']
            ];
        }

        foreach ($groupedScores as $personId => $details) {
            $total = collect($details)->sum('nilai');
            $avg = round($total / count($details));

            $evaluasi = Evaluasi::updateOrCreate(
                [
                    'periode' => $periode,
                    'id_kry' => $personId,
                    'id_penilai' => $idPenilai,
                ],
                [
                    'tgl_evl' => now(),
                    'skor_total' => $avg,
                    'rating' => $avg,
                    'status' => 'selesai'
                ]
            );

            $evaluasi->details()->delete();

            foreach ($details as $detail) {
                \App\Models\DetailEvaluasi::create([
                    'id_evl' => $evaluasi->id_evl,
                    'id_soal' => $detail['id_soal'],
                    'jawaban' => $detail['nilai'],
                ]);
            }
        }

        return redirect()
            ->route('staff.evaluations.index')
            ->with('success', 'Evaluasi berhasil dikirim.');
    }
}
