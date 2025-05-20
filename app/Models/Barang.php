<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    
    protected $fillable = [
        'id_kategori',
        'nama_barang',
        'deskripsi',
        'berat',
        'dimensi',
        'status'
    ];

    /**
     * Relasi dengan model KategoriBarang
     * Satu barang hanya memiliki satu kategori
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'id_kategori', 'id_kategori');
    }
} 