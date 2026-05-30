<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pinjaman extends Model
{
    protected $table = 'pinjamans';

    protected $fillable = [
        'karyawan_id',
        'type',
        'total',
        'keterangan',
        'tanggal',
        'status',
    ];

    public function karyawan(): BelongsTo
    {
        // Parameter kedua adalah foreign key di tabel lemburs
        // Parameter ketiga adalah owner key di tabel karyawans
        return $this->belongsTo(karyawan::class, 'id_kry', 'id_kry');
    }
}