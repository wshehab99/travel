<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;


class Tour extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name','price','ending_date','starting_date','travel_id'];
    public function price() : Attribute 
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }
}
