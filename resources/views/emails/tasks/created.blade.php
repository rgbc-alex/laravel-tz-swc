<x-mail::message>
# Создана новая задача

**Заголовок:** {{ $task->title }}

**Описание:**
{{ $task->description }}

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
