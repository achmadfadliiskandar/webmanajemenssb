<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Player;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Pemain extends Component
{
    use WithPagination, WithFileUploads;

    // Properti State
    public $isCreate = false;
    public $isEdit = false;
    public $search = '';

    // Properti Form
    public $player_id, $nama_pemain, $no_anggota, $tanggal_lahir, $tempat_lahir;
    public $posisi, $foto, $old_foto, $nomor_punggung, $kaki_dominan = 'kanan';
    public $tinggi_badan, $berat_badan, $status_pembayaran = 'lunas';

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * TAMPILAN UTAMA & FILTER KEAMANAN
     */
    public function render()
    {
        $user = Auth::user();
        
        // 1. Query dasar dengan pencarian nama
        $query = Player::where('nama_pemain', 'like', '%' . $this->search . '%')
                        ->latest()
                        ->get();

        // 2. Filter Data: Pelatih hanya bisa melihat pemain sesuai KU-nya
        if ($user->role === 'pelatih' && is_array($user->pelatih_ku)) {
            $players = $query->filter(function($p) use ($user) {
                return in_array($p->kelompok_usia, $user->pelatih_ku);
            });
        } else {
            // Admin bisa melihat semuanya
            $players = $query;
        }

        return view('livewire.admin.pemain', [
            'players' => $players
        ])->layout('layouts.app', ['judul' => 'Manajemen Pemain']);
    }

    /**
     * KEAMANAN: HANYA ADMIN YANG BISA BUKA FORM TAMBAH
     */
    public function showCreate()
    {
        if (Auth::user()->role !== 'admin') {
            return session()->flash('error', 'Akses ditolak! Anda tidak memiliki izin menambah data.');
        }

        $this->resetFields();
        $this->isCreate = true;
    }

    /**
     * KEAMANAN: HANYA ADMIN YANG BISA SIMPAN DATA
     */
    public function store()
    {
        if (Auth::user()->role !== 'admin') return;

        $this->validate([
            'nama_pemain' => 'required|min:3',
            'tanggal_lahir' => 'required|date',
            'posisi' => 'required',
            'tinggi_badan' => 'nullable|numeric',
            'berat_badan' => 'nullable|numeric',
            'foto' => 'nullable|image|max:1024', // max 1MB
        ]);

        $fotoPath = $this->foto ? $this->foto->store('players', 'public') : null;

        Player::create([
            'nama_pemain' => $this->nama_pemain,
            'no_anggota' => $this->no_anggota,
            'tanggal_lahir' => $this->tanggal_lahir,
            'tempat_lahir' => $this->tempat_lahir,
            'posisi' => $this->posisi,
            'foto' => $fotoPath,
            'nomor_punggung' => $this->nomor_punggung,
            'kaki_dominan' => $this->kaki_dominan,
            'tinggi_badan' => $this->tinggi_badan,
            'berat_badan' => $this->berat_badan,
            'status_pembayaran' => $this->status_pembayaran,
        ]);

        session()->flash('success', 'Pemain berhasil ditambahkan.');
        $this->backToIndex();
    }

    /**
     * KEAMANAN: HANYA ADMIN YANG BISA BUKA FORM EDIT
     */
    public function showEdit($id)
    {
        if (Auth::user()->role !== 'admin') {
            return session()->flash('error', 'Akses ditolak! Anda tidak memiliki izin mengubah data.');
        }

        $p = Player::findOrFail($id);
        $this->player_id = $id;
        $this->nama_pemain = $p->nama_pemain;
        $this->no_anggota = $p->no_anggota;
        $this->tanggal_lahir = date('Y-m-d', strtotime($p->tanggal_lahir));
        $this->tempat_lahir = $p->tempat_lahir;
        $this->posisi = $p->posisi;
        $this->old_foto = $p->foto;
        $this->nomor_punggung = $p->nomor_punggung;
        $this->kaki_dominan = $p->kaki_dominan;
        $this->tinggi_badan = $p->tinggi_badan;
        $this->berat_badan = $p->berat_badan;
        $this->status_pembayaran = $p->status_pembayaran;

        $this->isEdit = true;
    }

    /**
     * KEAMANAN: HANYA ADMIN YANG BISA UPDATE DATA
     */
    public function update()
    {
        if (Auth::user()->role !== 'admin') return;

        $p = Player::findOrFail($this->player_id);

        $fotoPath = $p->foto;
        if ($this->foto) {
            if ($p->foto) Storage::disk('public')->delete($p->foto);
            $fotoPath = $this->foto->store('players', 'public');
        }

        $p->update([
            'nama_pemain' => $this->nama_pemain,
            'no_anggota' => $this->no_anggota,
            'tanggal_lahir' => $this->tanggal_lahir,
            'tempat_lahir' => $this->tempat_lahir,
            'posisi' => $this->posisi,
            'foto' => $fotoPath,
            'nomor_punggung' => $this->nomor_punggung,
            'kaki_dominan' => $this->kaki_dominan,
            'tinggi_badan' => $this->tinggi_badan,
            'berat_badan' => $this->berat_badan,
            'status_pembayaran' => $this->status_pembayaran,
        ]);

        session()->flash('success', 'Data pemain diperbarui.');
        $this->backToIndex();
    }

    /**
     * KEAMANAN: HANYA ADMIN YANG BISA HAPUS DATA
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return session()->flash('error', 'Akses ditolak! Anda tidak memiliki izin menghapus data.');
        }

        $p = Player::findOrFail($id);
        if ($p->foto) Storage::disk('public')->delete($p->foto);
        $p->delete();

        session()->flash('success', 'Pemain berhasil dihapus.');
    }

    public function backToIndex()
    {
        $this->resetFields();
        $this->isCreate = false;
        $this->isEdit = false;
    }

    private function resetFields()
    {
        $this->nama_pemain = '';
        $this->no_anggota = '';
        $this->tanggal_lahir = '';
        $this->foto = null;
        $this->player_id = null;
    }
}