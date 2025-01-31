<?php
/*
 * File name: NearCriteria.php
 * Last modified: 2024.01.27 at 21:41:01
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Criteria;


use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\RepositoryInterface;

interface CriteriaInterface
{
    /**
     * Apply criteria in query repository.
     *
     * @param Builder $query
     * @return Builder
     */
    public function apply(Builder $query , RepositoryInterface $repository): Builder;
}