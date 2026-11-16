<div class="container-fluid"> {{-- SATU-SATUNYA DIV INDUK --}}
    
    {{-- HEADER SECTION --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0">
                {{ $isCreate ? 'Tambah User' : ($isEdit ? 'Edit User' : 'Daftar Pengguna') }}
            </h3>
            <p class="text-muted small mb-0">Manajemen hak akses dan profil pengelola SSB.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @if ($isCreate || $isEdit)
                <button wire:click.prevent="resetFields" class="btn btn-light border shadow-sm px-4 fw-bold">
                    <i class="fa fa-arrow-left me-2"></i> Kembali
                </button>
            @else
                {{-- Tombol Tambah: Hanya Admin --}}
                @if(auth()->user()->role === 'admin')
                    <button wire:click.prevent="showCreate" class="btn btn-primary shadow-sm px-4 py-2 fw-bold" style="border-radius: 10px;">
                        <i class="fa fa-plus-circle me-2"></i> Tambah User
                    </button>
                @endif
            @endif
        </div>
    </div>

    {{-- ALERT SECTION --}}
    @if (session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="border-radius: 10px;">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fa fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- MAIN CONTENT --}}
    @if ($isCreate || $isEdit)
        {{-- FORM SECTION --}}
        <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
            <form wire:submit.prevent="{{ $isCreate ? 'store' : 'update' }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">NAMA LENGKAP</label>
                        <input type="text" class="form-control" wire:model="name" placeholder="Nama Lengkap...">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold small">EMAIL</label>
                        <input type="email" class="form-control" wire:model="email" placeholder="email@contoh.com">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold small">PASSWORD {{ $isEdit ? '(Kosongkan jika tidak ganti)' : '' }}</label>
                        <input type="password" class="form-control" wire:model="password">
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold small">ROLE</label>
                        <select class="form-select" wire:model="role" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                            <option value="admin">Admin</option>
                            <option value="pelatih">Pelatih</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold small">STATUS AKUN</label>
                        <select class="form-select" wire:model="is_active" {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                            <option value="active">Aktif</option>
                            <option value="non-active">Non-Aktif</option>
                        </select>
                    </div>

                    {{-- Checkbox KU (Khusus Pelatih) --}}
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold small {{ $role == 'pelatih' ? 'text-primary' : 'text-muted' }}">SPESIALISASI KU</label>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            @foreach (['KU-6', 'KU-10', 'KU-14', 'Senior'] as $ku)
                                <div class="form-check" wire:key="ku-check-{{ $ku }}">
                                    <input class="form-check-input" type="checkbox" value="{{ $ku }}" 
                                           wire:model="pelatih_ku" id="ku_{{ $ku }}" 
                                           {{ $role == 'admin' || auth()->user()->role !== 'admin' ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="ku_{{ $ku }}">{{ $ku }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="fa fa-save me-2"></i> {{ $isCreate ? 'Simpan User' : 'Perbarui Profil' }}
                    </button>
                </div>
            </form>
        </div>
    @else
        {{-- LIST SECTION (CARDS) --}}
        <div class="input-group mb-4 shadow-sm" style="border-radius: 10px; overflow: hidden;">
            <span class="input-group-text bg-white border-0"><i class="fa fa-search text-muted"></i></span>
            <input type="text" class="form-control border-0 py-2" placeholder="Cari berdasarkan nama atau email..." wire:model="search">
        </div>

        <div class="row">
            @forelse ($users as $u)
                <div class="col-md-4 mb-4" wire:key="user-card-{{ $u->id }}">
                    <div class="card border-0 shadow-sm text-center p-3 h-100 position-relative" style="border-radius: 15px;">
                        
                        {{-- TOMBOL AKSI: Pojok Kanan Atas --}}
                        <div class="position-absolute top-0 end-0 p-2 d-flex gap-1" style="z-index: 10;">
                            {{-- Edit: Admin ATAU Milik Sendiri --}}
                            @if(auth()->user()->role === 'admin' || $u->id === auth()->id())
                                <button wire:click.prevent="showEdit({{ $u->id }})" type="button" class="btn btn-sm btn-light text-primary rounded-circle shadow-sm">
                                    <i class="fa fa-edit"></i>
                                </button>
                            @endif

                            {{-- Hapus: Hanya Admin & Bukan Diri Sendiri --}}
                            @if(auth()->user()->role === 'admin' && $u->id !== auth()->id())
                                <button wire:click.prevent="destroy({{ $u->id }})" onclick="confirm('Yakin ingin menghapus user ini?') || event.stopImmediatePropagation()" type="button" class="btn btn-sm btn-light text-danger rounded-circle shadow-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                        </div>

                        {{-- Avatar & Info --}}
                        <div class="my-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background={{ $u->role == 'admin' ? 'dc3545' : '0d6efd' }}&color=fff&bold=true" class="rounded-circle shadow-sm" width="70">
                        </div>
                        <h5 class="fw-bold mb-0 text-truncate px-3">{{ $u->name }}</h5>
                        <small class="text-muted d-block mb-3">{{ $u->email }}</small>

                        <div class="mt-auto">
                            <span class="badge bg-{{ $u->role == 'admin' ? 'danger' : 'primary' }} shadow-sm">
                                {{ strtoupper($u->role) }}
                                @if ($u->role === 'pelatih' && is_array($u->pelatih_ku))
                                    - {{ implode(', ', $u->pelatih_ku) }}
                                @endif
                            </span>
                            <span class="badge bg-{{ $u->is_active == 'active' ? 'success' : 'secondary' }} shadow-sm">
                                {{ ucfirst($u->is_active) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada pengguna ditemukan.</p>
                </div>
            @endforelse
        </div>
    @endif
</div>