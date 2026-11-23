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
        'total_amount',
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

    // Total of all payments marked as paid (refunded payments do not count)
    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments->where('status', 'paid')->sum('amount');
    }

    // What is still unpaid. A cancelled reservation owes nothing.
    public function getBalanceAttribute(): float
    {
        if ($this->status === 'cancelled') {
            return 0.0;
        }

        return max((float) $this->total_amount - $this->amount_paid, 0);
    }

    // Can only be cancelled while pending or confirmed, and before check-in day has passed
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->check_in->gte(today());
    }
}