<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id', 
        'room_id', 
        'check_in', 
        'check_out', 
        'status', 
        'total_amount'
    ];

    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payments()       // One to Many
    {
        return $this->hasMany(Payment::class);
    }

    public function services()       // Many to Many
    {
        return $this->belongsToMany(Service::class, 'reservation_services')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
