<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Jadwal as Schedule;
use Illuminate\Support\Facades\Auth;

class Jadwal extends Component
{
    public $search, $judul_agenda, $tanggal_agenda, $waktu_mulai, $waktu_selesai, $lokasi, $deskripsi, $keterangan, $schedule_id;
    public $isCreate = false;
    public $isEdit = false;

    public function resetFields()
    {
        $this->judul_agenda = '';
        $this->tanggal_agenda = '';
        $this->waktu_mulai = '';
        $this->waktu_selesai = '';
        $this->lokasi = '';
        $this->deskripsi = '';
        $this->keterangan = 'terjadwal';
        $this->isCreate = false;
        $this->isEdit = false;
    }

    public function showCreate()
    {
        $this->resetFields();
        $this->isCreate = true;
    }

    public function store()
    {
        $this->validate([
            'judul_agenda' => 'required',
            'tanggal_agenda' => 'required|date',
            'waktu_mulai' => 'required',
            'lokasi' => 'required',
        ]);

        Schedule::create([
            'judul_agenda' => $this->judul_agenda,
            'tanggal_agenda' => $this->tanggal_agenda,
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
            'lokasi' => $this->lokasi,
            'deskripsi' => $this->deskripsi,
            'keterangan' => $this->keterangan,
            'user_id' => Auth::id()
        ]);

        session()->flash('success', 'Agenda berhasil ditambahkan!');
        $this->resetFields();
    }

    public function showEdit($id)
    {
        $data = Schedule::findOrFail($id);
        $this->schedule_id = $id;
        $this->judul_agenda = $data->judul_agenda;
        $this->tanggal_agenda = $data->tanggal_agenda;
        $this->waktu_mulai = \Carbon\Carbon::parse($data->waktu_mulai)->format('H:i');
        $this->waktu_selesai = $data->waktu_selesai ? \Carbon\Carbon::parse($data->waktu_selesai)->format('H:i') : '';
        $this->lokasi = $data->lokasi;
        $this->deskripsi = $data->deskripsi;
        $this->keterangan = $data->keterangan;
        $this->isEdit = true;
    }

    public function update()
    {
        $this->validate(['judul_agenda' => 'required', 'tanggal_agenda' => 'required']);
        
        $data = Schedule::findOrFail($this->schedule_id);
        $data->update([
            'judul_agenda' => $this->judul_agenda,
            'tanggal_agenda' => $this->tanggal_agenda,
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
            'lokasi' => $this->lokasi,
            'deskripsi' => $this->deskripsi,
            'keterangan' => $this->keterangan,
            'user_id' => Auth::id()
        ]);

        session()->flash('success', 'Agenda berhasil diperbarui!');
        $this->resetFields();
    }

    public function destroy($id)
    {
        Schedule::find($id)->delete();
        session()->flash('success', 'Agenda berhasil dihapus!');
    }

    public function render()
    {
        $judul = 'Agenda & Jadwal';
        \Carbon\Carbon::setLocale('id');

        $schedules = Schedule::where('judul_agenda', 'like', '%' . $this->search . '%')
                    ->orWhere('lokasi', 'like', '%' . $this->search . '%')
                    ->orderBy('tanggal_agenda', 'asc')
                    ->get();

        return view('livewire.admin.jadwal', ['schedules' => $schedules])
               ->layout('layouts.app', ['judul' => $judul]);
    }
}