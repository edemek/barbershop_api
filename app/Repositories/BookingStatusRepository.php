<?php
/*
 * File name: BookingStatusRepository.php
 * Last modified: 2025.02.01 at 11:47:06
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Repositories;

use App\Models\BookingStatus;
use Prettus\Repository\Eloquent\BaseRepository;


/**
 * Class BookingStatusRepository
 * @package App\Repositories
 * @version January 25, 2021, 7:18 pm UTC
 *
 * @method BookingStatus findWithoutFail($id, $columns = ['*'])
 * @method BookingStatus find($id, $columns = ['*'])
 * @method BookingStatus first($columns = ['*'])
 */
class BookingStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status',
        'order'
    ];

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return BookingStatus::class;
    }
}