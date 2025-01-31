<?php
/*
 * File name: BookingRepository.php
 * Last modified: 2024.04.18 at 17:21:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2024
 */

namespace App\Repositories;

use App\Models\Booking;
use App\Traits\QueryToModel;
use Illuminate\Database\Eloquent\Builder ; 
// use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BookingRepository
 * @package App\Repositories
 * @version January 25, 2021, 9:22 pm UTC
 *
 * @method Booking findWithoutFail($id, $columns = ['*'])
 * @method Booking find($id, $columns = ['*'])
 * @method Booking first($columns = ['*'])
 */
class BookingRepository 
//extends BaseRepository
{
    use QueryToModel ;
    
    protected Builder $query; // Propriété pour stocker la requête

    public function __construct()
    {
        $this->query = Booking::query(); // Initialiser la requête ici
    }

    
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'salon',
        'e_service',
        'options',
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
        'hint'
    ];

 
    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return Booking::class;
    }
}