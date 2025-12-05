<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Тестирование регистрации пользователя.
     */
    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /**
     * Тестирование входа пользователя.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create(
            ['password' => bcrypt($password = 'i-love-laravel')]
        );

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }

    /**
     * Тестирование того, что неаутентифицированный пользователь не может получить доступ к задачам.
     */
    public function test_unauthenticated_user_cannot_access_tasks(): void
    {
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(401);
    }

    /**
     * Тестирование получения списка задач пользователем.
     */
    public function test_user_can_get_list_of_tasks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        Task::factory()->count(3)->create(['assignee_id' => $user->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Тестирование фильтрации задач по статусу.
     */
    public function test_user_can_filter_tasks_by_status(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        Task::factory()->create(['status' => 'planned', 'assignee_id' => $user->id]);
        Task::factory()->create(['status' => 'done', 'assignee_id' => $user->id]);

        $response = $this->getJson('/api/tasks?status=planned');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.status', 'planned');
    }

    /**
     * Тестирование создания задачи без вложения.
     */
    public function test_user_can_create_task_without_attachment(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJsonFragment($taskData);
        $this->assertDatabaseHas('tasks', $taskData);
    }

    /**
     * Тестирование создания задачи с вложением.
     */
    public function test_user_can_create_task_with_attachment(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Storage::fake('media');

        $file = UploadedFile::fake()->image('attachment.jpg');
        $taskData = [
            'title' => 'Task with attachment',
            'description' => 'Description here',
            'attachment' => $file,
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'Task with attachment']);
        $task = Task::where('title', 'Task with attachment')->first();
        $this->assertTrue($task->hasMedia());
    }


    /**
     * Тестирование получения одной задачи пользователем.
     */
    public function test_user_can_get_a_single_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create(['assignee_id' => $user->id]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('id', $task->id);
    }

    /**
     * Тестирование обновления задачи пользователем.
     */
    public function test_user_can_update_a_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create(['assignee_id' => $user->id]);
        $updateData = ['title' => 'Updated Title'];

        $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonPath('title', 'Updated Title');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Title']);
    }

    /**
     * Тестирование удаления задачи пользователем.
     */
    public function test_user_can_delete_a_task(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Тестирование валидации при создании задачи.
     */
    public function test_validation_for_creating_task(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson('/api/tasks', ['title' => '']);
        $response->assertStatus(422)->assertJsonValidationErrors('title');
    }
}
