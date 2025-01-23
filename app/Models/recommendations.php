<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'recommended_service',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
