<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Role;
use App\Models\Semester;
use App\Models\User;
use App\Services\AcademicProgressionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimsFullIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_grading_engine_calculates_correct_letter_and_points(): void
    {
        $service = new AcademicProgressionService();

        // 88.0 -> A (4.00)
        $gradeA = $service->resolveGrade(88.0);
        $this->assertEquals('A', $gradeA['grade']);
        $this->assertEquals(4.00, $gradeA['point']);

        // 72.0 -> B (3.00)
        $gradeB = $service->resolveGrade(72.0);
        $this->assertEquals('B', $gradeB['grade']);
        $this->assertEquals(3.00, $gradeB['point']);

        // 35.0 -> F (0.00)
        $gradeF = $service->resolveGrade(35.0);
        $this->assertEquals('F', $gradeF['grade']);
        $this->assertEquals(0.00, $gradeF['point']);
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}