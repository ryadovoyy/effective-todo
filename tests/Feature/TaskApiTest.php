<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    private const TASK_JSON_STRUCTURE = [
        'id',
        'title',
        'description',
        'status',
        'created_at',
        'updated_at',
    ];

    use RefreshDatabase;

    public function test_can_create_task(): void
    {
        $payload = [
            'title' => 'Buy groceries',
            'description' => 'Milk, eggs, bread',
            'status' => TaskStatus::PENDING->value,
        ];

        $response = $this->postJson('/tasks', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => self::TASK_JSON_STRUCTURE])
            ->assertJsonPath('data.title', 'Buy groceries')
            ->assertJsonPath('data.description', 'Milk, eggs, bread')
            ->assertJsonPath('data.status', TaskStatus::PENDING->value);

        $this->assertDatabaseHas('tasks', $payload);
    }

    public function test_validation_errors_on_create(): void
    {
        $response = $this->postJson('/tasks', [
            'title' => '',
            'status' => 'not-a-valid-status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_can_get_all_tasks(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [self::TASK_JSON_STRUCTURE]]);
    }

    public function test_can_get_single_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson('/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => self::TASK_JSON_STRUCTURE])
            ->assertJsonPath('data.id', $task->id);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create(['title' => 'Old title']);

        $payload = ['title' => 'New title', 'status' => TaskStatus::COMPLETED->value];

        $response = $this->putJson('/tasks/' . $task->id, $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => self::TASK_JSON_STRUCTURE])
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.title', 'New title')
            ->assertJsonPath('data.status', TaskStatus::COMPLETED->value);

        $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $payload));
    }

    public function test_validation_errors_on_update(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson('/tasks/' . $task->id, [
            'title' => '',
            'status' => 'not-a-valid-status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson('/tasks/' . $task->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
