<div>
    {{-- Header Card --}}
    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 15px;">
        <div class="row align-items-center">
            <div class="col-md-5">
                <label class="fw-bold small text-muted mb-2">PILIH JADWAL / AGENDA</label>
                <select class="form-select border-0 bg-light" wire:model="selected_schedule">
                    <option value="">-- Pilih Jadwal Latihan --</option>
                    @foreach ($schedules as $sj)
                        <option value="{{ $sj->id }}" {{ $sj->is_locked ? 'disabled' : '' }}>
                            {{ $sj->judul_agenda }}
                            ({{ \Carbon\Carbon::parse($sj->tanggal_agenda)->format('d/m/Y') }})
                            @if ($sj->is_future)
                                [Mendatang]
                            @endif
                            @if ($sj->is_expired)
                                [Kadaluarsa - Lewat 7 Hari]
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-7 text-md-end mt-3 mt-md-0">
                @if ($selected_schedule)
                    {{-- Cek lagi di view apakah jadwal yang terpilih terkunci atau tidak --}}
                    @php $selected = $schedules->firstWhere('id', $selected_schedule); @endphp

                    @if ($selected && !$selected->is_locked)
                        <button wire:click="store" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm fw-bold">
                            <i class="fa fa-save me-2"></i> Simpan Penilaian
                        </button>
                    @else
                        <span class="badge bg-danger p-2 px-3 rounded-pill">
                            <i class="fa fa-lock me-1"></i> Penilaian Ditutup
                        </span>
                    @endif

                    <a href="{{ route('cetak.performa', $selected_schedule) }}" target="_blank"
                        class="btn btn-outline-danger px-4 py-2 rounded-pill shadow-sm ms-2">
                        <i class="fa fa-file-pdf me-2"></i> Cetak PDF
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    @if ($selected_schedule)
        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-uppercase small fw-bold">
                            <th class="ps-4 py-3">Pemain</th>
                            <th class="text-center">PS</th>
                            <th class="text-center">DR</th>
                            <th class="text-center">SH</th>
                            <th class="text-center">FS</th>
                            <th class="text-center">ST</th>
                            <th class="text-center">DS</th>
                            <th class="px-4">Catatan Pelatih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($players as $player)
                            <tr wire:key="row-{{ $player->id }}">
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $player->nama_pemain }}</div>
                                    <span class="badge bg-soft-primary text-primary small"
                                        style="background-color: #e7f1ff;">{{ $player->kelompok_usia }}</span>
                                </td>
                                <td class="text-center" style="width: 80px;">
                                    <input type="number" class="form-control form-control-sm text-center"
                                        wire:model.defer="scores.{{ $player->id }}.passing">
                                </td>
                                <td class="text-center" style="width: 80px;">
                                    <input type="number" class="form-control form-control-sm text-center"
                                        wire:model.defer="scores.{{ $player->id }}.dribbling">
                                </td>
                                <td class="text-center" style="width: 80px;">
                                    <input type="number" class="form-control form-control-sm text-center"
                                        wire:model.defer="scores.{{ $player->id }}.shooting">
                                </td>
                                <td class="text-center" style="width: 80px;">
                                    <input type="number" class="form-control form-control-sm text-center"
                                        wire:model.defer="scores.{{ $player->id }}.physics">
                                </td>
                                <td class="text-center" style="width: 80px;">
                                    <input type="number" class="form-control form-control-sm text-center"
                                        wire:model.defer="scores.{{ $player->id }}.stamina">
                                </td>
                                <td class="text-center" style="width: 80px;">
                                    <input type="number" class="form-control form-control-sm text-center"
                                        wire:model.defer="scores.{{ $player->id }}.discipline">
                                </td>
                                <td class="px-4">
                                    <input type="text" class="form-control form-control-sm"
                                        placeholder="Contoh: Stamina oke"
                                        wire:model.defer="scores.{{ $player->id }}.catatan_pelatih">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Tidak ada pemain yang hadir di jadwal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="small text-muted mt-2 ps-2">* PS: Passing, DR: Dribbling, SH: Shooting, FS: physics, DS: Disiplin</p>
    @else
        <div class="text-center py-5 bg-white shadow-sm rounded-lg" style="border-radius: 15px;">
            <i class="fa fa-clipboard-list fa-3x text-light mb-3"></i>
            <p class="text-muted">Pilih jadwal di atas untuk menilai pemain yang hadir.</p>
        </div>
    @endif
</div>
