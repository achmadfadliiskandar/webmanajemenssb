<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'judul_agenda', 'tanggal_agenda', 'waktu_mulai', 'waktu_selesai',
        'lokasi', 'keterangan','deskripsi','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}