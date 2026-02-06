<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukKategori extends Model
{
    protected $table = 'produk_kategori';
    protected $primaryKey = 'id_produk_kategori';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori'
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_produk_kategori', 'id_produk_kategori');
    }
}
