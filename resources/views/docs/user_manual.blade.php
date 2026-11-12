<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DDU SIMS - Official User Manual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page { size: A4; margin: 18mm 15mm 18mm 15mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #212529; font-size: 13px; line-height: 1.6; }
        .page-break { page-break-before: always; }
        .cover-page { height: 90vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        h1, h2, h3, h4, h5 { color: #0b1f33; font-weight: 700; }
        h2 { border-bottom: 2px solid #c59b27; padding-bottom: 6px; margin-top: 25px; margin-bottom: 15px; }
        h3 { margin-top: 20px; font-size: 16px; color: #163657; }
        table { font-size: 12px; }
        table th { background-color: #0b1f33 !important; color: #fff !important; }
        .no-print-bar { position: fixed; top: 15px; right: 20px; z-index: 9999; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            a { text-decoration: none; color: #000; }
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
        <i class="bi bi-mortarboard-fill text-warning mb-3" style="font-size: 5rem;"></i>
        <h1 class="display-5 fw-bold mb-1">DAMBI DOLLO UNIVERSITY</h1>
        <h4 class="text-secondary fw-semibold mb-4">Office of the Registrar & ICT Directorate</h4>
        <hr style="width: 200px; height: 3px; background-color: #c59b27; margin: 20px auto;">
        <h2 class="border-0 fw-bold fs-3 mt-3">STUDENT INFORMATION MANAGEMENT SYSTEM (USIMS)</h2>
        <h3 class="text-muted fw-normal fs-5">Official System User Manual & Operational Handbook</h3>
        <div class="mt-5 pt-5 text-muted small">
            <div><strong>Document Version:</strong> 1.0 (Production Release)</div>
            <div><strong>Target Year:</strong> 2026/2027 Academic Year</div>
            <div><strong>Deployment Context:</strong> Dambi Dollo University, Oromia, Ethiopia</div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- TABLE OF TEST ACCOUNTS -->
    <h2>1. System Access & Credential Matrix</h2>
    <p>The system is pre-configured with operational credentials across all primary roles for institutional evaluation and administrative training:</p>
    
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>Operational Role</th>
                <th>Institutional Email</th>
                <th>Initial Password</th>
                <th>Primary Scope & Access Boundary</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Super Administrator</strong></td>
                <td><code>admin@ddu.edu.et</code></td>
                <td><code>DDU_P@ssword2026!</code></td>
                <td>University-wide Settings, Users, Database Backups, Structure, Audit Logs</td>
            </tr>
            <tr>
                <td><strong>Main Registrar Officer</strong></td>
                <td><code>registrar@ddu.edu.et</code></td>
                <td><code>DDU_P@ssword2026!</code></td>
                <td>Admissions Queue, Registry, Grade Publication, Transcripts, Clearance</td>
            </tr>
            <tr>
                <td><strong>College Dean (Tech)</strong></td>
                <td><code>dean.tech@ddu.edu.et</code></td>
                <td><code>DDU_P@ssword2026!</code></td>
                <td>Supervision of College Departments, Grade Oversight, College Analytics</td>
            </tr>
            <tr>
                <td><strong>Department Head (CS)</strong></td>
                <td><code>head.cs@ddu.edu.et</code></td>
                <td><code>DDU_P@ssword2026!</code></td>
                <td>Course Offerings, Section Scheduling, Add/Drop Decisions, Advisor Assignment</td>
            </tr>
            <tr>
                <td><strong>Course Lecturer / Instructor</strong></td>
                <td><code>lecturer1@ddu.edu.et</code></td>
                <td><code>DDU_P@ssword2026!</code></td>
                <td>20/30/50 Continuous Assessment, Lecture Attendance, Grade Submission</td>
            </tr>
            <tr>
                <td><strong>Faculty Academic Advisor</strong></td>
                <td><code>lecturer1@ddu.edu.et</code></td>
                <td><code>DDU_P@ssword2026!</code></td>
                <td>Assigned Advisee Roster, Warning/Probation Monitoring, Intervention Logs</td>
            </tr>
            <tr>
                <td><strong>Senior Enrolled Student</strong></td>
                <td><code>student1@ddu.edu.et</code></td>
                <td><code>DDU_P@ssword2026!</code></td>
                <td>Course Registration, Add/Drop, Timetable, Grade Reports, Clearance</td>
            </tr>
        </tbody>
    </table>

    <!-- CHAPTER 2: STUDENT USER GUIDE -->
    <h2>2. Student Self-Service Portal Guide</h2>
    
    <h3>2.1 Registration & Course Selection</h3>
    <ul>
        <li>Navigate to <strong>Academic Portal &rarr; Course Registration</strong> during the active registration period.</li>
        <li>Select the available course offerings and click <strong>Submit Registration</strong>.</li>
        <li><strong>Prerequisite Enforcement:</strong> If an advanced course requires an unmet prerequisite, the registration engine will block the submission with an alert indicating the missing course.</li>
    </ul>

    <h3>2.2 Course Add & Drop Requests</h3>
    <ul>
        <li>Navigate to <strong>Academic Portal &rarr; Course Add / Drop</strong> before the semester's <code>add_drop_deadline</code>.</li>
        <li><strong>To Drop:</strong> Click <em>Drop Course</em> next to an enrolled course, provide an academic justification, and submit for Department Head review.</li>
        <li><strong>To Add:</strong> Select an available section from the dropdown, enter your reason, and submit. Registered credits update automatically upon departmental approval.</li>
    </ul>

    <h3>2.3 Weekly Timetable & Official Transcripts</h3>
    <ul>
        <li><strong>Weekly Timetable:</strong> Access your day-by-day (Monday–Friday) lecture grid showing start/end times and hall assignments.</li>
        <li><strong>Official Transcript:</strong> Open <em>Official Transcript</em>. If any course is pending grades, the document is automatically watermarked as <strong>UNOFFICIAL PROGRESS REPORT</strong>. Once all grades are published, it unlocks the <strong>OFFICIAL STUDENT ACADEMIC RECORD</strong> with a cryptographic QR code.</li>
    </ul>

    <div class="page-break"></div>

    <!-- CHAPTER 3: INSTRUCTOR GUIDE -->
    <h2>3. Course Instructor & Academic Advisor Guide</h2>

    <h3>3.1 Continuous Assessment Marks Entry (20/30/50 Breakdown)</h3>
    <ul>
        <li>Navigate to <strong>Teaching & Marks &rarr; My Mark Sheets</strong> and select <em>Enter Marks</em>.</li>
        <li>Input scores into the evaluation columns: <strong>Continuous Assessment (Max 20%)</strong>, <strong>Midterm Exam (Max 30%)</strong>, and <strong>Final Exam (Max 50%)</strong>.</li>
        <li>Click <strong>Save Marks & Calculate Grades</strong>. The system automatically computes the total score out of 100 and assigns the appropriate letter grade (<code>A+</code> through <code>F</code>).</li>
        <li>Click <strong>Submit to Department Head</strong> to lock marks and forward for official endorsement.</li>
    </ul>

    <h3>3.2 Classroom Attendance & Exam Eligibility Tracking</h3>
    <ul>
        <li>Under your course, click <strong>Attendance &rarr; Take Attendance for Today</strong>.</li>
        <li>Enter the date, lecture topic, and mark students as <em>Present</em>, <em>Absent</em>, <em>Late</em>, or <em>Excused</em>.</li>
        <li>The system continuously tracks attendance percentages. Any student falling below <strong>80%</strong> attendance is automatically flagged as ineligible to sit for the final examination.</li>
    </ul>

    <h3>3.3 Academic Advising & Counseling Interventions</h3>
    <ul>
        <li>Navigate to <strong>Teaching & Marks &rarr; My Advisees Roster</strong>.</li>
        <li>Inspect advisees' CGPAs. Students with CGPA &lt; 2.00 or on academic warning are highlighted with red <strong>At-Risk</strong> badges.</li>
        <li>Click <strong>Advise & Log &rarr; Record Advising Session</strong> to log counseling interventions, tutoring referrals, and action plans.</li>
    </ul>

    <!-- CHAPTER 4: REGISTRAR & ADMIN -->
    <h2>4. Registrar & Super Administrator Operations</h2>

    <h3>4.1 Batch Student CSV Ingestion</h3>
    <ul>
        <li>Navigate to <strong>Registrar Office &rarr; Batch Student Import</strong>.</li>
        <li>Download the official template, populate student details, and upload the CSV file.</li>
        <li>The engine automatically validates program codes, prevents duplicate emails, creates user accounts, and issues sequential IDs (<code>UGR/XXXXX/YY</code>).</li>
    </ul>

    <h3>4.2 3-Tier Grade Publication Pipeline</h3>
    <ol>
        <li><strong>Instructor:</strong> Records marks and submits grade sheet.</li>
        <li><strong>Department Head:</strong> Endorses the grade sheet at <em>Department Review &rarr; Endorsements</em>.</li>
        <li><strong>Registrar:</strong> Publishes results at <em>Registrar Office &rarr; Publish Grades</em>, locking the ledger and triggering automated SGPA/CGPA calculations.</li>
    </ol>

    <h3>4.3 Graduation Clearance & Degree Conferral</h3>
    <ul>
        <li>Navigate to <strong>Clearance & Records &rarr; Graduation Clearance</strong>.</li>
        <li>Review the 4-office sign-off matrix (<em>Department, Library, Student Affairs, Finance</em>).</li>
        <li>Once all four entities show <code>Cleared</code>, click <strong>Graduate</strong> to confer the degree.</li>
    </ul>

    <h3>4.4 One-Click Database Backups</h3>
    <ul>
        <li>Navigate to <strong>Administration &rarr; Database Backups</strong>.</li>
        <li>Click <strong>Generate New Backup</strong> to produce an instant, full SQL snapshot of all university data.</li>
    </ul>
</body>
</html>