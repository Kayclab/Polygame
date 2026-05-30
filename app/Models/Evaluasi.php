<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluasi extends Model
{
    protected $table = 'evaluasis';
    protected $primaryKey = 'id_evl';

    protected $fillable = [
        'tgl_evl',
        'periode',
        'id_kry',
        'id_penilai',
        'skor_total',
        'rating',
        'status'
    ];

    public function karyawanDinilai(): BelongsTo
    {
        return $this->belongsTo(karyawan::class, 'id_kry', 'id_kry');
    }

    public function karyawanPenilai(): BelongsTo
    {
        return $this->belongsTo(karyawan::class, 'id_penilai', 'id_kry');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailEvaluasi::class, 'id_evl', 'id_evl');
    }
}
