<?php

declare(strict_types=1);

namespace Tests\Feature\Shared;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class SystemLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_http_request_is_written_to_system_log(): void
    {
        $this->get('/')
            ->assertOk();

        $this->assertDatabaseHas('system_logs', [
            'category' => 'http',
            'method' => 'GET',
            'path' => '/',
            'status_code' => 200,
        ]);
    }

    public function test_unhandled_exception_is_written_to_system_log(): void
    {
        Route::middleware('web')
            ->get(
                '/tests/system-log-exception',
                function (): never {
                    throw new RuntimeException('Тестовая ошибка логирования');
                }
            );

        $this->get('/tests/system-log-exception')
            ->assertStatus(500);

        $this->assertDatabaseHas('system_logs', [
            'category' => 'exception',
            'action' => 'RuntimeException',
            'path' => '/tests/system-log-exception',
        ]);
    }
}
