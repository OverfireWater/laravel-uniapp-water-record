<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class w_user extends Model
{


    public function w_type()
    {
        return $this->hasMany(w_type::class, 'uid', 'id');
    }
    public function w_user_price()
    {
        return $this->hasMany(w_user_price::class,'uid','id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
