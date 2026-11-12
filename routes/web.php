<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\RegistrationController;
use App\Http\Controllers\Instructor\GradeEntryController;
use App\Http\Controllers\Registrar\StudentRecordsController;
use App\Http\Controllers\Academic\GradeWorkflowController;
use App\Http\Controllers\Admission\PublicApplicationController;
use App\Http\Controllers\Admission\AdmissionOfficerController;
use App\Http\Controllers\Admin\StructureController;
use App\Http\Controllers\Academic\CourseCatalogController;
use App\Http\Controllers\Instructor\AttendanceController;
use App\Http\Controllers\Student\ServiceRequestController;
use App\Http\Controllers\Registrar\GraduationClearanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Academic\GradeChangeController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Academic\TimetableController;
use App\Http\Controllers\Academic\CurriculumController;
use App\Http\Controllers\Student\AddDropController;
use App\Http\Controllers\Academic\AddDropApprovalController;
use App\Http\Controllers\Registrar\StudentLifecycleController;
use App\Http\Controllers\Academic\AdvisorController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Registrar\StudentImportController;
use App\Http\Controllers\PublicVerificationController;
use App\Http\Controllers\DocumentationController;


Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public Transcript Verification Service (No login required)
Route::prefix('verify/transcript')->name('public.verify.')->group(function () {
    Route::get('/', [PublicVerificationController::class, 'index'])->name('index');
    Route::post('/', [PublicVerificationController::class, 'lookup'])->name('lookup');
    Route::get('/{code}', [PublicVerificationController::class, 'show'])->name('show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Student
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/registration', [RegistrationController::class, 'index'])->name('registration');
        Route::post('/registration', [RegistrationController::class, 'store'])->name('registration.store');
        Route::get('/grades', [RegistrationController::class, 'viewGrades'])->name('grades');
    });

    // Instructor
    Route::prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/offerings/{offering}', [GradeEntryController::class, 'showOffering'])->name('offerings.show');
        Route::post('/offerings/{offering}/marks', [GradeEntryController::class, 'updateMarks'])->name('marks.update');
    });

    // Registrar
    Route::prefix('registrar')->name('registrar.')->group(function () {
        Route::get('/students', [StudentRecordsController::class, 'index'])->name('students.index');
    });
    // Transcript View & Print
    Route::get('/student/transcript', [RegistrationController::class, 'printTranscript'])->name('student.transcript');

// Grade Approval Workflow
    Route::prefix('academic/grades')->name('academic.grades.')->group(function () {
        Route::get('/department', [GradeWorkflowController::class, 'departmentIndex'])->name('department');
        Route::get('/registrar', [GradeWorkflowController::class, 'registrarIndex'])->name('registrar');
        
        // 1. POST route for submitting marks
        Route::post('/{offering}/submit', [GradeWorkflowController::class, 'submit'])->name('submit');
        
        // 2. Fallback GET route: redirects back to the course page if refreshed or visited directly
        Route::get('/{offering}/submit', function ($offering) {
            return redirect()->route('instructor.offerings.show', $offering);
        });

        Route::post('/{sheet}/endorse', [GradeWorkflowController::class, 'endorse'])->name('endorse');
        Route::post('/{sheet}/publish', [GradeWorkflowController::class, 'publish'])->name('publish');
    });
    // Public Admission Application Routes (No login required)
    Route::prefix('apply')->name('admissions.')->group(function () {
        Route::get('/', [PublicApplicationController::class, 'create'])->name('apply');
        Route::post('/', [PublicApplicationController::class, 'store'])->name('store');
        Route::get('/submitted/{application}', [PublicApplicationController::class, 'submitted'])->name('submitted');
    });

