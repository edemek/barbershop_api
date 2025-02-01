<?php
/*
 * File name: NearCriteria.php
 * Last modified: 2024.01.27 at 21:41:01
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Criteria\Salons;

// use App\Criteria\CriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class NearCriteria.
 *
 * @package namespace App\Criteria\Salons;
 */
class NearCriteria  implements CriteriaInterface 
// ,PCriteriaInterface
{
    /**
     * @var array|Request
     */
    private Request|array $request;

    /**
     * NearCriteria constructor.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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

        if ($this->request->has(['myLat', 'myLon', 'areaLat', 'areaLon'])) {
            $coordination = $this->request->only('myLat', 'myLon', 'areaLat', 'areaLon');
            $coordination = array_values($coordination);
            return $query->near(...$coordination);
        } else if ($this->request->has(['myLat', 'myLon'])) {
            $coordination = $this->request->only('myLat', 'myLon');
            $coordination = array_values($coordination);
            array_push($coordination, ...$coordination);
            return $query->near(...$coordination);
        } else {
            // return $query->orderBy('available');
            return $query;
        }
    }
}