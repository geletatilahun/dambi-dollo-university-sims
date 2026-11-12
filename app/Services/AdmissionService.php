<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Curriculum;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdmissionService
{
    /**
     * Submit a new public application with duplicate checking.
     */
    public function submitApplication(array $data): Application
    {
        // 1. Check for duplicate National ID if provided
        if (!empty($data['national_id'])) {
            $existing = Application::where('national_id', $data['national_id'])
                ->orWhereHas('student', function ($q) use ($data) {
                    $q->where('national_id', $data['national_id']);
                })->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'national_id' => 'An active applicant or registered student with this National ID already exists.'
                ]);
            }
        }

        // 2. Generate Tracking Application Number: APP-{YEAR}-{SEQUENCE}
        $year = date('Y');
        $count = Application::whereYear('created_at', $year)->count() + 1;
        $applicationNumber = sprintf('APP-%s-%04d', $year, $count);

        return Application::create(array_merge($data, [
            'application_number' => $applicationNumber,
            'status' => 'Submitted',
        ]));
    }

    /**
     * Generate the official Ethiopian University Student ID format:
     * Format: UGR/{Sequential:5}/{EthiopianCohortYear:2}  (e.g., UGR/00101/19)
     */
    public function generateStudentIdNumber(string $studyMode): string
    {
        $prefix = match ($studyMode) {
            'Extension' => 'UGE',
            'Weekend'   => 'UGW',
            'Summer'    => 'UGS',
            default     => 'UGR',
        };

        // Ethiopian Academic Year Suffix (2026/2027 G.C. corresponds to 2019 E.C. -> '19')
        $yearSuffix = '19';

        $lastStudent = Student::where('student_id_number', 'LIKE', "{$prefix}/%/{$yearSuffix}")
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastStudent) {
            $parts = explode('/', $lastStudent->student_id_number);
            if (isset($parts[1])) {
                $nextSeq = ((int) $parts[1]) + 1;
            }
        }

        return sprintf('%s/%05d/%s', $prefix, $nextSeq, $yearSuffix);
    }

/**
     * Convert an Accepted Application into an Enrolled Student Record + User Account.
     */
    public function convertApplicantToStudent(Application $application): Student
    {
        if ($application->status !== 'Accepted') {
            throw new \Exception('Only accepted applications can be enrolled.');
        }

        return DB::transaction(function () use ($application) {
            // 1. Assign or fallback to Curriculum for the Program
            $curriculum = Curriculum::where('program_id', $application->program_id)->first();

            if (!$curriculum) {
                $curriculum = Curriculum::create([
                    'program_id' => $application->program_id,
                    'version' => '2026-Harmonized',
                    'title' => 'Undergraduate Curriculum',
                    'total_credits_required' => 142.0,
                    'is_active' => true,
                ]);
            }

            // 2. Generate Official Student ID Number
            $studentIdNumber = $this->generateStudentIdNumber($application->study_mode);

            // 3. Create Login User Account (or reuse if email exists)
            $defaultPassword = 'DDU_' . rand(100000, 999999) . '!';
            $user = User::firstOrCreate(
                ['email' => $application->email],
                [
                    'name' => "{$application->first_name} {$application->middle_name} {$application->last_name}",
                    'password' => Hash::make($defaultPassword),
                ]
            );

            // Assign Student Role
            $studentRole = Role::firstOrCreate(['name' => 'Student'], ['display_name' => 'Student']);
            if (!$user->roles()->where('name', 'Student')->exists()) {
                $user->roles()->attach($studentRole->id);
            }

            // 4. Safely prepare Student Profile Record (handles optional national_id column)
            $studentData = [
                'user_id' => $user->id,
                'program_id' => $application->program_id,
                'curriculum_id' => $curriculum->id,
                'student_id_number' => $studentIdNumber,
                'first_name' => $application->first_name,
                'middle_name' => $application->middle_name,
                'last_name' => $application->last_name,
                'gender' => $application->gender,
                'date_of_birth' => $application->date_of_birth,
                'cohort_year' => (int) date('Y'),
                'study_mode' => $application->study_mode,
                'status' => 'Active',
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'national_id')) {
                $studentData['national_id'] = $application->national_id;
            }

            $student = Student::create($studentData);

            // 5. Update Application Record to Enrolled
            $application->update([
                'status' => 'Enrolled',
                'student_id' => $student->id,
            ]);

            $student->initial_password = $defaultPassword;

            return $student;
        });
    }
}