<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['sometimes', 'string', Rule::in(['planned', 'in_progress', 'done'])],
            'due_date' => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'attachment' => ['nullable', 'file'],
        ];
    }
}
