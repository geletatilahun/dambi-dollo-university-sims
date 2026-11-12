<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseRegistration;
use App\Models\Program;
use App\Models\RegistrationCourse;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use App\Services\AcademicProgressionService;
use App\Services\AdmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SimsAcademicWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed initial roles & university baseline
        $this->seed(\Database\Seeders\SimsInitialBaselineSeeder::class);
    }

    /**
     * TEST 1: Grade Resolution and GPA Calculation Engine
     */
    public function test_grading_scale_and_gpa_resolution_accuracy(): void
    {
        $service = app(AcademicProgressionService::class);

        // Test letter grade & point mapping
        $scoreA = $service->resolveGrade(88.5);
        $this->assertEquals('A', $scoreA['grade']);
        $this->assertEquals(4.00, $scoreA['point']);

        $scoreB = $service->resolveGrade(76.0);
        $this->assertEquals('B+', $scoreB['grade']);
        $this->assertEquals(3.50, $scoreB['point']);

        $scoreF = $service->resolveGrade(35.0);
        $this->assertEquals('F', $scoreF['grade']);
        $this->assertEquals(0.00, $scoreF['point']);
    }

    /**
     * TEST 2: RBAC Security — Students cannot access Administrative Routes
     */
    public function test_student_cannot_access_administrative_endpoints(): void
    {
        $studentUser = User::where('email', 'student1@ddu.edu.et')->first();

        // Attempt to access user management, audit logs, or registrar clearance
        $response1 = $this->actingAs($studentUser)->get('/admin/users');
        $this->assertTrue(in_array($response1->status(), [302, 403]));

        $response2 = $this->actingAs($studentUser)->get('/admin/backups');
        $this->assertTrue(in_array($response2->status(), [302, 403]));
    }

    /**
     * TEST 3: Automated Ethiopian Student ID Generation & Account Enrollment
     */
    public function test_automated_student_id_generation_and_enrolment(): void
    {
        $admissionService = app(AdmissionService::class);
        $program = Program::first();

        // Submit application
        $application = $admissionService->submitApplication([
            'program_id' => $program->id,
            'first_name' => 'Almaz',
            'middle_name' => 'Ayana',
            'last_name' => 'Dibaba',
            'gender' => 'Female',
            'date_of_birth' => '2005-02-14',
            'email' => 'almaz.ayana@ddu.edu.et',
            'phone_number' => '+251911223344',
            'study_mode' => 'Regular',
            'entry_type' => 'Freshman',
        ]);

        $this->assertEquals('Submitted', $application->status);

        // Accept & Enroll
        $application->update(['status' => 'Accepted']);
        $student = $admissionService->convertApplicantToStudent($application);

        // Verify generated ID format: UGR/{sequence}/{year}
        $this->assertStringStartsWith('UGR/', $student->student_id_number);
        $this->assertEquals('Active', $student->status);
        $this->assertDatabaseHas('users', ['email' => 'almaz.ayana@ddu.edu.et']);
    }

    /**
     * TEST 4: Prerequisite Registration Blocker
     */
    public function test_course_registration_blocks_unmet_prerequisites(): void
    {
        $student = Student::first();
        $studentUser = $student->user;
        $semester = Semester::first();

        // Course A requires Course B as prerequisite
        $courseB = Course::first();
        $courseA = Course::create([
            'department_id' => $courseB->department_id,
            'code' => 'TEST301',
            'title' => 'Advanced Algorithms',
            'credit_hours' => 3.0,
            'ects' => 5.0,
        ]);
        $courseA->prerequisites()->attach($courseB->id);

        $offeringA = CourseOffering::create([
            'course_id' => $courseA->id,
            'semester_id' => $semester->id,
            'section' => 'Sec-A',
            'capacity' => 40,
        ]);

        // Student has NOT passed Course B, attempts to register for Offering A
        $response = $this->actingAs($studentUser)->post('/student/registration', [
            'offerings' => [$offeringA->id]
        ]);

        // Must redirect back with prerequisite error
        $response->assertSessionHasErrors('prerequisite');
    }
}