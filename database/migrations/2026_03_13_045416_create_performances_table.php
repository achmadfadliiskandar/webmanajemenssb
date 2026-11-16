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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            // Relasi ke tabel users (pelatih yang input)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            // Indikator penilaian (Skala 1-100)
            $table->integer('passing')->default(0);
            $table->integer('dribbling')->default(0);
            $table->integer('shooting')->default(0);
            $table->integer('stamina')->default(0);
            $table->integer('physics')->default(0);
            $table->integer('discipline')->default(0);
            $table->decimal('rata_rata', 5, 2);
            $table->text('catatan_pelatih')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
