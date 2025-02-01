<?php
/*
 * File name: BookingRepository.php
 * Last modified: 2025.02.01 at 11:47:06
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Repositories;

use App\Models\Booking;
use App\Traits\QueryToModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder ; 
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Contracts\RepositoryInterface;


/**
 * Class BookingRepository
 * @package App\Repositories
 * @version January 25, 2021, 9:22 pm UTC
 *
 * @method Booking findWithoutFail($id, $columns = ['*'])
 * @method Booking find($id, $columns = ['*'])
 * @method Booking first($columns = ['*'])
 */
class BookingRepository  extends BaseRepository implements RepositoryInterface
{
    use QueryToModel ;
    
    
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