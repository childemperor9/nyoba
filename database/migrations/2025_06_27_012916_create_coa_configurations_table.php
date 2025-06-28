<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoaConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coa_configurations', function (Blueprint $table) {
            $table->id();
            // Menambahkan kolom 'name' sebagai string, unik, dan tidak boleh kosong
            $table->string('name')->unique();
            // Menambahkan kolom 'description' sebagai teks dan boleh kosong (nullable)
            $table->text('description')->nullable();
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coa_configurations');
    }
}