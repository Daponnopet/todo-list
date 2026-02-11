<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filter == 'completed') {
            $query->where('is_completed', true);
        } elseif ($request->filter == 'pending') {
            $query->where('is_completed', false);
        }

        // Urut lama ke baru
        $tasks = $query->orderBy('id', 'asc')
                    ->paginate(3)
                    ->withQueryString();

        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required'
        ]);

        Task::create($request->all());

        return redirect()->route('tasks.index');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required'
        ]);

        $task->update($request->all());

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back();
    }

    public function toggle(Task $task)
    {
        $task->is_completed = !$task->is_completed;
        $task->save();
        return redirect()->back();
    }
}
