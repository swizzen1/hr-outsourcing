<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublicVacanciesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-01-05 10:00:00'));
    }

    public function test_draft_vacancy_not_listed(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);

        $draft = Vacancy::create([
            'company_id' => $company->id,
            'title' => 'Draft Role',
            'description' => 'Draft description',
            'employment_type' => 'full_time',
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/v1/public/vacancies');

        $response->assertOk();
        $this->assertNotContains($draft->id, collect($response->json('data'))->pluck('id'));
    }

    public function test_future_published_at_not_listed(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);

        $future = Vacancy::create([
            'company_id' => $company->id,
            'title' => 'Future Role',
            'description' => 'Future description',
            'employment_type' => 'full_time',
            'status' => 'published',
            'published_at' => Carbon::now()->addDay(),
        ]);

        $response = $this->getJson('/api/v1/public/vacancies');

        $response->assertOk();
        $this->assertNotContains($future->id, collect($response->json('data'))->pluck('id'));
    }

    public function test_expired_vacancy_not_listed(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);

        $expired = Vacancy::create([
            'company_id' => $company->id,
            'title' => 'Expired Role',
            'description' => 'Expired description',
            'employment_type' => 'full_time',
            'status' => 'published',
            'published_at' => Carbon::now()->subDay(),
            'expiration_date' => Carbon::now()->subDay()->toDateString(),
        ]);

        $response = $this->getJson('/api/v1/public/vacancies');

        $response->assertOk();
        $this->assertNotContains($expired->id, collect($response->json('data'))->pluck('id'));
    }

    public function test_published_and_not_expired_is_listed(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);

        $visible = Vacancy::create([
            'company_id' => $company->id,
            'title' => 'Visible Role',
            'description' => 'Visible description',
            'employment_type' => 'full_time',
            'status' => 'published',
            'published_at' => Carbon::now()->subHour(),
            'expiration_date' => Carbon::now()->addDay()->toDateString(),
        ]);

        $response = $this->getJson('/api/v1/public/vacancies');

        $response->assertOk();
        $this->assertContains($visible->id, collect($response->json('data'))->pluck('id'));
    }

    public function test_detail_returns_404_for_non_visible(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);

        $draft = Vacancy::create([
            'company_id' => $company->id,
            'title' => 'Draft Role',
            'description' => 'Draft description',
            'employment_type' => 'full_time',
            'status' => 'draft',
        ]);

        $this->getJson("/api/v1/public/vacancies/{$draft->id}")
            ->assertNotFound();
    }

    public function test_pagination_meta_present(): void
    {
        $company = Company::create(['name' => 'Acme Corp', 'slug' => 'acme-corp']);

        for ($i = 1; $i <= 15; $i++) {
            Vacancy::create([
                'company_id' => $company->id,
                'title' => "Visible Role {$i}",
                'description' => 'Visible description',
                'employment_type' => 'full_time',
                'status' => 'published',
                'published_at' => Carbon::now()->subDay(),
            ]);
        }

        $response = $this->getJson('/api/v1/public/vacancies');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
        $this->assertCount(10, $response->json('data'));
        $this->assertSame(1, $response->json('meta.current_page'));
        $this->assertSame(10, $response->json('meta.per_page'));
        $this->assertSame(15, $response->json('meta.total'));
    }
}
