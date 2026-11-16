<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('judul_agenda'); // Contoh: Latihan SSB Beji Putra 21 Agustus 2026 / sparing
            $table->date('tanggal_agenda');
            $table->time('waktu_mulai'); // Lebih efisien: 16:00:00
            $table->time('waktu_selesai')->nullable(); // Lebih efisien: 18:00:00
            $table->string('lokasi');
            $table->enum('keterangan',['terjadwal','tertunda','libur'])->default('terjadwal');
            $table->text('deskripsi')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
