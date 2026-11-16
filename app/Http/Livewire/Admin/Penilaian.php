<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Jadwal;
use App\Models\Player;
use App\Models\Absen;
use App\Models\Performance;
use Illuminate\Support\Facades\Auth;

class Penilaian extends Component
{
    public $selected_schedule;
    public $scores = []; 

    /**
     * Menyiapkan array input berdasarkan kolom database terbaru
     */
    public function updatedSelectedSchedule($value)
    {
        $this->scores = [];

        if ($value) {
            $user = Auth::user();

            // 1. Ambil ID pemain yang HADIR (Hanya yang hadir yang dinilai)
            $playerIdsHadir = Absen::where('schedule_id', $value)
                                ->where('status', 'hadir')
                                ->pluck('player_id');

            // 2. Filter pemain berdasarkan KU pelatih
            $query = Player::whereIn('id', $playerIdsHadir)->get();

            if ($user->role === 'pelatih' && is_array($user->pelatih_ku)) {
                $players = $query->filter(function($player) use ($user) {
                    return in_array($player->kelompok_usia, $user->pelatih_ku);
                });
            } else {
                $players = $query;
            }

            // 3. Mapping data ke array scores (Sesuai kolom DB baru)
            foreach ($players as $player) {
                $existing = Performance::where('schedule_id', $value)
                                        ->where('player_id', $player->id)
                                        ->first();

                $this->scores[$player->id] = [
                    'passing'         => $existing ? $existing->passing : 0,
                    'dribbling'       => $existing ? $existing->dribbling : 0,
                    'shooting'        => $existing ? $existing->shooting : 0,
                    'stamina'         => $existing ? $existing->stamina : 0, // Baru
                    'physics'         => $existing ? $existing->physics : 0, // Baru
                    'discipline'      => $existing ? $existing->discipline : 0, // Baru
                    'catatan_pelatih' => $existing ? $existing->catatan_pelatih : '', // Baru
                ];
            }
        }
    }

    public function store()
    {
        $this->validate([
            'selected_schedule' => 'required',
            'scores.*.passing'    => 'required|numeric|min:0|max:100',
            'scores.*.dribbling'  => 'required|numeric|min:0|max:100',
            'scores.*.shooting'   => 'required|numeric|min:0|max:100',
            'scores.*.stamina'    => 'required|numeric|min:0|max:100',
            'scores.*.physics'    => 'required|numeric|min:0|max:100',
            'scores.*.discipline' => 'required|numeric|min:0|max:100',
        ]);

        try {
            foreach ($this->scores as $playerId => $data) {
                // Hitung rata-rata dari 6 indikator penilaian
                $rataRata = (
                    $data['passing'] + 
                    $data['dribbling'] + 
                    $data['shooting'] + 
                    $data['stamina'] + 
                    $data['physics'] + 
                    $data['discipline']
                ) / 6;

                Performance::updateOrCreate(
                    [
                        'schedule_id' => $this->selected_schedule,
                        'player_id'   => $playerId,
                    ],
                    [
                        'user_id'         => Auth::id(),
                        'passing'         => $data['passing'],
                        'dribbling'       => $data['dribbling'],
                        'shooting'        => $data['shooting'],
                        'stamina'         => $data['stamina'],
                        'physics'         => $data['physics'],
                        'discipline'      => $data['discipline'],
                        'rata_rata'       => $rataRata,
                        'catatan_pelatih' => $data['catatan_pelatih'],
                    ]
                );
            }

            session()->flash('success', 'Penilaian berhasil disimpan oleh ' . Auth::user()->name);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();
        $today = \Carbon\Carbon::today();
    
        // Ambil semua jadwal
        $schedules = Jadwal::orderBy('tanggal_agenda', 'desc')->get();
    
        // Tambahkan status penguncian secara dinamis
        $schedules->transform(function ($sj) use ($today) {
            $tgl = \Carbon\Carbon::parse($sj->tanggal_agenda)->startOfDay();
            
            // LOGIKA: 
            // 1. Kunci jika tanggal agenda adalah MASA DEPAN (Belum latihan kok sudah dinilai?)
            // 2. Kunci jika sudah LEWAT lebih dari 7 hari dari hari ini
            $sj->is_future = $tgl->isAfter($today);
            $sj->is_expired = $tgl->diffInDays($today) > 7; 
            
            $sj->is_locked = $sj->is_future || $sj->is_expired;
            
            return $sj;
        });
    
        $players = [];
        if ($this->selected_schedule) {
            // Cari jadwal yang dipilih dari collection yang sudah di-transform
            $currentSchedule = $schedules->firstWhere('id', $this->selected_schedule);
            
            // Jika paksa pilih yang terkunci lewat inspect element, kosongkan pemain
            if ($currentSchedule && $currentSchedule->is_locked) {
                $players = [];
                session()->flash('error', 'Jadwal ini sudah tidak dapat dinilai (Kadaluarsa atau Belum Berlangsung).');
            } else {
                $playerIdsHadir = Absen::where('schedule_id', $this->selected_schedule)
                                    ->where('status', 'hadir')
                                    ->pluck('player_id');
    
                $query = Player::whereIn('id', $playerIdsHadir)->get();
    
                if ($user->role === 'pelatih' && is_array($user->pelatih_ku)) {
                    $players = $query->filter(function($player) use ($user) {
                        return in_array($player->kelompok_usia, $user->pelatih_ku);
                    });
                } else {
                    $players = $query->sortBy('nama_pemain');
                }
            }
        }
    
        return view('livewire.admin.penilaian', [
            'schedules' => $schedules,
            'players'   => $players
        ])->layout('layouts.app', ['judul' => 'Penilaian Performa']);
    }
}