<?php
/*
 * File name: Booking.php
 * Last modified: 2024.04.18 at 17:53:44
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2024
 */

namespace App\Models;

// use App\Casts\EServiceCollectionCast;
// use App\Casts\OptionCollectionCast;
// use App\Casts\TaxCollectionCast;
use App\Events\BookingCreatingEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Booking
 * @package App\Models
 * @version January 25, 2021, 9:22 pm UTC
 *
 * @property int id
 * @property User user
 * @property BookingStatus bookingStatus
 * @property Payment payment
 * @property Salon salon
 * @property EService[] e_services
 * @property Option[] options
 * @property integer quantity
 * @property integer user_id
 * @property integer address_id
 * @property integer booking_status_id
 * @property integer payment_status_id
 * @property Address address
 * @property integer payment_id
 * @property double duration
 * @property boolean at_salon
 * @property Coupon coupon
 * @property Tax[] taxes
 * @property \DateTime booking_at
 * @property \DateTime start_at
 * @property \DateTime ends_at
 * @property string hint
 * @property boolean cancel
 */
class Booking extends Model
{

    use HasFactory;
    public $table = 'bookings';

    public $fillable = [
        'salon',
        'e_services',
        'options',
        'quantity',
        'user_id',
        'employee_id',
        'booking_status_id',
        'address',
        'payment_id',
        'coupon',
        'taxes',
        'booking_at',
        'start_at',
        'ends_at',
        'hint',
        'cancel'
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static array $rules = [
        'user_id' => 'required|exists:users,id', //le client qui souhaite prendre la reservation
        'employee_id' => 'nullable|exists:users,id',
        'salon_id' => 'required|exists:salons,id',
        'booking_status_id' => 'required|exists:booking_statuses,id',
        'payment_id' => 'nullable|exists:payments,id'
    ];


    public function customFieldsValues(): MorphMany
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }


    /**
     * @return BelongsTo
     **/
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     **/

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * @return BelongsTo
     **/
    public function bookingStatus(): BelongsTo
    {
        return $this->belongsTo(BookingStatus::class, 'booking_status_id', 'id');
    }

}