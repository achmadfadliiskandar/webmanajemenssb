<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;

    protected $table = 'attendances';


    // Tambahkan baris ini untuk mengizinkan kolom disimpan ke database
    protected $fillable = [
        'schedule_id',
        'player_id',
        'status',
        'keterangan',
    ];
}