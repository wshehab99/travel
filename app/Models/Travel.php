<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Cviebrock\EloquentSluggable\Sluggable;

class Travel extends Model
{
    use HasFactory, Sluggable, HasUuids;
    protected $table = "travels";

    protected $fillable = ['name','description','slug','number_of_days','is_public'];
    protected $appends = ['number_of_nights'];
    public function tours() : HasMany 
    {
        return $this->hasMany(Tour::class);
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    public function numberOfNights() : Attribute 
    {
        return Attribute::make(get: fn($value, $attributes) => $attributes['number_of_days'] - 1);
    }
}
