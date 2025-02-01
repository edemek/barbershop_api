<?php
/*
 * File name: NearCriteria.php
 * Last modified: 2024.01.27 at 21:41:01
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Criteria\AvailabilityHours;

// use App\Criteria\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class NearCriteria.
 *
 * @package namespace App\Criteria\Salons;
 */
class AvailabilityHoursOfUserCriteria  implements CriteriaInterface 
// ,PCriteriaInterface
{
    /**
     * @var ?int
     */
    private ?int $userId;

    /**
     * AvailabilityHoursOfUserCriteria constructor.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }


    /**
     * Apply criteria in query repository
     *
      * @param Builder|Model $query
     * @return Builder|Model
     */
    public function apply($query ,  RepositoryInterface $repository  ): Builder|Model
    {
        return $query;

        if (Auth::check() && Auth::user()->role !== 'salon owner' ) {
            return $query->join('salon_users', 'salon_users.salon_id', '=', 'availability_hours.salon_id')
                ->groupBy('availability_hours.id')
                ->select('availability_hours.*')
                ->where('salon_users.user_id', $this->userId);
        } else {
            return $query;
        }
    }
}