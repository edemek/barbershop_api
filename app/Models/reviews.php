<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'salon_id',
        'rating',
        'comment',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
