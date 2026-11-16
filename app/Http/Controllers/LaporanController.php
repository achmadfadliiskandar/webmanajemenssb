<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Performance; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function cetakPerforma($schedule_id)
    {
        // 1. Ambil data agenda/jadwal
        $agenda = Jadwal::findOrFail($schedule_id);
        
        // 2. Ambil data penilaian yang terhubung dengan agenda ini
        // Asumsi: Model Performance punya relasi 'player' ke tabel players
        $performances = Performance::with('player')
                        ->where('schedule_id', $schedule_id)
                        ->get();

        // 3. Siapkan data untuk dikirim ke view PDF
        $data = [
            'agenda' => $agenda,
            'performances' => $performances,
            'date' => Carbon::now()->format('d/m/Y H:i')
        ];

        // 4. Load View dan Set Kertas A4
        $pdf = Pdf::loadView('pdf.laporan-performa', $data)
                  ->setPaper('a4', 'portrait');

        // 5. Tampilkan di browser (stream)
        return $pdf->stream('Laporan-Performa-'.$agenda->tanggal_agenda.'.pdf');
    }
}