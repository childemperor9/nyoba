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
        Schema::table('coa_configurations', function (Blueprint $table) {
            // Menambahkan kolom 'details_data' dengan tipe JSON
            // Ini akan menyimpan array detail transaksi
            $table->json('details_data')->nullable()->after('description'); // Sesuaikan posisi kolom jika perlu
            // Jika Anda juga ingin menambahkan transaction_type di sini (jika belum ada)
            $table->string('transaction_type')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coa_configurations', function (Blueprint $table) {
            // Menghapus kolom 'details_data' jika migrasi di-rollback
            $table->dropColumn('details_data');
            // Jika Anda menambahkan transaction_type di sini, hapus juga:
            $table->dropColumn('transaction_type');
        });
    }
};