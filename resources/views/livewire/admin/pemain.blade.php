<div>
    {{-- Header Section --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0">
                {{ $isCreate ? 'Tambah Pemain Baru' : ($isEdit ? 'Perbarui Data Pemain' : 'Daftar Pemain SSB') }}
            </h3>
            <p class="text-muted small mb-0">Kelola informasi talenta dan statistik pemain secara real-time.</p>
        </div>

        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @if ($isCreate || $isEdit)
                <button wire:click="backToIndex" class="btn btn-white border shadow-sm px-4 fw-bold">
                    <i class="fa fa-arrow-left me-2 text-secondary"></i> Kembali
                </button>
            @else
                {{-- HANYA ADMIN yang bisa melihat tombol Tambah Pemain --}}
                @if(auth()->user()->role === 'admin')
                    <button wire:click="showCreate" class="btn btn-primary shadow-sm px-4 py-2 fw-bold"
                        style="border-radius: 10px;">
                        <i class="fa fa-plus-circle me-2"></i> Tambah Pemain
                    </button>
                @endif
            @endif
        </div>
    </div>

    {{-- Search Bar (Hanya muncul di halaman utama) --}}
    @if (!$isCreate && !$isEdit)
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <span class="input-group-text bg-white border-0"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" wire:model.debounce.500ms="search" class="form-control border-0"
                        placeholder="Cari nama pemain...">
                </div>
            </div>
        </div>
    @endif

    {{-- Alert Section --}}
    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fa fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Main Content --}}
    @if ($isCreate || $isEdit)
        <div class="card border-0 shadow-sm rounded-lg p-4" style="border-radius: 15px;">
            {{-- Pastikan file pemain-form.blade.php sudah ada --}}
            @include('livewire.admin.pemain-form')
        </div>
    @else
        <div class="row">
            @forelse($players as $p)
                <div class="col-md-4 mb-4" wire:key="player-{{ $p->id }}">
                    <div class="card border-0 shadow-sm text-center p-3 h-100 position-relative"
                        style="border-radius: 20px; transition: 0.3s;">
                        
                        {{-- Badge Posisi --}}
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2" style="font-size: 10px; background-color: #e7f1ff;">
                                {{ strtoupper($p->posisi) }}
                            </span>
                        </div>

                        {{-- Foto Profil --}}
                        <div class="my-4">
                            @if($p->foto)
                                <img src="{{ asset('storage/'.$p->foto) }}" class="rounded-circle shadow-sm border border-3 border-white" width="90" height="90" style="object-fit: cover;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($p->nama_pemain) }}&background=1e3c72&color=fff&bold=true"
                                    class="rounded-circle shadow-sm border border-3 border-white" width="90" height="90">
                            @endif
                        </div>

                        {{-- Informasi Nama & KU --}}
                        <h5 class="fw-bold mb-1 text-dark">{{ $p->nama_pemain }}</h5>
                        <div class="mb-3">
                            <span class="badge bg-light text-dark border fw-normal">{{ $p->kelompok_usia }}</span>
                            <small class="text-muted d-block mt-1">ID: {{ $p->no_anggota ?? '-' }}</small>
                        </div>

                        {{-- Stats Singkat --}}
                        <div class="bg-light rounded-4 p-3 mb-3" style="border-radius: 15px;">
                            <div class="row g-0">
                                <div class="col-6 border-end">
                                    <small class="text-muted d-block mb-1">Tinggi</small>
                                    <span class="fw-bold text-dark">{{ $p->tinggi_badan ?? '0' }} <small>cm</small></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Kaki</small>
                                    <span class="fw-bold text-dark text-capitalize">{{ $p->kaki_dominan }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons (BEDA ROLE BEDA TOMBOL) --}}
                        <div class="d-flex gap-2">
                            @if(auth()->user()->role === 'admin')
                                {{-- Admin Bisa Edit dan Hapus --}}
                                <button wire:click="showEdit({{ $p->id }})"
                                    class="btn btn-primary btn-sm w-100 fw-bold py-2 shadow-sm" style="border-radius: 10px;">
                                    <i class="fa fa-edit me-1"></i> Edit
                                </button>
                                <button wire:click="destroy({{ $p->id }})"
                                    onclick="confirm('Yakin ingin menghapus {{ $p->nama_pemain }}?') || event.stopImmediatePropagation()"
                                    class="btn btn-outline-danger btn-sm py-2" style="border-radius: 10px;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @else
                                {{-- Pelatih Hanya Bisa Lihat (Read Only) --}}
                                <button class="btn btn-light btn-sm w-100 fw-bold py-2 text-muted shadow-none" style="border-radius: 10px; cursor: default;" disabled>
                                    <i class="fa fa-lock me-1"></i> Terkunci (Mode Pelatih)
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="opacity-25 mb-3">
                        <i class="fa fa-users fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted fw-normal">Tidak ada pemain ditemukan di kategori Anda.</h5>
                </div>
            @endforelse
        </div>
    @endif
</div>