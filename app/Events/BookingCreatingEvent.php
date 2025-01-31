<?php
/*
 * File name: BookingCreatingEvent.php
 * Last modified: 2025.01.30 at 14:22:50
 * Author: harrykouevi - https://github.com/harrykouevi
 * Copyright (c) 2025
 */

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreatingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        if (!empty($booking->booking_at)) {
            $booking->booking_at = $booking->booking_at;
            // $booking->booking_at = convertDateTime($booking->booking_at);

        }
        if (!empty($booking->start_at)) {
            $booking->start_at = $booking->start_at;
        }
        if (!empty($booking->ends_at)) {
            $booking->ends_at = $booking->ends_at;
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|PrivateChannel|array
     */
    public function broadcastOn(): Channel|PrivateChannel|array
    {
        return new PrivateChannel('channel-name');
    }
}