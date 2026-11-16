<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Player;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Performance;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
{
    \Carbon\Carbon::setLocale('id');
    $today = date('Y-m-d');

    // Statistik Dasar
    $totalPemainAll = Player::all(); 
    $totalAgenda    = Jadwal::count();
    $totalUser      = User::count();
    $agendaTertunda = Jadwal::where('keterangan', 'tertunda')->count();
    $rerataNilai    = Performance::avg('rata_rata') ?? 0;

    /** * QUERY CHART: DIUBAH MENJADI BULANAN
     * Mengelompokkan rata-rata nilai berdasarkan bulan dan tahun
     */
    $performances = Performance::selectRaw("
                        DATE_FORMAT(created_at, '%Y-%m') as month_year, 
                        AVG(rata_rata) as avg
                    ")
                    ->groupBy('month_year')
                    ->orderBy('month_year', 'asc')
                    ->take(12) // Ambil data 12 bulan terakhir
                    ->get();

    // Mapping Label: Ubah "2026-03" menjadi "Maret 2026" agar user paham
    $labels = $performances->map(function($item) {
        return \Carbon\Carbon::parse($item->month_year)->translatedFormat('F Y');
    });

    return view('livewire.admin.dashboard', [
        'totalPemain'    => $totalPemainAll->count(),
        'totalAgenda'    => $totalAgenda,
        'totalUser'      => $totalUser,
        'agendaTertunda' => $agendaTertunda,
        'rerataNilai'    => $rerataNilai,
        
        // Statistik Per KU
        'ku6'    => $totalPemainAll->where('kelompok_usia', 'KU-6')->count(),
        'ku10'   => $totalPemainAll->where('kelompok_usia', 'KU-10')->count(),
        'ku14'   => $totalPemainAll->where('kelompok_usia', 'KU-14')->count(),
        'senior' => $totalPemainAll->where('kelompok_usia', 'Senior')->count(),

        // Agenda Terdekat
        'agendaTerdekat' => Jadwal::where('tanggal_agenda', '>=', $today)
                            ->orderBy('tanggal_agenda', 'asc')
                            ->take(5)
                            ->get(),
        'tanggalSekarang' => $today,
        
        // Data Chart (Sudah dalam format JSON)
        'labels'    => json_encode($labels),
        'chartData' => json_encode($performances->pluck('avg')->map(fn($v) => (float)$v)),
    ])->layout('layouts.app', ['judul' => 'Dashboard']);
}
}