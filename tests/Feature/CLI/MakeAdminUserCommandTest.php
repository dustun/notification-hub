<?php

declare(strict_types=1);

namespace Tests\Feature\CLI;

use App\Auth\Infrastructure\Models\EloquentUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MakeAdminUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_be_created_via_project_command(): void
    {
        $exitCode = Artisan::call('app:make-admin', [
            '--name' => 'Admin User',
            '--email' => 'admin@example.com',
            '--password' => 'StrongPassword123!',
        ]);

        $this->assertSame(0, $exitCode);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'name' => 'Admin User',
        ]);

        $this->assertTrue(EloquentUser::query()->where('email', 'admin@example.com')->exists());
    }
}
