<div>
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0">
                {{ $isCreate ? 'Tambah Agenda' : ($isEdit ? 'Edit Agenda' : 'Jadwal & Agenda SSB') }}
            </h3>
        </div>
        <div class="col-md-6 text-md-end">
            <button wire:click="{{ $isCreate || $isEdit ? 'resetFields' : 'showCreate' }}"
                class="btn {{ $isCreate || $isEdit ? 'btn-light border' : 'btn-primary' }} shadow-sm">
                <i class="fa {{ $isCreate || $isEdit ? 'fa-arrow-left' : 'fa-plus' }} me-2"></i>
                {{ $isCreate || $isEdit ? 'Kembali' : 'Tambah Agenda' }}
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($isCreate || $isEdit)
        <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
            <form wire:submit.prevent="{{ $isCreate ? 'store' : 'update' }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">JUDUL AGENDA</label>
                        <input type="text" class="form-control" placeholder="Latihan Rutin..." wire:model="judul_agenda">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">STATUS</label>
                        <select class="form-select" wire:model="keterangan">
                            <option value="terjadwal">Terjadwal</option>
                            <option value="tertunda">Tertunda</option>
                            <option value="libur">Libur</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold small">TANGGAL</label>
                        <input type="date" class="form-control" wire:model="tanggal_agenda">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold small">WAKTU MULAI</label>
                        <select class="form-select" wire:model="waktu_mulai">
                            <option value="">-- Pilih Jam --</option>
                            @for($i = 6; $i <= 21; $i++)
                                @php $jam = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                                <option value="{{ $jam }}">{{ $jam }} WIB</option>
                                @php $setengah = str_pad($i, 2, '0', STR_PAD_LEFT) . ':30'; @endphp
                                <option value="{{ $setengah }}">{{ $setengah }} WIB</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold small">WAKTU SELESAI</label>
                        <select class="form-select" wire:model="waktu_selesai">
                            <option value="">-- Pilih Jam --</option>
                            @for($i = 6; $i <= 21; $i++)
                                @php $jam = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                                <option value="{{ $jam }}">{{ $jam }} WIB</option>
                                @php $setengah = str_pad($i, 2, '0', STR_PAD_LEFT) . ':30'; @endphp
                                <option value="{{ $setengah }}">{{ $setengah }} WIB</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold small">LOKASI</label>
                        <input type="text" class="form-control" placeholder="Lapangan..." wire:model="lokasi">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold small">DESKRIPSI / CATATAN</label>
                        <textarea class="form-control" rows="3" wire:model="deskripsi"></textarea>
                    </div>
                </div>
                <div class="text-end">
                    @if (Auth::user()->role === 'admin')
                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow">
                        <i class="fa fa-save me-2"></i> {{ $isCreate ? 'Simpan Agenda' : 'Update Agenda' }}
                    </button>
                    @endif
                </div>
            </form>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-md-12">
                <input type="text" class="form-control shadow-sm border-0 py-2" placeholder="Cari agenda..." wire:model="search">
            </div>
        </div>

        <div class="row">
            @forelse($schedules as $item)
                <div class="col-md-12 mb-3">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid {{ $item->keterangan == 'tertunda' ? '#dc3545' : ($item->tipe_agenda == 'pertandingan' ? '#ffc107' : '#0d6efd') }} !important;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center border-end">
                                    <h4 class="fw-bold mb-0 text-primary">{{ \Carbon\Carbon::parse($item->tanggal_agenda)->format('d') }}</h4>
                                    <small class="text-uppercase fw-bold text-muted">{{ \Carbon\Carbon::parse($item->tanggal_agenda)->translatedFormat('F Y') }}</small>
                                    <div class="mt-2 small bg-light rounded py-1"><i class="fa fa-clock me-1"></i> {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }}</div>
                                </div>
                                <div class="col-md-7 ps-md-4">
                                    <div class="d-flex align-items-center mb-1">
                                        <h5 class="fw-bold mb-0 me-2">{{ $item->judul_agenda }}</h5>
                                        <span class="badge bg-light text-dark border me-1 small text-uppercase">{{ $item->tipe_agenda }}</span>
                                        @if($item->keterangan == 'tertunda')
                                            <span class="badge bg-danger">Tertunda</span>
                                        @elseif($item->keterangan == 'libur')
                                            <span class="badge bg-warning">Libur</span>
                                        @else
                                            <span class="badge bg-success">Terjadwal</span>
                                        @endif
                                    </div>
                                    

                                    <div class="text-muted small">
                                        <span class="me-3"><i class="fa fa-map-marker-alt text-danger me-1"></i> {{ $item->lokasi }}</span>
                                        @if ($item->waktu_selesai)
                                            <span><i class="fa fa-hourglass-end text-warning me-1"></i> Selesai: {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }} WIB</span>
                                        @endif
                                    </div>
                                    <p class="mb-0 mt-2 small text-secondary fst-italic">{{ $item->deskripsi ?? 'Tidak ada catatan.' }}</p>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    @if (Auth::user()->role === 'admin')
                                    <button wire:click="showEdit({{ $item->id }})" class="btn btn-sm btn-outline-primary rounded-pill px-3">Edit</button>
                                    <button onclick="confirm('Hapus?') || event.stopImmediatePropagation()" wire:click="destroy({{ $item->id }})" class="btn btn-sm btn-outline-danger rounded-pill px-3">Hapus</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5"><h5 class="text-muted">Agenda tidak ditemukan.</h5></div>
            @endforelse
        </div>
    @endif
</div>  