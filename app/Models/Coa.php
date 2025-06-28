<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai konvensi Laravel (plural dari nama model)
    protected $table = 'coa';

    // Tentukan kolom yang bisa diisi (fillable) atau dijaga (guarded)
    protected $fillable = [
        'acc_num',
        'acc_name',
    ];
}
