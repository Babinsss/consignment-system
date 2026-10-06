<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = [];

    public function consignor()
    {
        return $this->belongsTo(Consignor::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}