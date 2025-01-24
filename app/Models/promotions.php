<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'salon_id',
        'title',
        'description',
        'discount_percentage',
        'start_date',
        'end_date',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
