<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mindigo\Auth\Mail\PasswordResetOtpMail;
use Mindigo\Auth\Models\User;
use Tests\TestCase;

class LearnSphereIdAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_ids_are_role_based_and_unique(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $secondStudent = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertMatchesRegularExpression('/^LS-STU-\d{4,}$/', $student->learn_sphere_id);
        $this->assertSame('LS-STU-1002', $secondStudent->learn_sphere_id);
        $this->assertMatchesRegularExpression('/^LS-TCH-\d{4,}$/', $teacher->learn_sphere_id);
        $this->assertMatchesRegularExpression('/^LS-ADM-\d{4,}$/', $admin->learn_sphere_id);
        $this->assertSame('LS-STU-1001', $student->learn_sphere_id);
        $this->assertSame('LS-TCH-1001', $teacher->learn_sphere_id);
        $this->assertSame('LS-ADM-1001', $admin->learn_sphere_id);
        $this->assertSame(
            4,
            User::query()->whereIn('id', [$student->id, $secondStudent->id, $teacher->id, $admin->id])->distinct()->count('learn_sphere_id')
        );
    }

    public function test_database_rejects_duplicate_learn_sphere_ids(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->expectException(QueryException::class);

        DB::table('users')
            ->where('id', $second->id)
            ->update(['learn_sphere_id' => $first->learn_sphere_id]);
    }

    public function test_successful_login_uses_learn_sphere_id_and_redirects_by_role(): void
    {
        $expectedRedirects = [
            'student' => '/student',
            'teacher' => '/teacher',
            'admin' => '/dashboard',
        ];

        foreach ($expectedRedirects as $role => $path) {
            $user = User::factory()->create([
                'role' => $role,
                'password' => 'secure-password',
            ]);

            $this->post(route('login.store'), [
                'learn_sphere_id' => $user->learn_sphere_id,
                'password' => 'secure-password',
            ])->assertRedirect(url($path));

            $this->assertAuthenticatedAs($user);
            auth()->logout();
        }
    }

    public function test_role_change_assigns_an_id_with_the_new_role_prefix(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $originalId = $user->learn_sphere_id;

        $user->update(['role' => 'teacher']);
        $user->refresh();

        $this->assertNotSame($originalId, $user->learn_sphere_id);
        $this->assertMatchesRegularExpression('/^LS-TCH-\d{4,}$/', $user->learn_sphere_id);
    }

    public function test_admin_account_creation_assigns_id_and_hashes_the_initial_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New Teacher',
            'email' => 'new.teacher@example.com',
            'password' => 'initial-secure-password',
            'password_confirmation' => 'initial-secure-password',
            'role' => 'teacher',
            'is_active' => true,
        ]);

        $created = User::query()->where('email', 'new.teacher@example.com')->firstOrFail();

        $response->assertRedirect(route('users.show', $created));
        $this->assertMatchesRegularExpression('/^LS-TCH-\d{4,}$/', $created->learn_sphere_id);
        $this->assertTrue(Hash::check('initial-secure-password', $created->password));
        $this->get(route('users.show', $created))->assertSee($created->learn_sphere_id);
    }

    public function test_email_otp_password_reset_updates_password_without_removing_the_flow(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'reset.user@example.com',
            'password' => 'old-password',
        ]);

        $this->postJson(route('password.send-otp'), ['email' => $user->email])
            ->assertOk();

        $otpMail = null;
        Mail::assertSent(PasswordResetOtpMail::class, function (PasswordResetOtpMail $mail) use ($user, &$otpMail): bool {
            $otpMail = $mail;

            return $mail->email === $user->email;
        });
        $this->assertNotNull($otpMail);

        $this->postJson(route('password.verify-otp'), [
            'email' => $user->email,
            'otp' => $otpMail->otp,
        ])->assertOk();

        $this->postJson(route('password.reset'), [
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertOk();

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }
}
