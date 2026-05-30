<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailEvaluasi extends Model
{
    protected $table = 'detail_evaluasis';

    protected $fillable = [
        'id_evl',
        'id_soal',
        'jawaban'
    ];

    public function evaluasi(): BelongsTo
    {
        return $this->belongsTo(Evaluasi::class, 'id_evl', 'id_evl');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalEvaluasi::class, 'id_soal', 'id_soal');
    }
}
