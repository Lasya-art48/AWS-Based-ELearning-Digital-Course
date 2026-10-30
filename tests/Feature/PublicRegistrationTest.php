<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mindigo\Auth\Models\User;
use Tests\TestCase;

class PublicRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_linked_from_login_and_excludes_admin_role(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee("Don't have an account?")
            ->assertSee(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Full Name')
            ->assertSee('Email Address')
            ->assertSee('Student')
            ->assertSee('Teacher')
            ->assertSee('Create Account')
            ->assertSee('Already have an account?')
            ->assertDontSee('Administrator');
    }

    public function test_student_can_register_and_sign_in_with_the_generated_id(): void
    {
        $this->post(route('register.store'), [
            'name' => 'New Student',
            'email' => 'new.student@example.com',
            'role' => 'student',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])->assertRedirect(route('register.complete'));

        $user = User::query()->where('email', 'new.student@example.com')->firstOrFail();

        $this->assertSame('LS-STU-1001', $user->learn_sphere_id);
        $this->assertTrue(Hash::check('a-strong-password', $user->password));
        $this->assertGuest();

        $this->get(route('register.complete'))
            ->assertOk()
            ->assertSee($user->learn_sphere_id)
            ->assertSee('Save this ID')
            ->assertDontSee('a-strong-password');

        $this->post(route('login.store'), [
            'learn_sphere_id' => $user->learn_sphere_id,
            'password' => 'a-strong-password',
        ])->assertRedirect(url('/student'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_teacher_can_register_and_receives_a_teacher_id(): void
    {
        $this->post(route('register.store'), [
            'name' => 'New Teacher',
            'email' => 'new.teacher@example.com',
            'role' => 'teacher',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])->assertRedirect(route('register.complete'));

        $user = User::query()->where('email', 'new.teacher@example.com')->firstOrFail();

        $this->assertSame('LS-TCH-1001', $user->learn_sphere_id);
    }

    public function test_duplicate_email_is_rejected_without_creating_an_account(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Duplicate Account',
            'email' => 'existing@example.com',
            'role' => 'student',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::query()->where('email', 'existing@example.com')->count());
    }

    public function test_password_confirmation_and_password_strength_are_validated(): void
    {
        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Invalid Password',
            'email' => 'invalid.password@example.com',
            'role' => 'student',
            'password' => 'short',
            'password_confirmation' => 'different',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'invalid.password@example.com']);
    }

    public function test_public_registration_cannot_create_an_administrator(): void
    {
        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Unauthorized Administrator',
            'email' => 'public.admin@example.com',
            'role' => 'admin',
            'password' => 'a-strong-password',
            'password_confirmation' => 'a-strong-password',
        ])->assertRedirect(route('register'))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'public.admin@example.com']);
    }
}
