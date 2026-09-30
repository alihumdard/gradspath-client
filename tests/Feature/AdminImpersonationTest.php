<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Lab404\Impersonate\Services\ImpersonateManager;
use Modules\Auth\app\Models\User;
use Modules\Settings\app\Models\Mentor;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(DatabaseTransactions::class);

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    Role::findOrCreate('student', 'web');
    Role::findOrCreate('mentor', 'web');
    Role::findOrCreate('admin', 'web');
});

function createImpersonationTestUser(string $role, array $attributes = []): User
{
    /** @var User $user */
    $user = User::factory()->create(array_merge([
        'email' => "{$role}-".Str::uuid().'@example.com',
        'is_active' => true,
        'email_verified_at' => now(),
    ], $attributes));

    $user->assignRole($role);

    if ($role === 'mentor') {
        Mentor::create([
            'user_id' => $user->id,
            'status' => 'active',
            'approved_at' => now(),
            'program_type' => 'mba',
            'headline' => 'Experienced MBA Mentor',
        ]);
    }

    return $user;
}

test('admin can impersonate a student and is redirected to student dashboard', function () {
    $admin = createImpersonationTestUser('admin');
    $student = createImpersonationTestUser('student');

    $response = $this->actingAs($admin)
        ->get(route('admin.impersonate', $student->id));

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('info');

    $manager = app(ImpersonateManager::class);
    expect($manager->isImpersonating())->toBeTrue();
    expect(Auth::id())->toBe($student->id);
    expect($manager->getImpersonatorId())->toBe($admin->id);
});

test('admin can impersonate a mentor and is redirected to mentor dashboard', function () {
    $admin = createImpersonationTestUser('admin');
    $mentor = createImpersonationTestUser('mentor');

    $response = $this->actingAs($admin)
        ->get(route('admin.impersonate', $mentor->id));

    $response->assertRedirect(route('mentor.dashboard'));
    $response->assertSessionHas('info');

    $manager = app(ImpersonateManager::class);
    expect($manager->isImpersonating())->toBeTrue();
    expect(Auth::id())->toBe($mentor->id);
    expect($manager->getImpersonatorId())->toBe($admin->id);
});

test('admin cannot impersonate another admin', function () {
    $admin1 = createImpersonationTestUser('admin');
    $admin2 = createImpersonationTestUser('admin');

    $response = $this->actingAs($admin1)
        ->get(route('admin.impersonate', $admin2->id));

    $response->assertStatus(403);
});

test('admin cannot impersonate themselves', function () {
    $admin = createImpersonationTestUser('admin');

    $response = $this->actingAs($admin)
        ->get(route('admin.impersonate', $admin->id));

    $response->assertStatus(403);
});

test('student cannot trigger impersonation', function () {
    $student1 = createImpersonationTestUser('student');
    $student2 = createImpersonationTestUser('student');

    $response = $this->actingAs($student1)
        ->get(route('admin.impersonate', $student2->id));

    $response->assertStatus(403);
});

test('admin can leave impersonation and restore admin session', function () {
    $admin = createImpersonationTestUser('admin');
    $student = createImpersonationTestUser('student');

    // Admin starts impersonating student
    $this->actingAs($admin)
        ->get(route('admin.impersonate', $student->id));

    expect(Auth::id())->toBe($student->id);

    // Leave impersonation
    $leaveResponse = $this->get(route('impersonate.leave'));

    $leaveResponse->assertRedirect(route('admin.users'));
    $leaveResponse->assertSessionHas('success');

    $manager = app(ImpersonateManager::class);
    expect($manager->isImpersonating())->toBeFalse();
    expect(Auth::id())->toBe($admin->id);
});

test('impersonation banner is visible on portal when impersonating a user', function () {
    $admin = createImpersonationTestUser('admin');
    $student = createImpersonationTestUser('student', ['name' => 'Jane Student Doe']);

    // Admin starts impersonating student
    $this->actingAs($admin)
        ->get(route('admin.impersonate', $student->id));

    $response = $this->get(route('student.dashboard'));

    $response->assertOk();
    $response->assertSee('Admin Impersonation Mode');
    $response->assertSee('Jane Student Doe');
    $response->assertSee(route('impersonate.leave'));
});

