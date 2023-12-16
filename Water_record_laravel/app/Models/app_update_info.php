<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class app_update_info extends Model
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function getSilentAttribute($value)
    {
        return $this->NumberTransitionToChar($value);
    }

    public function getForceAttribute($value)
    {
        return $this->NumberTransitionToChar($value);
    }

    public function getNetCheckAttribute($value)
    {
        return $this->NumberTransitionToChar($value);
    }

    public function getIssueAttribute($value)
    {
        return $this->NumberTransitionToChar($value);
    }

    private function NumberTransitionToChar(int $number): string
    {
        return match ($number) {
            0 => '否',
            1 => '是',
            default => ''
        };
    }
}
