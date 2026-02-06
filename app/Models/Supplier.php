<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'supplier';

    /**
     * Primary key tabel
     */
    protected $primaryKey = 'id_supplier';

    /**
     * Timestamps
     */
    public $timestamps = false;

    /**
     * Field yang bisa diisi
     */
    protected $fillable = [
        'nama_supplier',
        'telp_supplier',
        'alamat_supplier',
    ];

    /**
     * Relasi ke penerimaan (satu supplier bisa punya banyak penerimaan)
     */
    public function penerimaan()
    {
        return $this->hasMany(Penerimaan::class, 'id_supplier', 'id_supplier');
    }
}
