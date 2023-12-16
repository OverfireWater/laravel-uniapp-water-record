<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class w_user_price extends Model
{
    protected $hidden=['uid'];
    public function w_type()
    {
        return $this->hasOne(w_type::class, 'id', 'typeId');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
