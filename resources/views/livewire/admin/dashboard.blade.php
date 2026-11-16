<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark">Ringkasan Dashboard</h3>
            <p class="text-muted">Halo, {{ auth()->user()->name }}. Berikut data SSB Beji Putra per hari ini.</p>
        </div>
    </div>

    {{-- Baris Kartu Statistik Atas --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm p-3 bg-primary text-white" style="border-radius: 15px;">
                <small class="fw-bold opacity-75 text-uppercase">Total Pemain</small>
                <h2 class="fw-bold mb-0">{{ $totalPemain }}</h2>
                <i class="fa fa-users position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm p-3 bg-success text-white" style="border-radius: 15px;">
                <small class="fw-bold opacity-75 text-uppercase">Agenda Aktif</small>
                <h2 class="fw-bold mb-0">{{ $totalAgenda - $agendaTertunda }}</h2>
                <i class="fa fa-calendar-check position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm p-3 bg-warning text-dark" style="border-radius: 15px;">
                <small class="fw-bold opacity-75 text-uppercase">Rerata Nilai</small>
                <h2 class="fw-bold mb-0">{{ number_format($rerataNilai, 1) }}</h2>
                <i class="fa fa-chart-line position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm p-3 bg-danger text-white" style="border-radius: 15px;">
                <small class="fw-bold opacity-75 text-uppercase">Agenda Tertunda</small>
                <h2 class="fw-bold mb-0">{{ $agendaTertunda }}</h2>
                <i class="fa fa-clock position-absolute end-0 bottom-0 p-3 opacity-25 fa-2x"></i>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- List Agenda Terdekat --}}
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4 text-dark"><i class="fa fa-running me-2 text-primary"></i> Jadwal Latihan
                    Terdekat</h5>
                <div class="agenda-list">
                    @forelse($agendaTerdekat as $item)
                        @php $isToday = $item->tanggal_agenda == $tanggalSekarang; @endphp
                        <div class="d-flex align-items-center p-3 mb-3 border-start border-4 {{ $isToday ? 'bg-success text-white border-success' : 'bg-light border-primary' }}"
                            style="border-radius: 10px;">
                            <div class="me-3 text-center" style="width: 50px;">
                                <h5 class="fw-bold mb-0">{{ \Carbon\Carbon::parse($item->tanggal_agenda)->format('d') }}
                                </h5>
                                <small
                                    class="text-uppercase">{{ \Carbon\Carbon::parse($item->tanggal_agenda)->translatedFormat('M') }}</small>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $item->nama_agenda }}</h6>
                                <small
                                    class="{{ $isToday ? 'text-white-50' : 'text-muted' }}">{{ $item->lokasi }}</small>
                            </div>
                            @if ($isToday)
                                <span class="badge bg-white text-success fw-bold p-2 px-3 rounded-pill shadow-sm">HARI
                                    INI</span>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <p class="text-muted small">Belum ada agenda terdaftar.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Statistik KU & Chart Performa --}}
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 20px;">
                <h5 class="fw-bold mb-4"><i class="fa fa-users-cog me-2 text-primary"></i> Distribusi Kelompok Usia</h5>
                <div class="row text-center mb-4">
                    <div class="col-3">
                        <h4 class="fw-bold text-primary mb-0">{{ $ku6 }}</h4><small
                            class="text-muted">KU-6</small>
                    </div>
                    <div class="col-3 border-start">
                        <h4 class="fw-bold text-success mb-0">{{ $ku10 }}</h4><small
                            class="text-muted">KU-10</small>
                    </div>
                    <div class="col-3 border-start">
                        <h4 class="fw-bold text-info mb-0">{{ $ku14 }}</h4><small
                            class="text-muted">KU-14</small>
                    </div>
                    <div class="col-3 border-start">
                        <h4 class="fw-bold text-dark mb-0">{{ $senior }}</h4><small
                            class="text-muted">Senior</small>
                    </div>
                </div>

                <hr class="opacity-50">

                {{-- Area Canvas Chart --}}
                <div class="mt-3">
                    <h6 class="text-muted fw-bold mb-3 small text-uppercase">Tren Rerata Nilai Teknik Pemain</h6>
                    <div style="height: 250px;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Chart.js --}}
    {{-- app/resources/views/livewire/admin/dashboard.blade.php (Paling Bawah) --}}

    {{-- 1. PASTIKAN CDN INI TER-LOAD DI ATAS SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Bungkus dalam fungsi agar bisa dipanggil berulang kali
        function renderPerformanceChart() {
            // A. CEK APAKAH CANVASNYA ADA
            const ctx = document.getElementById('performanceChart');
            if (!ctx) {
                console.error("Gagal Render: Elemen 'performanceChart' tidak ditemukan.");
                return;
            }

            // B. CEK LIBRARY CHART.JS APAKAH SUDAH TER-LOAD
            if (typeof Chart === 'undefined') {
                console.error("Gagal Render: Library Chart.js belum ter-load.");
                return;
            }

            // C. PROTEKSI LIVEWIRE: Bersihkan chart lama di memori
            if (window.myPerformanceChart instanceof Chart) {
                window.myPerformanceChart.destroy();
            }

            const labelsJSON = {!! $labels !!};
            const chartDataJSON = {!! $chartData !!};

            // Jika data kosong, ganti canvas dengan pesan teks
            if (!labelsJSON || labelsJSON.length === 0) {
                console.warn("Info Render: Data performa kosong, menampilkan pesan placeholder.");
                ctx.parentNode.innerHTML =
                    '<div class="text-center py-5 text-muted small"><i class="fa fa-chart-area fa-2x mb-2 opacity-50"></i><br>Belum ada data penilaian performa pemain.</div>';
                return;
            }

            console.log("Mulai Render Chart dengan Data:", labelsJSON);

            // E. INISIALISASI CHART BARU
            const chartCtx = ctx.getContext('2d');
            window.myPerformanceChart = new Chart(chartCtx, {
                type: 'line',
                data: {
                    labels: labelsJSON, // Sumbu X
                    datasets: [{
                        label: 'Rerata Skor Teknik',
                        data: chartDataJSON, // Sumbu Y
                        borderColor: '#0d6efd', // Warna Garis Beji Putra
                        backgroundColor: 'rgba(13, 110, 253, 0.05)', // Isian halus
                        borderWidth: 3,
                        tension: 0.4, // Smooth curve
                        fill: true, // Fill area under line
                        pointBackgroundColor: '#0d6efd',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Sembunyikan legenda
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Skor: ' + context.parsed.y.toFixed(1);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                // Tidak perlu Date object lagi karena label sudah dikirim dalam format teks
                                maxRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100 // Tetap skala 100 sesuai penilaian barumu
                        }
                    }
                }
            });
        }

        // --- STRATEGI LOAD UNTUK LIVEWIRE 3 ---

        // 1. Jalankan saat DOM awal ter-load (F5 / Pertama kali login)
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM Ter-load, mencoba render chart.");
            renderPerformanceChart();
        });

        // 2. Jalankan saat sistem navigasi Livewire memuat ulang komponen (Halaman dipanggil/wire:poll)
        document.addEventListener('livewire:load', function() {
            console.log("Livewire komponen dimuat, mencoba render chart.");
            renderPerformanceChart();
        });

        // 3. Tambahan untuk Livewire 3 Turbo/Navigation (wire:navigate)
        document.addEventListener('livewire:navigated', function() {
            console.log("Livewire navigated, mencoba render chart.");
            renderPerformanceChart();
        });

        // Keamanan: Bersihkan saat komponen di-destroy agar tidak memory leak
        document.addEventListener('livewire:destroy', function() {
            if (window.myPerformanceChart instanceof Chart) {
                window.myPerformanceChart.destroy();
                console.log("Livewire destroy, membersihkan memori chart.");
            }
        });
    </script>
</div>
