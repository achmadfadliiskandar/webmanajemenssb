<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pemain',
        'no_anggota',
        'tanggal_lahir',
        'tempat_lahir',
        'nomor_punggung',
        'posisi',
        'foto',
        'kaki_dominan',
        'tinggi_badan',
        'berat_badan',
        'status_pembayaran',
    ];
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
    // app/Models/Player.php
    public function getKelompokUsiaAttribute()
    {
        $usia = \Carbon\Carbon::parse($this->tanggal_lahir)->age;

        if ($usia <= 6) return 'KU-6'; // Harus pakai strip jika di database pakai strip
        if ($usia <= 10) return 'KU-10';
        if ($usia <= 14) return 'KU-14';
        return 'Senior';
    }
    public function performances()
    {
        return $this->hasMany(Performance::class, 'player_id');
    }
}
