<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    use HasFactory;

    protected $table = 'kategori_barang';
    protected $primaryKey = 'id_kategori';
    
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'tarif_asuransi',
        'penanganan_khusus',
    ];

    /**
     * Relasi dengan model Barang
     * Satu kategori dapat memiliki banyak barang
     */
    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_kategori', 'id_kategori');
    }
}