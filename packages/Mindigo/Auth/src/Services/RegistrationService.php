<?php

namespace Mindigo\Auth\Services;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mindigo\Auth\Models\User;

class RegistrationService
{
    public function register(array $data): User
    {
        try {
            return DB::transaction(fn (): User => User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'password' => $data['password'],
                'is_active' => true,
            ]));
        } catch (UniqueConstraintViolationException $exception) {
            if (! User::query()->where('email', $data['email'])->exists()) {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'email' => __('Mindigo-auth::app.registration.email_taken'),
            ]);
        }
    }
}