// Protected Admission Officer / Registrar Routes
    Route::middleware(['auth'])->prefix('admissions')->name('admissions.')->group(function () {
        Route::get('/queue', [AdmissionOfficerController::class, 'index'])->name('queue');
        Route::get('/application/{application}', [AdmissionOfficerController::class, 'show'])->name('show');
        Route::post('/application/{application}/review', [AdmissionOfficerController::class, 'review'])->name('review');
        Route::post('/application/{application}/enroll', [AdmissionOfficerController::class, 'enroll'])->name('enroll');
    });

    // Administrative University Structure (Colleges, Departments, Programs)
    Route::prefix('admin/structure')->name('admin.structure.')->group(function () {
        Route::get('/', [StructureController::class, 'index'])->name('index');
        Route::post('/college', [StructureController::class, 'storeCollege'])->name('college.store');
        Route::post('/department', [StructureController::class, 'storeDepartment'])->name('department.store');
        Route::post('/program', [StructureController::class, 'storeProgram'])->name('program.store');
    });

    // Course Catalog & Semester Offerings
    Route::prefix('academic/catalog')->name('academic.catalog.')->group(function () {
        Route::get('/courses', [CourseCatalogController::class, 'indexCourses'])->name('courses');
        Route::post('/courses', [CourseCatalogController::class, 'storeCourse'])->name('courses.store');
        
        Route::get('/offerings', [CourseCatalogController::class, 'indexOfferings'])->name('offerings');
        Route::post('/offerings', [CourseCatalogController::class, 'storeOffering'])->name('offerings.store');
    });
    
    // Instructor Attendance Management
    Route::prefix('instructor/offerings/{offering}/attendance')->name('instructor.attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/create', [AttendanceController::class, 'create'])->name('create');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
    });

    // Student Self-Service Hub (Documents & Graduation Clearance)
    Route::prefix('student/services')->name('student.services.')->group(function () {
        Route::get('/', [ServiceRequestController::class, 'index'])->name('index');
        Route::post('/documents', [ServiceRequestController::class, 'storeDocumentRequest'])->name('documents.store');
        Route::post('/graduation', [ServiceRequestController::class, 'applyGraduation'])->name('graduation.apply');
    });

    // Registrar Document Queue & Clearance Management
    Route::prefix('registrar')->name('registrar.')->group(function () {
        Route::get('/documents', [GraduationClearanceController::class, 'documentsIndex'])->name('documents.index');
        Route::post('/documents/{doc}/status', [GraduationClearanceController::class, 'updateDocumentStatus'])->name('documents.update');

        Route::get('/clearance', [GraduationClearanceController::class, 'clearanceIndex'])->name('clearance.index');
        Route::post('/clearance/{clearance}/sign-off', [GraduationClearanceController::class, 'signOff'])->name('clearance.signoff');
        Route::post('/clearance/{clearance}/graduate', [GraduationClearanceController::class, 'graduate'])->name('clearance.graduate');
    });

    // Executive Institutional Analytics & Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    // Academic Calendar & Semester Setup
    Route::prefix('admin/calendar')->name('admin.calendar.')->group(function () {
        Route::get('/', [CalendarController::class, 'index'])->name('index');
        Route::post('/year', [CalendarController::class, 'storeYear'])->name('year.store');
        Route::post('/semester', [CalendarController::class, 'storeSemester'])->name('semester.store');
        Route::post('/semester/{semester}/activate', [CalendarController::class, 'activateSemester'])->name('semester.activate');
    });

    // Audited Grade Change Requests
    Route::prefix('academic/grades/changes')->name('academic.grades.changes.')->group(function () {
        Route::get('/', [GradeChangeController::class, 'index'])->name('index');
        Route::post('/request/{item}', [GradeChangeController::class, 'store'])->name('store');
        Route::post('/{change}/endorse', [GradeChangeController::class, 'endorse'])->name('endorse');
        Route::post('/{change}/approve', [GradeChangeController::class, 'approve'])->name('approve');
        Route::post('/{change}/reject', [GradeChangeController::class, 'reject'])->name('reject');
    });

    // Announcements & Noticeboard
    Route::prefix('admin/announcements')->name('admin.announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
    });

    // Super Admin User Administration
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
    });

    // Security & Audit Log Viewer
    Route::get('/admin/audit-logs', [AuditController::class, 'index'])->name('admin.audit.index');

    // Timetables & Classroom Management
    Route::prefix('academic/timetables')->name('academic.timetables.')->group(function () {
        Route::get('/', [TimetableController::class, 'index'])->name('index');
        Route::post('/classroom', [TimetableController::class, 'storeClassroom'])->name('classroom.store');
        Route::post('/schedule', [TimetableController::class, 'storeSchedule'])->name('schedule.store');
    });

    // Personal Weekly Timetable (Students & Instructors)
    Route::get('/my-schedule', [TimetableController::class, 'mySchedule'])->name('schedule.my');

    // Curriculum & Prerequisite Management
    Route::prefix('academic/curricula')->name('academic.curricula.')->group(function () {
        Route::get('/', [CurriculumController::class, 'index'])->name('index');
        Route::post('/', [CurriculumController::class, 'store'])->name('store');
        Route::get('/{curriculum}', [CurriculumController::class, 'show'])->name('show');
        Route::post('/{curriculum}/attach', [CurriculumController::class, 'attachCourse'])->name('attach');
        Route::post('/prerequisites/set', [CurriculumController::class, 'setPrerequisite'])->name('prerequisite.set');
    });
