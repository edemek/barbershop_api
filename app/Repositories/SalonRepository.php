<?php
/*
 * File name: SalonRepository.php
 * Last modified: 2025.02.01 at 11:47:06
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Repositories;

use App\Criteria\Salons\NearCriteria;
use App\Models\Salon;
use App\Traits\QueryToModel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Foundation\Application;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;

// use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SalonRepository
 * @package App\Repositories
 * @version January 30, 2025, 11:11 am UTC
 *
 * @method Salon findWithoutFail($id, $columns = ['*'])
 * @method Salon first($columns = ['*'])
 */
class SalonRepository extends BaseRepository implements RepositoryInterface
{
    use QueryToModel ;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'salon_level_id',
        'address_id',
        'description',
        'phone_number',
        'mobile_number',
        'availability_range',
        'available',
        'closed',
        'featured'
    ];

    

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return Salon::class;
    }
}