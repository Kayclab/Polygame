<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Notification extends Model
{
    protected $table = 'notifications';

    protected $primaryKey = 'id_notification';

    protected $fillable = [
        'id_kry',
        'title',
        'message',
        'type',
        'priority',
        'is_read'
    ];

    public function karyawan()
    {
        return $this->belongsTo(karyawan::class, 'id_kry', 'id_kry');
    }
}