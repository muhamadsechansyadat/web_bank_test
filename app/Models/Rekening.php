<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transaksi;

class Rekening extends Model
{
    use HasFactory;
    protected $table = 'rekening';
    protected $fillable = ['jumlah', 'no_rek', 'user_id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function transaksi()
    {
        return $this->hasMany('App\Models\Transaksi', 'rekening_id', 'id');
    }
}
