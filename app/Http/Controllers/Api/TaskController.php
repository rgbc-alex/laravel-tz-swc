<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Mail\TaskCreatedNotification;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    /**
     * Отображение списка задач с фильтрацией.
     */
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::query()
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->assignee_id, fn ($query, $assignee_id) => $query->where('assignee_id', $assignee_id))
            ->when($request->due_date, fn ($query, $due_date) => $query->whereDate('due_date', $due_date))
            ->with('media')
            ->get()
            ->map(fn (Task $task) => $this->formatTaskResponse($task));

        return response()->json($tasks);
    }

    /**
     * Создание новой задачи.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        if ($request->hasFile('attachment')) {
            $task->addMediaFromRequest('attachment')->toMediaCollection();
        }

        if ($task->assignee) {
            Mail::to($task->assignee->email)->send(new TaskCreatedNotification($task));
        }

        return response()->json($this->formatTaskResponse($task), 201);
    }

    /**
     * Отображение конкретной задачи.
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json($this->formatTaskResponse($task));
    }

    /**
     * Обновление задачи.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        if ($request->hasFile('attachment')) {
            $task->clearMediaCollection();
            $task->addMediaFromRequest('attachment')->toMediaCollection();
        }

        return response()->json($this->formatTaskResponse($task));
    }

    /**
     * Удаление задачи.
     */
    public function destroy(Task $task): Response
    {
        $task->delete();

        return response()->noContent();
    }

    /**
     * Форматирует ответ для задачи, добавляя URL вложения.
     */
    private function formatTaskResponse(Task $task): array
    {
        $taskData = $task->load('assignee')->toArray();
        $taskData['attachment_url'] = $task->getFirstMediaUrl();
        return $taskData;
    }
}
