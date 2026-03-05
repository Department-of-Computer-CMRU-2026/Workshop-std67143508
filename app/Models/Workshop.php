<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    /** @use HasFactory<\Database\Factories\WorkshopFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'speaker',
        'location',
        'total_seats',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function getRemainingSeatsAttribute()
    {
        return $this->total_seats - $this->registrations()->count();
    }
}
