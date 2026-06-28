<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

class NonFunctionalRequirementsTest extends TestCase
{
    public function test_password_policy_requires_minimum_length_letters_and_numbers(): void
    {
        $weakPassword = Validator::make([
            'password' => 'short',
            'password_confirmation' => 'short',
        ], [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $strongPassword = Validator::make([
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], [
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->assertTrue($weakPassword->fails());
        $this->assertFalse($strongPassword->fails(), implode(' ', $strongPassword->errors()->all()));
    }

    public function test_health_endpoint_reports_application_readiness(): void
    {
        config(['nonfunctional.reliability.health_database_connections' => ['default']]);

        $this->getJson(route('system.health'))
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.application.status', 'ok')
            ->assertJsonPath('checks.cache.status', 'ok');
    }

    public function test_web_pages_expose_performance_budget_headers(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertHeader('X-Performance-Budget-Ms', '2000');
    }
}
