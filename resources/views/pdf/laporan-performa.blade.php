<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Performa - {{ $agenda->judul_agenda }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
            font-size: 18px;
            color: #0d6efd;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 2px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #ddd;
            text-transform: uppercase;
        }
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .text-left {
            text-align: left !important;
        }
        .bg-score {
            background-color: #fafafa;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .signature {
            float: right;
            text-align: center;
            width: 200px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Evaluasi Performa Pemain</h2>
        <br>
        <h4>SSB BEJI PUTRA</h4>
        <p style="margin: 5px 0;">Sistem Informasi Manajemen Sekolah Sepak Bola (SSB) Beji Putra</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Agenda</strong></td>
            <td width="35%">: {{ $agenda->judul_agenda }}</td>
            <td width="15%"><strong>Tanggal</strong></td>
            <td width="35%">: {{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi</strong></td>
            <td>: {{ $agenda->lokasi }}</td>
            <td><strong>Tipe</strong></td>
            <td>: {{ ucfirst($agenda->tipe_agenda) }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th class="text-left">Nama Pemain</th>
                <th width="8%">PS</th>
                <th width="8%">DR</th>
                <th width="8%">SH</th>
                <th width="8%">FS</th>
                <th width="8%">DS</th>
                <th width="12%">Rata-rata</th>
                <th>Catatan Pelatih</th>
            </tr>
        </thead>
        <tbody>
            @forelse($performances as $p)
            <tr>
                <td class="text-left">
                    <strong>{{ $p->player->nama_lengkap ?? $p->player->nama_pemain }}</strong><br>
                    <small style="color: #666;">Posisi: {{ $p->player->posisi }}</small>
                </td>
                <td>{{ $p->passing }}</td>
                <td>{{ $p->dribbling }}</td>
                <td>{{ $p->shooting }}</td>
                <td>{{ $p->physics }}</td>
                <td>{{ $p->discipline }}</td>
                <td class="bg-score">{{ number_format($p->rata_rata, 1) }}</td>
                <td class="text-left" style="font-size: 9px;">{{ $p->catatan_pelatih ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8">Belum ada data penilaian untuk agenda ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px; font-size: 9px; color: #777;">
        * Keterangan: PS (Passing), DR (Dribbling), SH (Shooting), FS (Fisik), DS (Disiplin)
    </div>

    <div class="footer">
        <div class="signature">
            <p>Dicetak pada: {{ $date }}</p>
            <br><br><br>
            <p><strong>( ____________________ )</strong></p>
            <p>Pelatih Kepala / Admin</p>
        </div>
    </div>

</body>
</html>