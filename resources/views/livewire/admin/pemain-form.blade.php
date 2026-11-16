<form wire:submit.prevent="{{ $isCreate ? 'store' : 'update' }}">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="small fw-bold text-uppercase">Nama Pemain</label>
            <input type="text" class="form-control @error('nama_pemain') is-invalid @enderror" wire:model="nama_pemain" placeholder="Nama Lengkap">
            @error('nama_pemain') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="small fw-bold text-uppercase">No Anggota (Opsional)</label>
            <input type="text" class="form-control @error('no_anggota') is-invalid @enderror" wire:model="no_anggota" placeholder="Masukkan No Anggota">
            @error('no_anggota') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-4 mb-3">
            <label class="small fw-bold text-uppercase">Tanggal Lahir</label>
            <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" wire:model="tanggal_lahir">
            @error('tanggal_lahir') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="small fw-bold text-uppercase">Tempat Lahir</label>
            <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" wire:model="tempat_lahir">
            @error('tempat_lahir') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="small fw-bold text-uppercase">Posisi</label>
            <select class="form-select @error('posisi') is-invalid @enderror" wire:model="posisi">
                <option value="">-- Pilih Posisi --</option>
                <option value="goalkeeper">Goalkeeper</option>
                <option value="defensif">Defensif</option>
                <option value="midfielder">Midfielder</option>
                <option value="forward">Forward</option>
            </select>
            @error('posisi') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="small fw-bold text-uppercase">Kaki Dominan</label>
            <select class="form-select @error('kaki_dominan') is-invalid @enderror" wire:model="kaki_dominan">
                <option value="kanan">Kanan</option>
                <option value="kiri">Kiri</option>
                <option value="kiri-kanan">Kiri-Kanan</option>
            </select>
            @error('kaki_dominan') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-4 mb-3">
            <label class="small fw-bold text-uppercase">Tinggi Badan (cm)</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa fa-arrows-alt-v text-muted"></i></span>
                <input type="number" class="form-control @error('tinggi_badan') is-invalid @enderror" wire:model="tinggi_badan" placeholder="Contoh: 170">
                <span class="input-group-text bg-light">cm</span>
            </div>
            @error('tinggi_badan') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="small fw-bold text-uppercase">Nomor Punggung (opsional)</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa fa-arrows-alt-v text-muted"></i></span>
                <input type="number" class="form-control @error('nomor_punggung') is-invalid @enderror" wire:model="nomor_punggung" placeholder="Contoh: 7">
            </div>
            @error('nomor_punggung') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="small fw-bold text-uppercase">Berat Badan (kg)</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa fa-weight text-muted"></i></span>
                <input type="number" class="form-control @error('berat_badan') is-invalid @enderror" wire:model="berat_badan" placeholder="Contoh: 65">
                <span class="input-group-text bg-light">kg</span>
            </div>
            @error('berat_badan') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="small fw-bold text-uppercase">Status Pembayaran</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa fa-receipt text-muted"></i></span>
                <select class="form-select @error('status_pembayaran') is-invalid @enderror" wire:model="status_pembayaran">
                    <option value="">-- Pilih Status --</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum_lunas">Belum Lunas</option>
                    <option value="tunggakan">Tunggakan</option>
                </select>
            </div>
            @error('status_pembayaran') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" style="border-radius: 12px;">
            <i class="fa fa-save me-2"></i> {{ $isCreate ? 'SIMPAN DATA PEMAIN' : 'PERBARUI DATA PEMAIN' }}
        </button>
    </div>
</form>