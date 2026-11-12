<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DDU SIMS - Academic Policy & Operational Guidelines (SOP)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page { size: A4; margin: 18mm 15mm 18mm 15mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #212529; font-size: 13px; line-height: 1.6; }
        .page-break { page-break-before: always; }
        .cover-page { height: 90vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        h1, h2, h3, h4, h5 { color: #0b1f33; font-weight: 700; }
        h2 { border-bottom: 2px solid #198754; padding-bottom: 6px; margin-top: 25px; margin-bottom: 15px; }
        h3 { margin-top: 18px; font-size: 15px; color: #163657; }
        table { font-size: 12px; }
        table th { background-color: #0b1f33 !important; color: #fff !important; }
        .sop-box { border-left: 4px solid #198754; background-color: #f8f9fa; padding: 12px 16px; margin-bottom: 12px; border-radius: 4px; }
        .no-print-bar { position: fixed; top: 15px; right: 20px; z-index: 9999; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            a { text-decoration: none; color: #000; }
        }
    </style>
</head>
<body class="p-4">

    <!-- Print / Export Bar -->
    <div class="no-print no-print-bar">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm me-2">&larr; Return to Dashboard</a>
        <button onclick="window.print()" class="btn btn-success btn-sm fw-bold">
            <i class="bi bi-printer-fill me-1"></i> Save / Export Guidelines as PDF
        </button>
    </div>

    <!-- COVER PAGE -->
    <div class="cover-page text-center">
        <i class="bi bi-shield-check text-success mb-3" style="font-size: 5rem;"></i>
        <h1 class="display-5 fw-bold mb-1">DAMBI DOLLO UNIVERSITY</h1>
        <h4 class="text-secondary fw-semibold mb-4">Office of the Academic Vice President & University Registrar</h4>
        <hr style="width: 220px; height: 3px; background-color: #198754; margin: 20px auto;">
        <h2 class="border-0 fw-bold fs-3 mt-3 text-dark">ACADEMIC POLICY GUIDELINES & OPERATIONAL PROCEDURES (SOP)</h2>
        <h3 class="text-muted fw-normal fs-5">Standard Guidelines for Digital Academic Governance via USIMS</h3>
        <div class="mt-5 pt-5 text-muted small">
            <div><strong>Document ID:</strong> DDU-SOP-SIMS-2026</div>
            <div><strong>Effective Date:</strong> Academic Year 2026/2027</div>
            <div><strong>Approved By:</strong> DDU Senate & Academic Commission</div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- GUIDELINE 1: ADMISSIONS & STUDENT IDENTIFICATION -->
    <h2>Guideline 1: Admissions & Student ID Numbering Policy</h2>
    
    <div class="sop-box">
        <strong>Policy Statement:</strong> Every admitted candidate must be assigned an immutable, sequentially generated university student identification number following the national higher education format.
    </div>

    <h3>1.1 Student ID Composition</h3>
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th width="25%">Study Mode</th>
                <th width="20%">Prefix Code</th>
                <th width="30%">Format Syntax</th>
                <th width="25%">Example Allocated ID</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Regular Undergraduate</strong></td>
                <td><code>UGR</code></td>
                <td><code>UGR/{Sequence:5}/{Year:2}</code></td>
                <td><code>UGR/00101/19</code></td>
            </tr>
            <tr>
                <td><strong>Extension (Evening)</strong></td>
                <td><code>UGE</code></td>
                <td><code>UGE/{Sequence:5}/{Year:2}</code></td>
                <td><code>UGE/00045/19</code></td>
            </tr>
            <tr>
                <td><strong>Weekend Program</strong></td>
                <td><code>UGW</code></td>
                <td><code>UGW/{Sequence:5}/{Year:2}</code></td>
                <td><code>UGW/00023/19</code></td>
            </tr>
            <tr>
                <td><strong>Summer (Kiremt)</strong></td>
                <td><code>UGS</code></td>
                <td><code>UGS/{Sequence:5}/{Year:2}</code></td>
                <td><code>UGS/00012/19</code></td>
            </tr>
        </tbody>
    </table>

    <h3>1.2 Duplicate Prevention & Identity Safeguards</h3>
    <ul>
        <li><strong>National ID Verification:</strong> No applicant or student may hold multiple active records with the same National / Kebele Identification number.</li>
        <li><strong>Email Uniqueness:</strong> The system enforces unique email addresses across both applicant records and system login accounts.</li>
    </ul>

    <!-- GUIDELINE 2: COURSE REGISTRATION & PREREQUISITES -->
    <h2>Guideline 2: Course Registration & Prerequisite Enforcement</h2>

    <div class="sop-box">
        <strong>Policy Statement:</strong> Registration is restricted to authorized calendar windows. Students may not advance to higher-level courses without demonstrating competence in foundational prerequisite courses.
    </div>

    <h3>2.1 Credit Load Regulations</h3>
    <ul>
        <li><strong>Minimum Semester Load:</strong> <code>12.0 Credit Hours</code> (regular full-time undergraduate students).</li>
        <li><strong>Standard Maximum Load:</strong> <code>19.0 Credit Hours</code>.</li>
        <li><strong>Academic Overload Authorization:</strong> Maximum <code>22.0 Credit Hours</code>, granted only to graduating students or students with a Cumulative GPA (CGPA) $\ge 3.50$, subject to Department Head approval.</li>
    </ul>

    <h3>2.2 Automated Prerequisite Blocking</h3>
    <ul>
        <li>Before any course offering registration is saved, the engine queries the student's historical transcript ledger.</li>
        <li>If a course requires a prerequisite, the prerequisite course must have an earned letter grade other than <code>F</code>, <code>Fx</code>, or <code>NULL</code>.</li>
        <li>Attempting to register for an unfulfilled course triggers an automated system block.</li>
    </ul>

    <div class="page-break"></div>

    <!-- GUIDELINE 3: ATTENDANCE & EXAM ELIGIBILITY -->
    <h2>Guideline 3: Classroom Attendance & Final Exam Eligibility</h2>

    <div class="sop-box">
        <strong>Mandatory 80% Rule:</strong> Pursuant to Senate Academic Legislation, a student who has attended less than <strong>80%</strong> of the scheduled lecture and laboratory hours in any course is ineligible to sit for the final examination.
    </div>

    <h3>3.1 Attendance Recording & Audit Procedures</h3>
    <ul>
        <li>Instructors must record lecture attendance for each session via the SIMS attendance roll call interface.</li>
        <li>The system dynamically computes:
            $$\text{Attendance Rate (\%)} = \left( \frac{\text{Sessions Attended}}{\text{Total Sessions Conducted}} \right) \times 100$$
        </li>
        <li><strong>Automated Ineligibility Flag:</strong> When a student's cumulative attendance drops below 80%, the system flags their profile as <em>Low Attendance (&lt;80%)</em>, and the student's final examination score entry box is withheld on the mark sheet.</li>
    </ul>

    <!-- GUIDELINE 4: EVALUATION & 20/30/50 GRADING SCHEME -->
    <h2>Guideline 4: Continuous Assessment & Evaluation Scheme</h2>

    <div class="sop-box">
        <strong>Harmonized Evaluation Scheme:</strong> Courses are evaluated on a 100% cumulative scale divided into continuous assessment components and a summative final examination.
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>Evaluation Component</th>
                <th>Standard Weight</th>
                <th>Institutional Guideline</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Continuous Assessment</strong></td>
                <td><strong>20%</strong></td>
                <td>Quizzes, homework assignments, lab exercises, term papers (minimum 3 assessments).</td>
            </tr>
            <tr>
                <td><strong>Midterm Examination</strong></td>
                <td><strong>30%</strong></td>
                <td>Conducted halfway through the semester term.</td>
            </tr>
            <tr>
                <td><strong>Final Examination</strong></td>
                <td><strong>50%</strong></td>
                <td>Supervised final examination administered during the official examination window.</td>
            </tr>
        </tbody>
    </table>

    <h3>4.1 Standardized Grading Scale & Grade Points</h3>
    <table class="table table-bordered table-sm text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>Raw Score</th>
                <th>Letter Grade</th>
                <th>Grade Point</th>
                <th>Academic Description</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>90.0 – 100.0</td><td><strong>A+</strong></td><td>4.00</td><td>Excellent</td></tr>
            <tr><td>85.0 – 89.9</td><td><strong>A</strong></td><td>4.00</td><td>Excellent</td></tr>
            <tr><td>80.0 – 84.9</td><td><strong>A-</strong></td><td>3.75</td><td>Very Good</td></tr>
            <tr><td>75.0 – 79.9</td><td><strong>B+</strong></td><td>3.50</td><td>Very Good</td></tr>
            <tr><td>70.0 – 74.9</td><td><strong>B</strong></td><td>3.00</td><td>Good</td></tr>
            <tr><td>65.0 – 69.9</td><td><strong>B-</strong></td><td>2.75</td><td>Good</td></tr>
            <tr><td>60.0 – 64.9</td><td><strong>C+</strong></td><td>2.50</td><td>Satisfactory</td></tr>
            <tr><td>50.0 – 59.9</td><td><strong>C</strong></td><td>2.00</td><td>Satisfactory</td></tr>
            <tr><td>45.0 – 49.9</td><td><strong>C-</strong></td><td>1.75</td><td>Unsatisfactory</td></tr>
            <tr><td>40.0 – 44.9</td><td><strong>D</strong></td><td>1.00</td><td>Very Weak (Pass)</td></tr>
            <tr><td>&lt; 40.0</td><td><strong>F</strong></td><td>0.00</td><td>Fail</td></tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- GUIDELINE 5: GRADE PUBLICATION & AMENDMENT SOP -->
    <h2>Guideline 5: Grade Endorsement, Publication & Amendment SOP</h2>

    <div class="sop-box">
        <strong>Lockout Policy:</strong> Once grades are published by the Registrar, neither instructors nor department heads can directly alter marks. Corrections must follow the Audited Grade Change Request (GCR) workflow.
    </div>

    <h3>5.1 The 3-Tier Approval Workflow</h3>
    <ol>
        <li><strong>Tier 1 — Instructor Submission:</strong> Instructor saves continuous and final marks, then clicks <em>Submit to Department Head</em>. This permanently locks the mark sheet from instructor modification.</li>
        <li><strong>Tier 2 — Departmental Endorsement:</strong> The Department Head reviews grade distributions and signs off on the submission.</li>
        <li><strong>Tier 3 — Registrar Official Publication:</strong> The Registrar reviews the endorsed sheets and clicks <em>Publish Officially</em>. This releases marks to student portals and automatically updates cumulative GPAs.</li>
    </ol>

    <h3>5.2 Post-Publication Grade Change Protocol</h3>
    <ul>
        <li><strong>Step A:</strong> The instructor initiates a Grade Change Request via the SIMS portal, providing the original mark, proposed mark, and documented justification (e.g., calculation error, clerical omission).</li>
        <li><strong>Step B:</strong> The Department Head reviews and endorses the request.</li>
        <li><strong>Step C:</strong> The Registrar issues final approval. The system automatically updates the official score, modifies the grade point, and recalculates the student's SGPA and CGPA.</li>
    </ul>

    <!-- GUIDELINE 6: ACADEMIC STANDING & ADVISING -->
    <h2>Guideline 6: Academic Standing & Faculty Advising Procedures</h2>

    <div class="sop-box">
        <strong>Progression Rule:</strong> An undergraduate student must maintain a minimum Cumulative GPA (CGPA) of <strong>2.00</strong> to remain in Good Standing.
    </div>

    <h3>6.1 Standing Decision Rules</h3>
    <ul>
        <li><strong>Distinction:</strong> $\text{CGPA} \ge 3.50$ with no failed courses.</li>
        <li><strong>Very Great Distinction:</strong> $\text{CGPA} \ge 3.75$ with no failed courses.</li>
        <li><strong>Good Standing:</strong> $\text{SGPA} \ge 2.00$ and $\text{CGPA} \ge 2.00$.</li>
        <li><strong>Academic Warning:</strong> $\text{SGPA} &lt; 1.75$ or $\text{CGPA} &lt; 2.00$ in any given semester.</li>
        <li><strong>Academic Dismissal:</strong> Two consecutive academic warnings, or a single semester with $\text{SGPA} &lt; 1.00$.</li>
    </ul>

    <h3>6.2 Mandatory Advising Interventions</h3>
    <ul>
        <li>Students placed on Academic Warning are flagged with a red <strong>At-Risk</strong> badge on the Advisor Portal.</li>
        <li>The assigned Academic Advisor must conduct an advising session within two weeks of grade publication and log documented counseling notes in the student's SIMS dossier.</li>
    </ul>

    <div class="page-break"></div>

    <!-- GUIDELINE 7: GRADUATION CLEARANCE -->
    <h2>Guideline 7: 4-Point Institutional Graduation Clearance SOP</h2>

    <div class="sop-box">
        <strong>Clearance Protocol:</strong> No degree certificate or final transcript may be conferred without unanimous digital sign-off from all four clearance offices.
    </div>

    <h3>7.1 The 4-Office Clearance Grid</h3>
    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th width="30%">Clearance Authority</th>
                <th width="35%">Verification Criteria</th>
                <th width="35%">Clearance Officer</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Academic Department</strong></td>
                <td>Completion of all curricular credits, fulfillment of major/elective courses, and submission of final project/internship.</td>
                <td>Department Head</td>
            </tr>
            <tr>
                <td><strong>2. University Library</strong></td>
                <td>Return of all borrowed books, reference materials, and clearance of library fines.</td>
                <td>Head University Librarian</td>
            </tr>
            <tr>
                <td><strong>3. Student Affairs &amp; Services</strong></td>
                <td>Dormitory room checkout, key return, sports equipment return, and cafeteria clearance.</td>
                <td>Dean of Student Affairs</td>
            </tr>
            <tr>
                <td><strong>4. Finance &amp; Cost-Sharing</strong></td>
                <td>Settlement of institutional dues, cost-sharing obligation forms, and tuition fees (for non-regular streams).</td>
                <td>Director of Finance</td>
            </tr>
        </tbody>
    </table>

    <h3>7.2 Final Degree Conferral</h3>
    <ul>
        <li>Once all four entities mark their status as <code>Cleared</code>, the Registrar's final <strong>Graduate</strong> action unlocks.</li>
        <li>The student status is updated to <code>Graduated</code>, academic history is archived, and the candidate is added to the official University Graduation Roll.</li>
    </ul>

    <!-- GUIDELINE 8: DIGITAL VERIFICATION -->
    <h2>Guideline 8: Digital Transcript Verification & Anti-Fraud Security</h2>

    <div class="sop-box">
        <strong>Verification Guarantee:</strong> Every official transcript issued by Dambi Dollo University carries a cryptographically verifiable Reference ID and dynamic QR code.
    </div>

    <h3>8.1 Verification Instructions for Third Parties</h3>
    <ul>
        <li>Scan the QR code printed on the bottom right corner of the transcript with a smartphone camera, or navigate to:  
            <strong><code>http://sims.ddu.edu.et/verify/transcript</code></strong>
        </li>
        <li>Input the unique document reference code (e.g. <code>DDU-VER-5573F0D4</code>).</li>
        <li>The system cross-references the code with the official university ledger and displays the authenticated student name, verified degree, and genuine CGPA with an official green certification seal.</li>
    </ul>
</body>
</html>