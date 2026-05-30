<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlipGaji extends Model
{
    protected $table = 'slip_gajis';

    protected $primaryKey = 'id_slip';

    protected $fillable = [
        'id_kry',
        'periode',
        'gaji_pokok',
        'bonus',
        'potongan',
        'total_gaji',
        'file_slip',
        'status'
    ];

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(karyawan::class, 'id_kry', 'id_kry');
    }
}