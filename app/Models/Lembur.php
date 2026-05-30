<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lembur extends Model
{
    protected $table = 'lemburs';
    protected $primaryKey = 'id_lbr';

    protected $fillable = [
        'id_kry', 
        'tgl_lbr', 
        'qty_jam', 
        'keterangan', // Sesuai dengan field 'reason' di form
        'bukti_foto', // Untuk menyimpan path gambar
        'sts_lbr'
    ];

    public function karyawan(): BelongsTo
    {
        // Parameter kedua adalah foreign key di tabel lemburs
        // Parameter ketiga adalah owner key di tabel karyawans
        return $this->belongsTo(karyawan::class, 'id_kry', 'id_kry');
    }
}
