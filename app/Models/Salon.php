<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Facades\OpeningHoursService ;
use App\Packages\OpeningHoursService as  OpeningHours ;

class Salon extends Model
{
    use HasFactory;
    public $table = 'salons';


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


    /**
     * get each range of 30 min with open/close salon
     */
    public function weekCalendarRange(Carbon $date, int $employeeId): array
    {
        $period = CarbonPeriod::since($date->subDay()->ceilDay())->minutes(30)->until($date->addDay()->ceilDay()->subMinutes(30));
        $dates = [];
        // Iterate over the period
        foreach ($period as $key => $d) {
            $firstDate = $d->locale('en')->toDateTime();
            $isOpen = $firstDate > new DateTime("now");
            if ($isOpen) {
                $isOpen = $this->openingHours()->isOpenAt($firstDate);
                if ($isOpen && $employeeId != 0) {
                    $isOpen = !($this->bookings()->where('booking_at', '=', $firstDate)
                        ->where('cancel', '<>', '1')
                        ->whereNotIn('booking_status_id', ['6', '7'])
                        ->where('employee_id', '=', $employeeId)
                        ->count());
                }
            }
            $times = $d->locale('en')->toIso8601String();
            $dates[] = [$times, $isOpen];
        }
        return $dates;
    }

    public function openingHours(): OpeningHours
    {
        $openingHoursArray = [];
        foreach ($this->availabilityHours as $element) {
            $openingHoursArray[$element['day']][] = $element['start_at'] . '-' . $element['end_at'];
        }
        return   OpeningHoursService::mergeOverlappingRanges($openingHoursArray);
    }

 

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function services()
    {
        return $this->hasMany(EService::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany
     **/
    public function availabilityHours(): HasMany
    {
        return $this->hasMany(AvailabilityHour::class, 'salon_id')->orderBy('start_at');
    }
}
