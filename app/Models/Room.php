<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'room_type',
        'price_per_night',
        'status'
    ];

    // One-to-Many Relationship
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Get room image based on room type
    public function getImageAttribute(): string
    {
        return match (strtolower($this->room_type)) {
            'standard' => 'standard-room.jpg',
            'deluxe'   => 'deluxe-room.jpg',
            default    => 'luxury-room.jpg',
        };
    }

    // Get room description based on room type
    public function getDescriptionAttribute(): string
    {
        return match (strtolower($this->room_type)) {
            'standard' => 'A cozy and comfortable room for your relaxing stay.',
            'deluxe'   => 'Enjoy extra space and premium room amenities.',
            default    => 'Experience elegance and exceptional comfort.',
        };
    }
}
