<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Gaji Pokok di Jabatan
        Schema::table('positions', function (Blueprint $table) {
            $table->decimal('base_salary', 12, 2)->default(0)->after('description');
        });

        // 2. Tambah Waktu Selesai di Tasks (buat hitung bonus/denda)
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('base_salary');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};