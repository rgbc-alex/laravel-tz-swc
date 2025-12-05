<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Определяет, авторизован ли пользователь для выполнения этого запроса.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получает правила валидации, которые применяются к запросу.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string', Rule::in(['planned', 'in_progress', 'done'])],
            'due_date' => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'attachment' => ['nullable', 'file'],
        ];
    }
}
