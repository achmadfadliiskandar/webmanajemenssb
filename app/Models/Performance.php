<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    use HasFactory;
    protected $fillable = [
        'schedule_id', 
        'player_id', 
        'user_id', 
        'passing', 
        'dribbling', 
        'shooting', 
        'stamina',    // Tambahkan ini
        'physics', 
        'discipline', 
        'rata_rata', 
        'catatan_pelatih'
    ];
    public function player() {
        return $this->belongsTo(Player::class, 'player_id');
    }
    public function agenda()
    {
        return $this->belongsTo(Jadwal::class, 'schedule_id');
    }
}
