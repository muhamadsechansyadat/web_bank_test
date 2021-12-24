<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rekening;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $fillable = ['rekening_id', 'jumlah', 'tipe', 'tujuan'];

    public function rekening()
    {
        return $this->belongsTo('App\Models\Rekening', 'id', 'rekening_id');
    }
}
