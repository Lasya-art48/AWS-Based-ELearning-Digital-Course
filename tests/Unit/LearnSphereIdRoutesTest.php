<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LearnSphereIdRoutesTest extends TestCase
{
    public function test_passwordless_sign_in_routes_are_not_registered(): void
    {
        $routes = Route::getRoutes();

        $this->assertNull($routes->getByName('Mindigo-id.send'));
        $this->assertNull($routes->getByName('Mindigo-id.verify-otp'));
        $this->assertNull($routes->getByName('Mindigo-id.verify'));
        $this->assertNotNull($routes->getByName('password.request'));
    }

    public function test_login_page_prompts_for_a_learn_sphere_id_and_password(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('LearnSphere ID')
            ->assertSee('Password')
            ->assertSee(route('register'))
            ->assertDontSee('Sign in with Google')
            ->assertDontSee('sign-in link');
    }

    public function test_registration_routes_are_available_to_guests(): void
    {
        $this->assertNotNull(Route::getRoutes()->getByName('register'));
        $this->assertNotNull(Route::getRoutes()->getByName('register.store'));
        $this->assertNotNull(Route::getRoutes()->getByName('register.complete'));

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
}
