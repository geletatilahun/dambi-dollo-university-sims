<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DDU SIMS - Technical Architecture & Data Dictionary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page { size: A4; margin: 18mm 15mm 18mm 15mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #212529; font-size: 12.5px; line-height: 1.5; }
        .page-break { page-break-before: always; }
        .cover-page { height: 90vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        h1, h2, h3, h4, h5 { color: #0b1f33; font-weight: 700; }
        h2 { border-bottom: 2px solid #0d6efd; padding-bottom: 6px; margin-top: 25px; margin-bottom: 15px; }
        h3 { margin-top: 18px; font-size: 15px; color: #163657; }
        table { font-size: 11.5px; }
        table th { background-color: #0b1f33 !important; color: #fff !important; }
        code { font-size: 11px; color: #d63384; }
        .no-print-bar { position: fixed; top: 15px; right: 20px; z-index: 9999; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body class="p-4">

    <!-- Print Button (Hidden on PDF) -->
    <div class="no-print no-print-bar">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm me-2">&larr; Return to Dashboard</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-printer-fill me-1"></i> Save / Export as PDF
        </button>
    </div>

    <!-- COVER PAGE -->
    <div class="cover-page text-center">
        <i class="bi bi-cpu-fill text-primary mb-3" style="font-size: 5rem;"></i>
        <h1 class="display-5 fw-bold mb-1">DAMBI DOLLO UNIVERSITY</h1>
        <h4 class="text-secondary fw-semibold mb-4">Directorate of Information & Communication Technology</h4>
        <hr style="width: 200px; height: 3px; background-color: #0d6efd; margin: 20px auto;">
        <h2 class="border-0 fw-bold fs-3 mt-3">SYSTEM ARCHITECTURE & TECHNICAL SPECIFICATION</h2>
        <h3 class="text-muted fw-normal fs-5">Complete Data Dictionary, Engine Algorithms & Operational Playbook</h3>
        <div class="mt-5 pt-5 text-muted small">
            <div><strong>Framework:</strong> Laravel 11 / PHP 8.2+ | <strong>Database:</strong> MySQL 8.0</div>
            <div><strong>Author:</strong> Opia Software Engineering Team</div>
            <div><strong>Release:</strong> Version 1.0 Production-Ready Baseline</div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ARCHITECTURE OVERVIEW -->
    <h2>1. System Architecture & Design Patterns</h2>
    <p>USIMS is structured as an enterprise-grade <strong>Modular Monolith</strong> utilizing domain-driven service encapsulation. Critical business workflows are separated from HTTP delivery mechanisms through dedicated service providers:</p>
    
    <ul>
        <li><strong>AcademicProgressionService:</strong> Calculates deterministic SGPA, cumulative CGPA, grade points, and academic standings (*Good Standing, Distinction, Warning, Dismissal*).</li>
        <li><strong>AdmissionService:</strong> Handles duplicate verification, applicant-to-student conversions, and automated sequential Ethiopian ID generation (<code>UGR/{Sequence:5}/{Year:2}</code>).</li>
        <li><strong>DatabaseBackupService:</strong> A pure PHP/PDO SQL dumper that queries <code>information_schema.tables</code> for <code>BASE TABLE</code> entries, generating standalone SQL dump archives without shell dependencies.</li>
    </ul>

    <!-- DATA DICTIONARY -->
    <h2>2. Complete Database Data Dictionary</h2>
    <p>The relational database schema consists of 22 normalized tables enforcing referential integrity:</p>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th width="20%">Table Name</th>
                <th width="35%">Key Columns & Data Types</th>
                <th width="45%">Constraints, Foreign Keys & Business Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>users</code></td>
                <td><code>id (PK)</code>, <code>name</code>, <code>email (UQ)</code>, <code>password</code></td>
                <td>System-wide authentication accounts with hashed credentials.</td>
            </tr>
            <tr>
                <td><code>roles</code> & <code>user_roles</code></td>
                <td><code>id (PK)</code>, <code>name (UQ)</code>, <code>display_name</code></td>
                <td>Role-based access control mapping users to primary functional scopes.</td>
            </tr>
            <tr>
                <td><code>colleges</code></td>
                <td><code>id (PK)</code>, <code>code (UQ)</code>, <code>name</code>, <code>is_active</code></td>
                <td>University academic faculties (e.g. College of Engineering & Technology).</td>
            </tr>
            <tr>
                <td><code>departments</code></td>
                <td><code>id (PK)</code>, <code>college_id (FK)</code>, <code>code</code>, <code>name</code></td>
                <td>Academic departments managing degree programs and instructors.</td>
            </tr>
            <tr>
                <td><code>programs</code></td>
                <td><code>id (PK)</code>, <code>department_id (FK)</code>, <code>code</code>, <code>degree_level</code></td>
                <td>Accredited degrees (Regular, Extension, Weekend, Summer).</td>
            </tr>
            <tr>
                <td><code>courses</code></td>
                <td><code>id (PK)</code>, <code>department_id (FK)</code>, <code>code (UQ)</code>, <code>credit_hours</code></td>
                <td>Master course catalog storing Credit Hours and ECTS values.</td>
            </tr>
            <tr>
                <td><code>course_prerequisites</code></td>
                <td><code>course_id (FK)</code>, <code>prerequisite_course_id (FK)</code></td>
                <td>Composite primary key defining prerequisite dependencies.</td>
            </tr>
            <tr>
                <td><code>curricula</code> & <code>curriculum_courses</code></td>
                <td><code>curriculum_id (FK)</code>, <code>course_id (FK)</code>, <code>year_level</code></td>
                <td>Maps courses to Year 1–5 and Semester 1–2 terms.</td>
            </tr>
            <tr>
                <td><code>students</code></td>
                <td><code>id (PK)</code>, <code>user_id (FK)</code>, <code>student_id_number (UQ)</code>, <code>status</code></td>
                <td>Master student profiles and lifecycle records.</td>
            </tr>
            <tr>
                <td><code>course_offerings</code></td>
                <td><code>id (PK)</code>, <code>course_id (FK)</code>, <code>semester_id (FK)</code>, <code>capacity</code></td>
                <td>Semester course sections (e.g. <code>Sec-A</code>) and seat capacities.</td>
            </tr>
            <tr>
                <td><code>timetables</code></td>
                <td><code>course_offering_id (FK)</code>, <code>classroom_id (FK)</code>, <code>start_time</code></td>
                <td>Lecture schedule slots with clash detection.</td>
            </tr>
            <tr>
                <td><code>registration_courses</code></td>
                <td><code>registration_id (FK)</code>, <code>final_score</code>, <code>grade_letter</code></td>
                <td>Enrolled line-item course marks and letter grade results.</td>
            </tr>
            <tr>
                <td><code>assessment_components</code></td>
                <td><code>course_offering_id (FK)</code>, <code>title</code>, <code>max_weight</code></td>
                <td>20% Continuous, 30% Midterm, 50% Final Exam breakdown.</td>
            </tr>
            <tr>
                <td><code>attendance_records</code></td>
                <td><code>session_id (FK)</code>, <code>student_id (FK)</code>, <code>status</code></td>
                <td>Tracks student presence (Present, Absent, Late, Excused).</td>
            </tr>
            <tr>
                <td><code>semester_results</code></td>
                <td><code>student_id (FK)</code>, <code>semester_id (FK)</code>, <code>sgpa</code>, <code>cgpa</code></td>
                <td>Cumulative GPA records and academic standing states.</td>
            </tr>
            <tr>
                <td><code>add_drop_requests</code></td>
                <td><code>student_id (FK)</code>, <code>offering_id (FK)</code>, <code>request_type</code></td>
                <td>Course addition/drop requests subject to departmental approval.</td>
            </tr>
            <tr>
                <td><code>graduation_clearances</code></td>
                <td><code>student_id (FK)</code>, <code>status_dept</code>, <code>status_library</code>, <code>status_finance</code></td>
                <td>4-office institutional sign-off grid before degree conferral.</td>
            </tr>
            <tr>
                <td><code>transcript_verifications</code></td>
                <td><code>student_id (FK)</code>, <code>verification_code (UQ)</code>, <code>issued_at</code></td>
                <td>Unique tokens linked to public verification QR codes.</td>
            </tr>
            <tr>
                <td><code>audit_logs</code></td>
                <td><code>user_id (FK)</code>, <code>action_event</code>, <code>description</code>, <code>ip_address</code></td>
                <td>Immutable audit trail of sensitive system mutations.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- BUSINESS LOGIC ENGINES -->
    <h2>3. Mathematical Formulations & Logic Engines</h2>

    <h3>3.1 GPA / CGPA Calculation Formula</h3>
    <p>Semester Grade Point Average (SGPA) is computed deterministically at semester close:</p>
    <div class="p-2 bg-light border rounded font-monospace small">
        SGPA = &sum;(Course_Credit_Hours &times; Earned_Grade_Points) / &sum;(Course_Credit_Hours)
    </div>
    <p class="mt-2"><strong>In-Progress Guard:</strong> If any registered course in a term has <code>grade_letter = NULL</code>, final SGPA calculation is withheld, displaying <em>Term In Progress</em> to prevent premature GPA claims.</p>

    <h3>3.2 Timetable Conflict Detection Algorithm</h3>
    <p>Before saving any scheduled slot $[S_{\text{new}}, E_{\text{new}}]$ on Day $D$, the system evaluates overlap against existing slots $[S_{\text{exist}}, E_{\text{exist}}]$ using the condition:</p>
    <div class="p-2 bg-light border rounded font-monospace small">
        Overlap = (S_exist &lt; E_new) AND (E_exist &gt; S_new)
    </div>
    <p class="mt-2">Checks are performed concurrently for classroom availability and instructor teaching availability.</p>

    <!-- PRODUCTION PLAYBOOK -->
    <h2>4. Production Deployment & Maintenance Playbook</h2>
    <p>Run these terminal commands to optimize the application for production deployment:</p>
    
    <div class="bg-light p-3 border rounded font-monospace small">
        # 1. Clear development caches<br>
        php artisan optimize:clear<br><br>
        # 2. Compile production caches<br>
        php artisan config:cache<br>
        php artisan route:cache<br>
        php artisan view:cache<br><br>
        # 3. Execute automated test suite<br>
        php artisan test --filter=SimsAcademicWorkflowTest
    </div>

    <h3 class="mt-4">Automated Nightly Backup Cron Job</h3>
    <p>To enable automated 02:00 AM database snapshots, add this entry to the server crontab (<code>crontab -e</code>):</p>
    <div class="bg-light p-2 border rounded font-monospace small">
        * * * * * cd /var/www/ddu-sims &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1
    </div>
</body>
</html>