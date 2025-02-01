<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        DB::table('booking_statuses')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'status' => 'Received',
                    'order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),
            1 =>
                array(
                    'id' => 2,
                    'status' => 'In Progress',
                    'order' => 40,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),
            2 =>
                array(
                    'id' => 3,
                    'status' => 'On the Way',
                    'order' => 20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),
            3 =>
                array(
                    'id' => 4,
                    'status' => 'Accepted',
                    'order' => 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),
            4 =>
                array(
                    'id' => 5,
                    'status' => 'Ready',
                    'order' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),
            5 =>
                array(
                    'id' => 6,
                    'status' => 'Done',
                    'order' => 50,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),
            6 =>
                array(
                    'id' => 7,
                    'status' => 'Failed',
                    'order' => 60,
                    'created_at' => now(),
                    'updated_at' => now(),
                ),
        ));


    }
}
