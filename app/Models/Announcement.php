<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;
    protected $table = 'announcements';
    protected $primaryKey = 'id_announcement';

    protected $fillable = [
        'title',
        'content',
        'id_kry' // Tambahkan ini menggantikan author string
    ];

    /**
     * Relasi balik ke model Karyawan (Admin yang memposting)
     */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(karyawan::class, 'id_kry', 'id_kry');
    }
}
