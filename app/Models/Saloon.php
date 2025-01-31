<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'owner_id',
        'seats_available',
        'opening_hours',

        'salon_level_id',
        'address_id',
        'description',
        'phone_number',
        'mobile_number',
        'availability_range',
        'available',
        'featured',
        'accepted'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static array $rules = [
        'name' => 'required|max:127',
        // 'salon_level_id' => 'required|exists:salon_levels,id',
        // 'address_id' => 'required|exists:addresses,id',
        'address' => 'required|string',
        'phone_number' => 'max:50',
        // 'mobile_number' => 'max:50',
        'seats_available' => 'required|numeric|max:9999999.99|min:0.01'
        // 'availability_range' => 'required|numeric|max:9999999.99|min:0.01'
    ];

    protected $casts = [
        'opening_hours' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
