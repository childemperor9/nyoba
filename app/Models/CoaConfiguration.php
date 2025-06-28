<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoaConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'transaction_type', // Pastikan kolom ini ada di migrasi tabel coa_configurations
        'description',
        'details_data',     // Kolom baru untuk menyimpan detail dalam format JSON
    ];

    // Menentukan casting atribut
    // Laravel akan otomatis mengkonversi array PHP menjadi JSON saat disimpan
    // dan JSON dari DB menjadi array PHP saat diambil
    protected $casts = [
        'details_data' => 'array',
    ];

    // Relasi details() yang lama dihapus karena tidak ada model CoaConfigurationDetail terpisah
    // public function details()
    // {
    //     return $this->hasMany(CoaConfigurationDetail::class);
    // }
}