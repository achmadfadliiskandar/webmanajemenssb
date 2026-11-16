<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengubah dari ENUM ke TEXT agar bisa menampung Array/JSON
            $table->text('pelatih_ku')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Balikin ke enum kalau mau rollback (opsional)
            $table->enum('pelatih_ku', ['KU-6', 'KU-10', 'KU-14', 'Senior'])->nullable()->change();
        });
    }
};