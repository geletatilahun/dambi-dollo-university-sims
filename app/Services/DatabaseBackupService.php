<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseBackupService
{
    /**
     * Dumps all base tables and rows belonging to the active database into storage/app/backups.
     */
    public function createBackup(): string
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'ddu_sims_backup_' . date('Y_m_d_His') . '.sql';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();

        // 1. Query ONLY real BASE TABLES belonging strictly to this database
        $tables = DB::select("
            SELECT TABLE_NAME AS tbl_name 
            FROM information_schema.tables 
            WHERE TABLE_SCHEMA = ? 
              AND TABLE_TYPE = 'BASE TABLE'
        ", [$dbName]);

        $handle = fopen($filepath, 'w+');

        fwrite($handle, "-- --------------------------------------------------\n");
        fwrite($handle, "-- Dambi Dollo University (DDU) SIMS Database Backup\n");
        fwrite($handle, "-- Database: `{$dbName}`\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- --------------------------------------------------\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $t) {
            $table = $t->tbl_name;

            // Skip any tables with dots or invalid schema cross-references
            if (str_contains($table, '.')) {
                continue;
            }

            try {
                // Export Table DDL Schema
                $createStmt = DB::select("SHOW CREATE TABLE `{$table}`");
                $createTableSql = $createStmt[0]->{'Create Table'} ?? null;

                if ($createTableSql) {
                    fwrite($handle, "-- Structure for table `{$table}`\n");
                    fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                    fwrite($handle, $createTableSql . ";\n\n");
                }

                // Export Table Rows
                $rows = DB::table($table)->get();
                if ($rows->count() > 0) {
                    fwrite($handle, "-- Data for table `{$table}`\n");
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $escapedValues = array_map(function ($val) use ($pdo) {
                            return $val === null ? 'NULL' : $pdo->quote((string)$val);
                        }, array_values($rowArray));

                        $insertSql = sprintf(
                            "INSERT INTO `%s` VALUES (%s);\n",
                            $table,
                            implode(', ', $escapedValues)
                        );
                        fwrite($handle, $insertSql);
                    }
                    fwrite($handle, "\n");
                }
            } catch (\Throwable $e) {
                // If a specific table or view has permission issues, skip it and continue
                fwrite($handle, "-- Could not export `{$table}`: " . $e->getMessage() . "\n\n");
                continue;
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return $filename;
    }

    /**
     * List all existing backups with metadata.
     */
    public function listBackups(): array
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            return [];
        }

        $files = scandir($backupDir);
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $path = $backupDir . DIRECTORY_SEPARATOR . $file;
                $sizeBytes = filesize($path);
                
                $sizeFormatted = $sizeBytes > 1048576 
                    ? round($sizeBytes / 1048576, 2) . ' MB' 
                    : round($sizeBytes / 1024, 2) . ' KB';

                $backups[] = [
                    'filename' => $file,
                    'size' => $sizeFormatted,
                    'created_at' => date('Y-m-d H:i:s', filemtime($path)),
                ];
            }
        }

        // Sort latest first
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }
}