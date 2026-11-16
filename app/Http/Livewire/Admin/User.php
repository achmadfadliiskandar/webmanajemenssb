<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class User extends Component
{
    use WithPagination;

    // Properti State Tampilan
    public $isCreate = false;
    public $isEdit   = false;
    public $search   = '';

    // Properti Form (Model Binding)
    public $user_id, $name, $email, $password, $role = 'pelatih', $is_active = 'active';
    public $pelatih_ku = []; // Pastikan inisialisasi sebagai array kosong

    // Reset pagination kalau lagi ngetik di search bar
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Fungsi Munculkan Form Tambah
    public function showCreate()
    {
        // Proteksi: Hanya Admin yang bisa tambah user baru
        if (Auth::user()->role !== 'admin') {
            return session()->flash('error', 'Akses ditolak! Anda tidak punya izin mengelola pengguna.');
        }

        $this->resetFields();
        $this->isCreate = true;
        $this->isEdit   = false;
    }

    // Fungsi Munculkan Form Edit
    public function showEdit($id)
    {
        // Proteksi: Hanya Admin yang bisa edit user
        if (auth()->user()->role !== 'admin' && $id !== auth()->id()) {
            return session()->flash('error', 'Akses ditolak! Anda tidak punya izin mengelola pengguna.');
        }

        $user = UserModel::findOrFail($id);
        
        $this->user_id    = $id;
        $this->name       = $user->name;
        $this->email      = $user->email;
        $this->role       = $user->role;
        $this->is_active  = $user->is_active;
        $this->pelatih_ku = $user->pelatih_ku ?? []; // Ambil data array KU
        $this->password   = ''; 

        $this->isEdit   = true;
        $this->isCreate = false;
    }

    // Fungsi Reset Inputan & Kembali ke Daftar
    public function resetFields()
    {
        $this->user_id    = null;
        $this->name       = '';
        $this->email      = '';
        $this->password   = '';
        $this->role       = 'pelatih';
        $this->is_active  = 'active';
        $this->pelatih_ku = [];
        
        $this->isCreate   = false;
        $this->isEdit     = false;
    }

    // Fungsi Simpan Data Baru
    public function store()
    {
        if (Auth::user()->role !== 'admin') return;

        $this->validate([
            'name'       => 'required|min:3',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'role'       => 'required',
            'pelatih_ku'   => 'nullable|array', 
            'pelatih_ku.*' => 'in:KU-6,KU-10,KU-14,Senior',
        ]);

        UserModel::create([
            'name'       => $this->name,
            'email'      => $this->email,
            'password'   => Hash::make($this->password),
            'role'       => $this->role,
            'is_active'  => $this->is_active,
            // Jika role baru adalah Admin, paksa pelatih_ku jadi NULL
            'pelatih_ku' => $this->role == 'admin' ? null : $this->pelatih_ku,
        ]);

        session()->flash('success', 'User baru berhasil ditambahkan.');
        $this->resetFields();
    }

    // Fungsi Update Data Lama
    public function update()
    {
        // PROTEKSI DOUBLE: Cek lagi saat akan menyimpan data
        if (auth()->user()->role !== 'admin' && $this->user_id !== auth()->id()) {
            return;
        }

        $this->validate([
            'name'  => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'password' => 'nullable|min:6', // Password opsional saat edit
        ]);

        $user = UserModel::find($this->user_id);
        
        // Data dasar yang boleh diubah semua orang (Nama & Email)
        $data = [
            'name'  => $this->name,
            'email' => $this->email,
        ];

        // LOGIKA KHUSUS: Hanya Admin yang boleh ubah ROLE, STATUS, dan KU
        if (auth()->user()->role === 'admin') {
            $data['role']       = $this->role;
            $data['is_active']  = $this->is_active;
            $data['pelatih_ku'] = ($this->role === 'admin') ? null : $this->pelatih_ku;
        }

        // Jika password diisi, maka di-hash dan dimasukkan ke data update
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        session()->flash('success', 'Profil berhasil diperbarui!');
        $this->resetFields();
    }

    // Fungsi Hapus Data
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return session()->flash('error', 'Akses ditolak!');
        }

        if ($id == Auth::id()) {
            session()->flash('error', 'Anda tidak diperbolehkan menghapus akun Anda sendiri!');
            return;
        }

        UserModel::find($id)->delete();
        session()->flash('success', 'User berhasil dihapus.');
    }

    public function render()
    {
        $users = UserModel::where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->latest()
                    ->get();

        return view('livewire.admin.user', [
            'users' => $users
        ])->layout('layouts.app', ['judul' => 'Manajemen User']);
    }
}