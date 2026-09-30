<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Institutions\app\Models\University;
use Modules\Institutions\app\Models\UniversityProgram;
use Modules\Institutions\app\Models\FeaturedInstitution;
use Modules\Institutions\app\Services\InstitutionService;
use Modules\Discovery\app\Services\TopInstitutionService;

uses(TestCase::class, RefreshDatabase::class);

it('includes only admin-created institutions in top institution service for dashboards', function () {
    $adminUni = University::create([
        'name' => 'Admin Managed University',
        'display_name' => 'Admin Uni',
        'country' => 'US',
        'is_active' => true,
        'is_admin_created' => true,
    ]);

    $importedUni = University::create([
        'name' => 'Imported External University',
        'display_name' => 'Imported Uni',
        'country' => 'US',
        'is_active' => true,
        'is_admin_created' => false,
    ]);

    $topService = new TopInstitutionService();
    $results = $topService->forDashboard();

    $ids = $results->pluck('id')->all();

    expect($ids)->toContain($adminUni->id);
    expect($ids)->not->toContain($importedUni->id);
});

it('includes only admin-created institutions in institution service browse data and list', function () {
    $adminUni = University::create([
        'name' => 'Admin Managed University 2',
        'display_name' => 'Admin Uni 2',
        'country' => 'US',
        'is_active' => true,
        'is_admin_created' => true,
    ]);

    $importedUni = University::create([
        'name' => 'Imported External University 2',
        'display_name' => 'Imported Uni 2',
        'country' => 'US',
        'is_active' => true,
        'is_admin_created' => false,
    ]);

    $service = new InstitutionService();

    $listResults = $service->list([]);
    $listIds = collect($listResults->items())->pluck('id')->all();

    expect($listIds)->toContain($adminUni->id);
    expect($listIds)->not->toContain($importedUni->id);

    $browseResults = $service->browseData();
    $browseIds = $browseResults->pluck('id')->all();

    expect($browseIds)->toContain($adminUni->id);
    expect($browseIds)->not->toContain($importedUni->id);
});

it('includes only admin-created institutions on landing page featured institutions and programs', function () {
    $adminUni = University::create([
        'name' => 'Admin Managed University 3',
        'display_name' => 'Admin Uni 3',
        'country' => 'US',
        'logo_url' => 'university_logo/admin.png',
        'is_active' => true,
        'is_admin_created' => true,
    ]);

    $importedUni = University::create([
        'name' => 'Imported External University 3',
        'display_name' => 'Imported Uni 3',
        'country' => 'US',
        'logo_url' => 'university_logo/imported.png',
        'is_active' => true,
        'is_admin_created' => false,
    ]);

    FeaturedInstitution::create([
        'university_id' => $adminUni->id,
        'sort_order' => 1,
        'source' => FeaturedInstitution::SOURCE_MANUAL,
    ]);

    FeaturedInstitution::create([
        'university_id' => $importedUni->id,
        'sort_order' => 2,
        'source' => FeaturedInstitution::SOURCE_MANUAL,
    ]);

    UniversityProgram::create([
        'university_id' => $adminUni->id,
        'program_name' => 'MBA',
        'program_type' => 'mba',
        'tier' => 'elite',
        'is_active' => true,
    ]);

    UniversityProgram::create([
        'university_id' => $importedUni->id,
        'program_name' => 'MBA External',
        'program_type' => 'mba',
        'tier' => 'elite',
        'is_active' => true,
    ]);

    $landingPrograms = UniversityProgram::getLandingPagePrograms();
    $mbaPrograms = $landingPrograms->get('mba');

    $universityIdsInMba = $mbaPrograms ? $mbaPrograms->pluck('university_id')->all() : [];

    expect($universityIdsInMba)->toContain($adminUni->id);
    expect($universityIdsInMba)->not->toContain($importedUni->id);
});

it('ensures student and mentor explore programs pages show only admin-created institutions and programs', function () {
    $student = \App\Models\User::factory()->create(['role' => 'student']);
    $mentorUser = \App\Models\User::factory()->create(['role' => 'mentor']);

    $adminUni = University::create([
        'name' => 'Admin Only University',
        'display_name' => 'Admin Only Uni',
        'country' => 'US',
        'is_active' => true,
        'is_admin_created' => true,
    ]);

    $importedUni = University::create([
        'name' => 'Non Admin External University',
        'display_name' => 'Non Admin Uni',
        'country' => 'US',
        'is_active' => true,
        'is_admin_created' => false,
    ]);

    $this->actingAs($student)
        ->get(route('student.institutions.index'))
        ->assertOk()
        ->assertSee('Admin Only University')
        ->assertDontSee('Non Admin External University');

    $this->actingAs($mentorUser)
        ->get(route('mentor.institutions.index'))
        ->assertOk()
        ->assertSee('Admin Only University')
        ->assertDontSee('Non Admin External University');
});
