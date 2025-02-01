<?php
/*
 * File name: AvailabilityHourRepository.php
 * Last modified: 2025.02.01 at 11:47:06
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Repositories;

use App\Models\AvailabilityHour;
use App\Traits\QueryToModel;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class AvailabilityHourRepository
 * @package App\Repositories
 * @version January 16, 2021, 4:08 pm UTC
 *
 * @method AvailabilityHour findWithoutFail($id, $columns = ['*'])
 * @method AvailabilityHour find($id, $columns = ['*'])
 * @method AvailabilityHour first($columns = ['*'])
 */
class AvailabilityHourRepository extends BaseRepository
{

    use QueryToModel ;
    
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'day',
        'start_at',
        'end_at',
        'data',
        'salon_id'
    ];

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return AvailabilityHour::class;
    }
}