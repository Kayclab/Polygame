<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoalEvaluasi extends Model
{
    protected $table = 'soal_evaluasis';
    protected $primaryKey = 'id_soal';

    protected $fillable = [
        'pertanyaan',
        'kategori',
        'target_role',
        'bobot_nilai',
        'is_active'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(DetailEvaluasi::class, 'id_soal', 'id_soal');
    }
}
