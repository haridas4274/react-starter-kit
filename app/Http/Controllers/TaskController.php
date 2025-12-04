<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Events\TaskCreated;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        return Inertia::render('Tasks/Index', [
            'liveTasks' => Task::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);
        $task = Task::create($data);
        // Broadcast event
        // event()
        // broadcast(new TaskCreated($task))->toOthers();
        TaskCreated::dispatch($task);
        return redirect()->back();
    }
}
