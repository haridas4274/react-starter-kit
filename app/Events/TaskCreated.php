<?php

namespace App\Events;

use App\Models\Task;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * Create a new event instance.
     */
    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tasks'),
        ];
    }
    public function broadcastWith()
    {
        return [
            'task' => $this->task
        ];
    }
}
