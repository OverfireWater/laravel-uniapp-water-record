<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class w_type extends Model
{
    protected $hidden = ['uid'];

    public function w_user_price()
    {
        return $this->belongsTo(w_user_price::class, 'id', 'typeId');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
