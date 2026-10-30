<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mindigo\Auth\Models\User;
use Tests\TestCase;

class AuthLoginProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajax_login_returns_the_role_redirect_for_the_processing_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'password' => 'secure-password',
        ]);

        $this->postJson(route('login.store'), [
            'learn_sphere_id' => $user->learn_sphere_id,
            'password' => 'secure-password',
        ])->assertOk()
            ->assertJsonPath('redirect', url('/student'))
            ->assertJsonStructure(['message', 'redirect']);

        $this->assertAuthenticatedAs($user);
    }

    public function test_ajax_login_failure_returns_validation_errors_for_the_processing_screen(): void
    {
        $user = User::factory()->create([
            'password' => 'secure-password',
        ]);

        $this->postJson(route('login.store'), [
            'learn_sphere_id' => $user->learn_sphere_id,
            'password' => 'incorrect-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('learn_sphere_id');

        $this->assertGuest();
    }
}
