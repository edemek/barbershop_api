<?php
/*
 * File name:  OpeningHoursService.php
 * Last modified: 2025.02.01 at 13:53:44
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Packages;

class OpeningHoursService
{
    protected $openingHours = [];
    protected $dateTimeToCheck; // Property to hold the date and time to check

    public function mergeOverlappingRanges(array $openingHoursArray) : OpeningHoursService
    {
        $mergedHours = [];

        foreach ($openingHoursArray as $day => $ranges) {
            // Convert time ranges to arrays of start and end times
            $timeRanges = [];
            foreach ($ranges as $range) {
                list($start, $end) = explode('-', $range);
                $timeRanges[] = [
                    'start' => strtotime($start),
                    'end' => strtotime($end)
                ];
            }

            // Sort the time ranges by start time
            usort($timeRanges, function($a, $b) {
                return $a['start'] <=> $b['start'];
            });

            // Merge overlapping ranges
            $merged = [];
            foreach ($timeRanges as $current) {
                if (empty($merged)) {
                    $merged[] = $current;
                } else {
                    $last = &$merged[count($merged) - 1];
                    if ($current['start'] <= $last['end']) { // Overlapping range
                        // Merge the ranges by updating the end time
                        $last['end'] = max($last['end'], $current['end']);
                    } else {
                        // No overlap, just add the current range
                        $merged[] = $current;
                    }
                }
            }

            // Convert back to original format 'HH:MM-HH:MM'
            $formattedRanges = array_map(function($range) {
                return date('H:i', $range['start']) . '-' . date('H:i', $range['end']);
            }, $merged);

            // Store merged ranges for the day
            $mergedHours[$day] = $formattedRanges;
        }

        // Store merged hours for further use
        $this->openingHours = $mergedHours;

        return $this; 
    }


    public function setDateTimeToCheck($dateTime)
    {
        // Set the date and time to check
        $this->dateTimeToCheck = strtotime($dateTime);
    }

    public function forDate_()
    {
        // Get the day of the week from the stored dateTimeToCheck
        return strtolower(date('l', $this->dateTimeToCheck)); // e.g., 'monday'
    }
    
    private function forDate(\DateTime|string $date)
    {
        $this->dateTimeToCheck = (!$date instanceof \DateTime)? strtotime($date) : strtotime($date->format('Y-m-d H:i:s')); 
        // Get the day of the week (0 for Sunday, 6 for Saturday)
        $dayOfWeek = strtolower(date('l', $this->dateTimeToCheck)); // e.g., 'monday'

        return isset($this->openingHours[$dayOfWeek]) ? 
               $this->openingHours[$dayOfWeek] : 
               []; // Return opening hours for that day or empty array if not set
    }

    public function isOpenAt( $date){
        $this->forDate($date) ;
        return $this->isOpen() ;
    }

    private function isOpen()
    {
        // Get the day of the week and time from the stored dateTimeToCheck
        if (!$this->dateTimeToCheck) {
            return false; // No date set, cannot determine if open
        }

        $dayOfWeek = strtolower(date('l', $this->dateTimeToCheck)); // e.g., 'monday'
        $time = date('H:i', $this->dateTimeToCheck);
        // dd( [$dayOfWeek ,$time ,$this->openingHours ]) ;

        // Check if there are opening hours for that day
        if (!array_key_exists($dayOfWeek, $this->openingHours)) {
            return false; // Closed on this day
        }

        foreach ($this->openingHours[$dayOfWeek] as $range) {
            list($start, $end) = explode('-', $range);
            if ($time >= trim($start) && $time <= trim($end)) {
                return true; // Open during this time range
            }
        }

        return false; // Not open during any of the ranges
    }
}