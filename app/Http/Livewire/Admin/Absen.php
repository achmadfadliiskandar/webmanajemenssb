<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Jadwal;
use App\Models\Player;
use App\Models\Absen as Absensi; // Pastikan nama model sesuai (Absensi atau Presensi)
use Illuminate\Support\Facades\Auth;

class Absen extends Component
{
    public $selected_schedule;
    public $attendance = []; // Pastikan ini public

    // Penting: Inisialisasi awal agar array tidak kosong
    public function mount()
    {
        $this->selected_schedule = null;
        $this->attendance = [];
    }

    public function updatedSelectedSchedule($value)
    {
        if ($value) {
            $players = Player::all();
            
            // Ambil data absen yang sudah ada di database (jika ada)
            $existing = Absensi::where('schedule_id', $value)->get();

            foreach ($players as $player) {
                $saved = $existing->where('player_id', $player->id)->first();

                // Kita paksa array ini terisi agar wire:model di blade berfungsi
                $this->attendance[$player->id] = [
                    'status' => $saved ? $saved->status : 'hadir',
                    'keterangan' => $saved ? $saved->keterangan : '',
                ];
            }
        }
    }

    public function store()
    {
        // 1. Validasi awal input form
        $this->validate([
            'selected_schedule' => 'required',
            'attendance.*.status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        try {
            // 2. AMBIL DATA JADWAL & CEK TANGGAL (KEAMANAN BACKEND)
            $jadwal = Jadwal::findOrFail($this->selected_schedule);
            $tanggalAgenda = \Carbon\Carbon::parse($jadwal->tanggal_agenda)->startOfDay();
            $hariIni = \Carbon\Carbon::today();

            // Jika tanggal agenda sudah LEWAT dari hari ini, batalkan proses simpan
            if ($tanggalAgenda->isBefore($hariIni)) {
                session()->flash('error', 'Gagal Simpan! Jadwal ini sudah melewati batas waktu presensi (Sudah Lewat).');
                return;
            }

            // 3. PROSES SIMPAN JIKA LOLOS VALIDASI TANGGAL
            foreach ($this->attendance as $playerId => $data) {
                Absensi::updateOrCreate(
                    [
                        'schedule_id' => $this->selected_schedule,
                        'player_id'   => $playerId,
                    ],
                    [
                        'status'     => $data['status'],
                        'keterangan' => $data['keterangan'] ?? '',
                    ]
                );
            }

            session()->flash('success', 'Absensi berhasil disimpan untuk agenda: ' . $jadwal->judul_agenda);
            
        } catch (\Exception $e) {
            // Tangani jika ada error database atau sistem
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }


    public $search = '';
    public function render()
    {
        $judul = 'Presensi Pemain';
        
        // Ambil semua jadwal
        $schedules = Jadwal::orderBy('tanggal_agenda', 'desc')->get();
        
        // Tambahkan status 'is_locked' ke setiap jadwal secara dinamis
        $schedules->transform(function ($item) {
            // Logika: Kunci jika tanggal agenda sudah LEWAT dari hari ini (startOfDay)
            $item->is_locked = \Carbon\Carbon::parse($item->tanggal_agenda)
                                ->startOfDay()
                                ->isBefore(\Carbon\Carbon::today());
            return $item;
        });

        // Inisialisasi players
        $players = [];

        if ($this->selected_schedule) {
            // Query pemain dengan filter pencarian nama
            $players = Player::where('nama_pemain', 'like', '%' . $this->search . '%')
                            ->orderBy('nama_pemain', 'asc')
                            ->get();
        }

        return view('livewire.admin.absen', [
            'schedules' => $schedules,
            'players'   => $players
        ])->layout('layouts.app', ['judul' => $judul]);
    }
}