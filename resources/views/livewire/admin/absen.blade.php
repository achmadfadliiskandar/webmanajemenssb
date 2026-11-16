<div>
    {{-- Header --}}
    <div class="card border-0 shadow-sm rounded-lg p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold text-dark mb-0">Presensi Kehadiran</h3>
                <p class="text-muted mb-0">Catat kehadiran pemain untuk setiap sesi.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="badge bg-primary px-3 py-2 rounded-pill">
                    <i class="fa fa-calendar-alt me-1"></i> {{ date('d F Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Step 1: Pilih Agenda --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <label class="fw-bold small text-muted text-uppercase mb-2">Pilih Agenda Latihan/Pertandingan</label>
            <select class="form-select form-select-lg shadow-sm border-0 bg-light" wire:model="selected_schedule">
                <option value="">-- Pilih Jadwal --</option>
                @foreach ($schedules as $sch)
                    <option value="{{ $sch->id }}">
                        {{ \Carbon\Carbon::parse($sch->tanggal_agenda)->format('d/m/Y') }} - {{ $sch->judul_agenda }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Step 2: Input Pencarian & Daftar Absensi --}}
    @if ($selected_schedule)
        {{-- Kolom Pencarian Real-time --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <span class="input-group-text bg-white border-0 ps-3">
                        <i class="fa fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-0 py-3"
                        placeholder="Cari nama pemain yang ingin diabsen..." wire:model.debounce.300ms="search">
                    @if ($search)
                        <button class="btn btn-white border-0 text-muted" wire:click="$set('search', '')">
                            <i class="fa fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="ps-4 py-3 border-0">PEMAIN</th>
                            <th class="text-center border-0">STATUS KEHADIRAN</th>
                            <th class="pe-4 border-0">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($players as $player)
                            <tr class="border-bottom" wire:key="player-absen-{{ $player->id }}">
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $player->nama_pemain }}</div>
                                    <small class="text-muted">Posisi: {{ $player->posisi }}</small>
                                </td>
                                <td class="text-center" style="min-width: 280px;">
                                    <div class="btn-group w-100 px-2" role="group">
                                        <input type="radio" class="btn-check" id="h-{{ $player->id }}"
                                            value="hadir" wire:model="attendance.{{ $player->id }}.status">
                                        <label class="btn btn-outline-success border-2 fw-bold"
                                            for="h-{{ $player->id }}">H</label>

                                        <input type="radio" class="btn-check" id="i-{{ $player->id }}"
                                            value="izin" wire:model="attendance.{{ $player->id }}.status">
                                        <label class="btn btn-outline-info border-2 fw-bold"
                                            for="i-{{ $player->id }}">I</label>

                                        <input type="radio" class="btn-check" id="s-{{ $player->id }}"
                                            value="sakit" wire:model="attendance.{{ $player->id }}.status">
                                        <label class="btn btn-outline-warning border-2 fw-bold"
                                            for="s-{{ $player->id }}">S</label>

                                        <input type="radio" class="btn-check" id="a-{{ $player->id }}"
                                            value="alpha" wire:model="attendance.{{ $player->id }}.status">
                                        <label class="btn btn-outline-danger border-2 fw-bold"
                                            for="a-{{ $player->id }}">A</label>
                                    </div>
                                </td>
                                <td class="pe-4">
                                    <input type="text"
                                        class="form-control form-control-sm border-0 bg-light rounded-pill px-3"
                                        placeholder="Catatan..."
                                        wire:model="attendance.{{ $player->id }}.keterangan">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5058/5058046.png" width="80"
                                        class="mb-3 opacity-25">
                                    <h6 class="text-muted">Pemain dengan nama "{{ $search }}" tidak ditemukan.
                                    </h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white p-4 border-0">
                <div class="row align-items-center">
                    <div class="col-md-6 small text-muted">
                        <i class="fa fa-info-circle me-1"></i> Data yang sudah tersimpan akan otomatis terupdate jika
                        diubah.
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        @if (Auth::user()->role === 'admin')
                            @if ($selected_schedule)
                                {{-- Cari data jadwal yang sedang dipilih dari koleksi $schedules --}}
                                @php
                                    $currentSchedule = $schedules->firstWhere('id', $selected_schedule);
                                @endphp

                                @if ($currentSchedule)
                                    @if ($currentSchedule->is_locked)
                                        {{-- Tampilan jika Jadwal SUDAH LEWAT (Terkunci) --}}
                                        <button
                                            class="btn btn-secondary px-5 py-2 rounded-pill shadow fw-bold opacity-50"
                                            disabled>
                                            <i class="fa fa-lock me-2"></i> PRESENSI DITUTUP (SUDAH LEWAT)
                                        </button>
                                    @else
                                        {{-- Tampilan jika Jadwal MASIH AKTIF (Hari ini atau Masa Depan) --}}
                                        <button wire:click.prevent="store" wire:loading.attr="disabled"
                                            class="btn btn-primary px-5 py-2 rounded-pill shadow fw-bold">
                                            <span wire:loading.remove>
                                                <i class="fa fa-save me-2"></i> SIMPAN PRESENSI
                                            </span>
                                            <span wire:loading>
                                                <i class="fa fa-spinner fa-spin me-2"></i> SEDANG MENYIMPAN...
                                            </span>
                                        </button>
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Tampilan saat agenda belum dipilih --}}
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 15px;">
            <div class="py-4">
                <div class="mb-3">
                    <i class="fa fa-user-check text-light" style="font-size: 100px;"></i>
                </div>
                <h5 class="text-secondary fw-bold">Belum Ada Agenda Terpilih</h5>
                <p class="text-muted">Silakan pilih salah satu jadwal agenda di atas untuk memulai sesi absensi pemain.
                </p>
            </div>
        </div>
    @endif
</div>
