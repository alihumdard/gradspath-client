<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Notifications\QueuedStudentAccountCreated;
use App\Notifications\QueuedMentorAccountCreated;
use Modules\Auth\app\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(DatabaseTransactions::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Role::findOrCreate('student', 'web');
    Role::findOrCreate('mentor', 'web');
    Role::findOrCreate('admin', 'web');
});

function createAdminUserForManualCreation(): User
{
    $admin = User::factory()->create([
        'email' => 'admin-'.Str::uuid().'@example.com',
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    $admin->assignRole('admin');
    return $admin;
}

test('admin can manually create a student account and dispatch email notification', function () {
    Notification::fake();
    $admin = createAdminUserForManualCreation();

    $email = 'student-'.Str::uuid().'@example.com';
    $password = 'SecretPassword123!';

    $adminPath = config('auth.admin_path', 'admin');
    $response = $this->actingAs($admin)
        ->post("/{$adminPath}/users", [
            'name' => 'John Student',
            'email' => $email,
            'password' => $password,
            'program_level' => 'undergrad',
            'program_type' => 'mba',
            'institution_text' => 'Stanford University',
        ], [
            'Accept' => 'application/json',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Student account for John Student created successfully and login details sent via email.');

    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('John Student');
    expect($user->is_active)->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->hasRole('student'))->toBeTrue();

    expect($user->studentProfile)->not->toBeNull();
    expect($user->studentProfile->program_level)->toBe('undergrad');
    expect($user->studentProfile->program_type)->toBe('mba');
    expect($user->studentProfile->institution_text)->toBe('Stanford University');

    expect($user->credit)->not->toBeNull();
    expect($user->setting)->not->toBeNull();

    Notification::assertSentTo($user, QueuedStudentAccountCreated::class, function ($notification) use ($password) {
        return $notification->plainPassword === $password;
    });

    $log = DB::table('admin_logs')->where('action', 'create_student')->where('target_id', $user->id)->first();
    expect($log)->not->toBeNull();
});

test('admin can manually create a mentor account and dispatch email notification', function () {
    Notification::fake();
    $admin = createAdminUserForManualCreation();

    $uni = \Modules\Institutions\app\Models\University::query()->create([
        'name' => 'MIT',
        'slug' => 'mit-'.Str::random(6),
        'country' => 'United States',
        'is_active' => true,
    ]);
    $prog = \Modules\Institutions\app\Models\UniversityProgram::query()->create([
        'university_id' => $uni->id,
        'program_type' => 'mba',
        'program_name' => 'Computer Science',
        'degree' => 'MS',
        'is_active' => true,
    ]);

    $email = 'mentor-'.Str::uuid().'@example.com';
    $password = 'MentorPassword123!';

    $adminPath = config('auth.admin_path', 'admin');
    $response = $this->actingAs($admin)
        ->post("/{$adminPath}/mentors", [
            'name' => 'Dr. Mentor Test',
            'email' => $email,
            'password' => $password,
            'mentor_type' => 'graduate',
            'title' => 'PhD Candidate at MIT',
            'grad_school_display' => 'MIT Graduate School',
            'university_id' => $uni->id,
            'university_program_id' => $prog->id,
            'status' => 'active',
            'edu_email' => 'test@mit.edu',
        ], [
            'Accept' => 'application/json',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'Mentor account for Dr. Mentor Test created successfully and login details sent via email.');

    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Dr. Mentor Test');
    expect($user->is_active)->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->hasRole('mentor'))->toBeTrue();

    expect($user->mentor)->not->toBeNull();
    expect($user->mentor->mentor_type)->toBe('graduate');
    expect($user->mentor->title)->toBe('PhD Candidate at MIT');
    expect($user->mentor->grad_school_display)->toBe('MIT Graduate School');
    expect($user->mentor->status)->toBe('active');
    expect($user->mentor->edu_email)->toBe('test@mit.edu');

    expect($user->credit)->not->toBeNull();
    expect($user->setting)->not->toBeNull();

    Notification::assertSentTo($user, QueuedMentorAccountCreated::class, function ($notification) use ($password) {
        return $notification->plainPassword === $password;
    });

    $log = DB::table('admin_logs')->where('action', 'create_mentor')->where('target_id', $user->mentor->id)->first();
    expect($log)->not->toBeNull();
});

test('manual creation fails validation for duplicate email and short password', function () {
    $admin = createAdminUserForManualCreation();
    $existing = User::factory()->create([
        'email' => 'existing-'.Str::uuid().'@example.com',
    ]);

    $adminPath = config('auth.admin_path', 'admin');
    $response = $this->actingAs($admin)
        ->post("/{$adminPath}/users", [
            'name' => 'Test User',
            'email' => $existing->email,
            'password' => 'short',
        ], [
            'Accept' => 'application/json',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('non-admin user cannot access manual creation endpoints', function () {
    $student = User::factory()->create(['is_active' => true, 'email_verified_at' => now()]);
    $student->assignRole('student');

    $adminPath = config('auth.admin_path', 'admin');

    $this->actingAs($student)
        ->post("/{$adminPath}/users", [
            'name' => 'Attempt User',
            'email' => 'attempt@example.com',
            'password' => 'password123',
        ], ['Accept' => 'application/json'])
        ->assertStatus(403);

    $this->actingAs($student)
        ->post("/{$adminPath}/mentors", [
            'name' => 'Attempt Mentor',
            'email' => 'attemptmentor@example.com',
            'password' => 'password123',
            'mentor_type' => 'graduate',
        ], ['Accept' => 'application/json'])
        ->assertStatus(403);
});

test('admin can create account with empty password and system auto-generates password', function () {
    Notification::fake();
    $admin = createAdminUserForManualCreation();

    $email = 'autopass-'.Str::uuid().'@example.com';
    $adminPath = config('auth.admin_path', 'admin');

    $response = $this->actingAs($admin)
        ->post("/{$adminPath}/users", [
            'name' => 'Auto Pass Student',
            'email' => $email,
        ], ['Accept' => 'application/json']);

    $response->assertStatus(201);

    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull();

    Notification::assertSentTo($user, QueuedStudentAccountCreated::class, function ($notification) {
        return !empty($notification->plainPassword) && strlen($notification->plainPassword) >= 8;
    });
});

test('admin-created user can log in and bypasses email verification notice', function () {
    $admin = createAdminUserForManualCreation();
    $email = 'logintest-'.Str::uuid().'@example.com';
    $password = 'SecretPassword123!';

    $adminPath = config('auth.admin_path', 'admin');
    $this->actingAs($admin)
        ->post("/{$adminPath}/users", [
            'name' => 'Direct Login Student',
            'email' => $email,
            'password' => $password,
        ], ['Accept' => 'application/json'])
        ->assertStatus(201);

    // Ensure logged out from admin
    auth()->logout();

    // Now log in as the newly created student
    $loginResponse = $this->post(route('auth.login.post'), [
        'email' => $email,
        'password' => $password,
    ]);

    $loginResponse->assertRedirect(route('student.dashboard'));
    $loginResponse->assertSessionHasNoErrors();
});
