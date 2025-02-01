<?php
/*
 * File name:  OpeningHoursService.php
 * Last modified: 2025.02.01 at 14:36:44
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OpeningHoursService extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'opening_hours_service'; // This will be used to resolve the service from the container
    }
}
