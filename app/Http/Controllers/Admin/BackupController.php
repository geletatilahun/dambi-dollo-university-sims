<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(
        protected DatabaseBackupService $backupService
    ) {}

    // View Backups Dashboard
    public function index()
    {
        $backups = $this->backupService->listBackups();
        return view('admin.backups.index', compact('backups'));
    }

    // Trigger Instant Snapshot Creation
    public function store()
    {
        try {
            $filename = $this->backupService->createBackup();
            AuditLog::record('DATABASE_BACKUP', "SuperAdmin generated database snapshot {$filename}");

            return back()->with('success', "Database backup created successfully: {$filename}");
        } catch (\Exception $e) {
            return back()->withErrors(['backup' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    // Download Backup SQL File (Auto-generates or grabs latest if no filename provided)
    public function download(?string $filename = null): BinaryFileResponse
    {
        if (!$filename) {
            $backups = $this->backupService->listBackups();
            if (empty($backups)) {
                $filename = $this->backupService->createBackup();
            } else {
                $filename = $backups[0]['filename'];
            }
        }

        $path = storage_path('app/backups/' . basename($filename));

        if (!file_exists($path)) {
            $filename = $this->backupService->createBackup();
            $path = storage_path('app/backups/' . basename($filename));
        }

        AuditLog::record('DATABASE_BACKUP_DOWNLOAD', "Downloaded database backup {$filename}");

        return response()->download($path);
    }

    // Delete Outdated Backup
    public function destroy(string $filename)
    {
        $path = storage_path('app/backups/' . basename($filename));

        if (file_exists($path)) {
            unlink($path);
            AuditLog::record('DATABASE_BACKUP_DELETED', "SuperAdmin deleted backup {$filename}");
            return back()->with('success', "Backup {$filename} deleted.");
        }

        return back()->withErrors(['backup' => 'File not found.']);
    }
}