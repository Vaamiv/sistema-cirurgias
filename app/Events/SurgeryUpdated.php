<?php
namespace App\Events;

use App\Models\Surgery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SurgeryUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(public Surgery $surgery) {}

    public function broadcastOn(): Channel {
        return new Channel('surgeries');
    }

    public function broadcastAs(): string { return 'surgery.updated'; }
}
