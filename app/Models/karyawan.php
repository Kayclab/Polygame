<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SlipGaji;
use App\Models\Notification;

class karyawan extends Authenticatable
{
    use HasFactory;

    public function getAuthIdentifierName()
    {
        return 'id_kry';
    }

    public function getAuthIdentifier()
    {
        return $this->id_kry;
    }

    protected $table = 'karyawans';
    protected $primaryKey = 'id_kry';
    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'n_kry',
        'jab',
        'alamat',
        'tmpt_lahir',
        'tgl_lahir',
        'tgl_mulai_kerja',
        'telp',
        'email',
        'password',
        'role'
    ];

    protected $hidden = ['password'];

    public function slipGajis()
    {return $this->hasMany(SlipGaji::class, 'id_kry', 'id_kry');}

    public function notifications(){
        return $this->hasMany(Notification::class, 'id_kry', 'id_kry');
    }
}
