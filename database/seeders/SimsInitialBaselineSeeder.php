<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\CourseOffering;
use App\Models\Student;

class SimsInitialBaselineSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            'SuperAdmin' => 'Super Administrator',
            'Registrar' => 'Registrar Officer',
            'Dean' => 'College Dean',
            'DepartmentHead' => 'Department Head',
            'Instructor' => 'Academic Lecturer',
            'Student' => 'Student',
        ];

        $roleModels = [];
        foreach ($roles as $name => $display) {
            $roleModels[$name] = Role::create([
                'name' => $name,
                'display_name' => $display,
                'description' => $display
            ]);
        }

        // 2. University Academic Structure
        $college = College::create(['code' => 'COT', 'name' => 'College of Engineering & Technology']);
        $dept = Department::create(['college_id' => $college->id, 'code' => 'CS', 'name' => 'Department of Computer Science']);
        $program = Program::create([
            'department_id' => $dept->id,
            'code' => 'BSC-CS',
            'name' => 'B.Sc. in Computer Science',
            'degree_level' => 'Undergraduate',
            'study_mode' => 'Regular',
            'duration_years' => 4
        ]);

        $year = AcademicYear::create(['code' => '2026/2027', 'start_date' => '2026-09-01', 'end_date' => '2027-06-30', 'is_current' => true]);
        $sem = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Semester 1',
            'start_date' => '2026-09-15',
            'end_date' => '2027-01-30',
            'registration_start' => '2026-09-15',
            'registration_deadline' => '2026-10-30',
            'add_drop_deadline' => '2026-11-10',
            'grade_submission_deadline' => '2027-02-10',
            'is_active' => true,
        ]);

        $course1 = Course::create(['department_id' => $dept->id, 'code' => 'CoSc2011', 'title' => 'Data Structures and Algorithms', 'credit_hours' => 4.0, 'ects' => 7.0]);
        $course2 = Course::create(['department_id' => $dept->id, 'code' => 'CoSc3022', 'title' => 'Database Systems', 'credit_hours' => 3.0, 'ects' => 5.0]);

        $curriculum = Curriculum::create(['program_id' => $program->id, 'version' => '2026-Harmonized', 'title' => 'CS Standard Curriculum', 'total_credits_required' => 142.0]);
        CurriculumCourse::create(['curriculum_id' => $curriculum->id, 'course_id' => $course1->id, 'year_level' => 2, 'semester_term' => 1]);
        CurriculumCourse::create(['curriculum_id' => $curriculum->id, 'course_id' => $course2->id, 'year_level' => 2, 'semester_term' => 1]);

        // Offerings
        $offering1 = CourseOffering::create(['course_id' => $course1->id, 'semester_id' => $sem->id, 'section' => 'Sec-A', 'capacity' => 50]);
        $offering2 = CourseOffering::create(['course_id' => $course2->id, 'semester_id' => $sem->id, 'section' => 'Sec-A', 'capacity' => 50]);

        // 3. Create Seed Users
        $password = Hash::make('DDU_P@ssword2026!');

        $users = [
            'admin@ddu.edu.et' => ['name' => 'Super Administrator', 'role' => 'SuperAdmin'],
            'registrar@ddu.edu.et' => ['name' => 'Main Registrar Officer', 'role' => 'Registrar'],
            'lecturer1@ddu.edu.et' => ['name' => 'Dr. Abebe Kebede', 'role' => 'Instructor'],
            'student1@ddu.edu.et' => ['name' => 'Chala Tolosa Geda', 'role' => 'Student'],
        ];

        foreach ($users as $email => $u) {
            $user = User::create([
                'name' => $u['name'],
                'email' => $email,
                'password' => $password,
            ]);
            $user->roles()->attach($roleModels[$u['role']]->id);

            if ($u['role'] === 'Instructor') {
                $offering1->instructors()->attach($user->id);
            }

            if ($u['role'] === 'Student') {
                Student::create([
                    'user_id' => $user->id,
                    'program_id' => $program->id,
                    'curriculum_id' => $curriculum->id,
                    'student_id_number' => 'UGR/00100/18',
                    'first_name' => 'Chala',
                    'middle_name' => 'Tolosa',
                    'last_name' => 'Geda',
                    'gender' => 'Male',
                    'date_of_birth' => '2004-05-12',
                    'cohort_year' => 2026,
                    'study_mode' => 'Regular',
                    'status' => 'Active',
                ]);
            }
        }
    }
}