// INSTRUCTOR PORTAL (Marks, Attendance & Assessment Components)
    Route::prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/offerings/{offering}', [GradeEntryController::class, 'showOffering'])->name('offerings.show');
        Route::post('/offerings/{offering}/marks', [GradeEntryController::class, 'updateMarks'])->name('marks.update');

        // ADD THESE TWO LINES:
        Route::post('/offerings/{offering}/components', [GradeEntryController::class, 'storeComponent'])->name('components.store');
        Route::post('/offerings/{offering}/components/reset', [GradeEntryController::class, 'resetComponents'])->name('components.reset');

        // Attendance Tracking Routes
        Route::prefix('offerings/{offering}/attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::get('/create', [AttendanceController::class, 'create'])->name('create');
            Route::post('/', [AttendanceController::class, 'store'])->name('store');
        });
    });

    // Student Add/Drop Portal
    Route::prefix('student/add-drop')->name('student.adddrop.')->group(function () {
        Route::get('/', [AddDropController::class, 'index'])->name('index');
        Route::post('/', [AddDropController::class, 'store'])->name('store');
    });

    // Department Head Add/Drop Approvals
    Route::prefix('academic/add-drop')->name('academic.adddrop.')->group(function () {
        Route::get('/', [AddDropApprovalController::class, 'index'])->name('index');
        Route::post('/{addDrop}/approve', [AddDropApprovalController::class, 'approve'])->name('approve');
        Route::post('/{addDrop}/reject', [AddDropApprovalController::class, 'reject'])->name('reject');
    });

    // Advisor Portal (For Lecturers / Advisors)
    Route::prefix('advisor')->name('advisor.')->group(function () {
        Route::get('/advisees', [AdvisorController::class, 'index'])->name('index');
        Route::get('/advisees/{student}', [AdvisorController::class, 'show'])->name('show');
        Route::post('/advisees/{student}/notes', [AdvisorController::class, 'storeNote'])->name('notes.store');
    });

    // Department Head / Registrar: Advisor Assignment Matrix
    Route::prefix('academic/advisors')->name('academic.advisors.')->group(function () {
        Route::get('/assign', [AdvisorController::class, 'assignIndex'])->name('assign');
        Route::post('/assign', [AdvisorController::class, 'assignAdvisor'])->name('assign.store');
    });

    // Student: View My Assigned Advisor & Advice
    Route::get('/student/my-advisor', [AdvisorController::class, 'studentAdvisorView'])->name('student.advisor');

    // Under Route::prefix('registrar')->name('registrar.')->group(function () { ...
    Route::get('/students/{student}/manage', [StudentLifecycleController::class, 'show'])->name('students.show');
    Route::post('/students/{student}/status', [StudentLifecycleController::class, 'updateStatus'])->name('students.status.update');
    Route::post('/students/{student}/transfer', [StudentLifecycleController::class, 'transferProgram'])->name('students.transfer');

    // Academic Advising Portal
    Route::prefix('academic/advising')->name('academic.advising.')->group(function () {
        Route::get('/', [AdvisorController::class, 'index'])->name('index');
        Route::get('/student/{student}', [AdvisorController::class, 'show'])->name('show');
        Route::post('/student/{student}/note', [AdvisorController::class, 'storeNote'])->name('note.store');
        Route::post('/assign', [AdvisorController::class, 'assignAdvisor'])->name('assign');
    });

    // Inside admin prefix:
    Route::get('/admin/backup/download', [BackupController::class, 'download'])->name('admin.backup.download');

// Database Backup Management (Super Administrator)
    Route::prefix('admin/backups')->name('admin.backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/', [BackupController::class, 'store'])->name('store');
        Route::get('/download/{filename?}', [BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
    });

    // Fallback direct URL alias: /admin/backup/download
    Route::get('/admin/backup/download', [BackupController::class, 'download'])->name('admin.backup.download');
    
    // Bulk Student CSV Import & Migration
    Route::prefix('registrar/import')->name('registrar.import.')->group(function () {
        Route::get('/', [StudentImportController::class, 'index'])->name('index');
        Route::get('/template', [StudentImportController::class, 'downloadTemplate'])->name('template');
        Route::post('/', [StudentImportController::class, 'store'])->name('store');
    });
// Mark notifications as read
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\AnnouncementController::class, 'markAllRead'])->name('notifications.markAllRead');
    
    // REGISTRAR STUDENT REGISTRY & LIFECYCLE
    Route::prefix('registrar')->name('registrar.')->group(function () {
        Route::get('/students', [StudentRecordsController::class, 'index'])->name('students.index');
        
        // ADD THESE THREE ROUTES:
        Route::get('/students/{student}/manage', [StudentLifecycleController::class, 'show'])->name('students.show');
        Route::post('/students/{student}/status', [StudentLifecycleController::class, 'updateStatus'])->name('students.status.update');
        Route::post('/students/{student}/transfer', [StudentLifecycleController::class, 'transferProgram'])->name('students.transfer');
    });

/// Print-Ready PDF Manuals & Guidelines
Route::get('/docs/user-manual', [DocumentationController::class, 'userManual'])->name('docs.user_manual');
Route::get('/docs/technical-manual', [DocumentationController::class, 'technicalManual'])->name('docs.technical_manual');
Route::get('/docs/guidelines', [DocumentationController::class, 'guidelines'])->name('docs.guidelines');
Route::get('/docs/guidlines', fn() => redirect()->route('docs.guidelines'));
});