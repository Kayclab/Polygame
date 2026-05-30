<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use App\Models\SlipGaji;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $payslips = SlipGaji::where('id_kry', Auth::user()->id_kry)
            ->latest()
            ->get();

        $lastJumlah = $payslips->first()?->total_gaji ?? 0;

        $employee = [
            'name'       => Auth::user()->n_kry,
            'role'       => Auth::user()->jab,
            'account_id' => Auth::user()->id_kry,
        ];

        return view('staff.payroll', compact(
            'payslips',
            'lastJumlah',
            'employee'
        ));
    }
}