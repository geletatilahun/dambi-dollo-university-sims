<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Curriculum;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AdmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentImportController extends Controller
{
    public function __construct(
        protected AdmissionService $admissionService
    ) {}

    // View Import Portal
    public function index()
    {
        $programs = Program::where('is_active', true)->get();
        return view('registrar.import', compact('programs'));
    }

    // Download Standard CSV Template
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ddu_student_import_template.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            
            // CSV Header Row
            fputcsv($handle, [
                'first_name',
                'middle_name',
                'last_name',
                'gender',
                'date_of_birth',
                'national_id',
                'email',
                'phone_number',
                'program_code',
                'study_mode',
            ]);

            // Sample Row 1
            fputcsv($handle, [
                'Tolosa',
                'Girma',
                'Desta',
                'Male',
                '2005-04-18',
                'ET-99887766',
                'tolosa.girma@ddu.edu.et',
                '+251911002233',
                'BSC-CS',
                'Regular',
            ]);

            // Sample Row 2
            fputcsv($handle, [
                'Ayantu',
                'Bekele',
                'Kumsa',
                'Female',
                '2005-09-12',
                'ET-44556677',
                'ayantu.bekele@ddu.edu.et',
                '+251922334455',
                'BSC-CS',
                'Regular',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    // Parse, Validate, and Ingest CSV Data
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // Max 5MB
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        $expectedHeaders = ['first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth', 'national_id', 'email', 'phone_number', 'program_code', 'study_mode'];

        // Verify Header Structure
        if (!$header || count(array_intersect($expectedHeaders, $header)) < 7) {
            fclose($handle);
            return back()->withErrors(['csv' => 'Invalid CSV format. Please download and use the official DDU template.']);
        }

        $programsMap = Program::pluck('id', 'code')->toArray();
        $studentRole = Role::firstOrCreate(['name' => 'Student'], ['display_name' => 'Student']);

        $importedCount = 0;
        $errors = [];
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = array_combine(array_slice($header, 0, count($row)), $row);

                $firstName = trim($data['first_name'] ?? '');
                $middleName = trim($data['middle_name'] ?? '');
                $lastName = trim($data['last_name'] ?? '');
                $gender = trim($data['gender'] ?? 'Male');
                $dob = trim($data['date_of_birth'] ?? '2005-01-01');
                $nationalId = trim($data['national_id'] ?? null);
                $email = trim($data['email'] ?? '');
                $phone = trim($data['phone_number'] ?? '');
                $programCode = trim($data['program_code'] ?? '');
                $studyMode = trim($data['study_mode'] ?? 'Regular');

                // Row Validations
                if (empty($firstName) || empty($lastName) || empty($email)) {
                    $errors[] = "Row {$rowNumber}: Missing required name or email.";
                    continue;
                }

                if (!in_array($gender, ['Male', 'Female'])) {
                    $errors[] = "Row {$rowNumber} ({$firstName}): Invalid gender '{$gender}'. Must be 'Male' or 'Female'.";
                    continue;
                }

                if (!isset($programsMap[$programCode])) {
                    $errors[] = "Row {$rowNumber} ({$firstName}): Program code '{$programCode}' does not exist in catalog.";
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $errors[] = "Row {$rowNumber} ({$firstName}): Email '{$email}' is already registered.";
                    continue;
                }

                $programId = $programsMap[$programCode];

                // Assign or Create Curriculum
                $curriculum = Curriculum::where('program_id', $programId)->first();
                if (!$curriculum) {
                    $curriculum = Curriculum::create([
                        'program_id' => $programId,
                        'version' => '2026-Harmonized',
                        'title' => 'Undergraduate Curriculum',
                        'total_credits_required' => 140.0,
                        'is_active' => true,
                    ]);
                }

                // 1. Create User
                $defaultPassword = 'DDU_' . rand(100000, 999999) . '!';
                $user = User::create([
                    'name' => "{$firstName} {$middleName} {$lastName}",
                    'email' => $email,
                    'password' => Hash::make($defaultPassword),
                ]);
                $user->roles()->attach($studentRole->id);

                // 2. Generate Unique Student ID Number
                $studentIdNumber = $this->admissionService->generateStudentIdNumber($studyMode);

                // 3. Create Student Record
                $studentData = [
                    'user_id' => $user->id,
                    'program_id' => $programId,
                    'curriculum_id' => $curriculum->id,
                    'student_id_number' => $studentIdNumber,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'gender' => $gender,
                    'date_of_birth' => $dob,
                    'cohort_year' => (int) date('Y'),
                    'study_mode' => in_array($studyMode, ['Regular', 'Extension', 'Weekend', 'Summer']) ? $studyMode : 'Regular',
                    'status' => 'Active',
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('students', 'national_id')) {
                    $studentData['national_id'] = $nationalId;
                }

                Student::create($studentData);
                $importedCount++;
            }

            DB::commit();
            fclose($handle);

            AuditLog::record('BULK_IMPORT', "Registrar batch-imported {$importedCount} students via CSV");

            $message = "Batch processing complete: {$importedCount} students successfully registered with official IDs.";
            return back()->with('success', $message)->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['csv' => 'Import encountered a database error: ' . $e->getMessage()]);
        }
    }
}