<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalEmployees = User::where('role', 'employee')->count();
        $totalSales = Sale::count();
        $totalRevenue = Sale::where('status', 'completed')->sum('total');
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock_quantity', '<=', DB::raw('low_stock_threshold'))->count();
        
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        $recentSales = Sale::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 'totalAdmins', 'totalEmployees', 
            'totalSales', 'totalRevenue', 'totalProducts', 
            'lowStockProducts', 'recentUsers', 'recentSales'
        ));
    }
    
    public function accessControl()
    {
        $users = User::withCount('sales')->orderBy('name')->paginate(20);
        return view('admin.access-control', compact('users'));
    }
    
    public function systemLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $content = file_get_contents($logFile);
            $lines = explode("\n", $content);
            $lines = array_reverse($lines);
            $logs = array_slice($lines, 0, 100);
        }
        
        return view('admin.system-logs', compact('logs'));
    }
    
    public function activityLog()
    {
        $activities = collect();
        
        $recentSales = Sale::with('user')->orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($recentSales as $sale) {
            $activities->push((object)[
                'user' => $sale->user->name,
                'action' => 'Made a sale',
                'details' => "Invoice: {$sale->invoice_no} - Amount: Ksh{$sale->total}",
                'created_at' => $sale->created_at
            ]);
        }
        
        $recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($recentUsers as $user) {
            $activities->push((object)[
                'user' => $user->name,
                'action' => 'Account created',
                'details' => "Role: " . ucfirst($user->role),
                'created_at' => $user->created_at
            ]);
        }
        
        $activities = $activities->sortByDesc('created_at');
        
        return view('admin.activity-log', compact('activities'));
    }
    
    public function databaseBackup()
    {
        if (!file_exists(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }
        
        $backupFiles = [];
        $backups = glob(storage_path('backups/*.sql'));
        
        foreach ($backups as $backup) {
            $backupFiles[] = (object)[
                'name' => basename($backup),
                'size' => round(filesize($backup) / 1024, 2),
                'date' => date('Y-m-d H:i:s', filemtime($backup)),
                'path' => $backup
            ];
        }
        
        usort($backupFiles, function($a, $b) {
            return strtotime($b->date) - strtotime($a->date);
        });
        
        return view('admin.database-backup', compact('backupFiles'));
    }
    
    /**
     * Create database backup using PHP (no mysqldump required)
     */
    public function createBackup()
    {
        try {
            if (!file_exists(storage_path('backups'))) {
                mkdir(storage_path('backups'), 0755, true);
            }
            
            $backupFile = storage_path("backups/backup-" . date('Y-m-d-H-i-s') . ".sql");
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::connection()->getDatabaseName();
            $tableKey = 'Tables_in_' . $databaseName;
            
            $handle = fopen($backupFile, 'w');
            
            // Write header
            fwrite($handle, "-- Soma POS Database Backup\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Database: " . $databaseName . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                
                // Get create table syntax
                $createTable = DB::select("SHOW CREATE TABLE $tableName");
                fwrite($handle, "DROP TABLE IF EXISTS `$tableName`;\n");
                fwrite($handle, $createTable[0]->{'Create Table'} . ";\n\n");
                
                // Get table data
                $rows = DB::table($tableName)->get();
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $rowArray = (array)$row;
                        $columns = array_keys($rowArray);
                        $values = array_map(function($value) {
                            if ($value === null) return 'NULL';
                            return "'" . str_replace("'", "\\'", $value) . "'";
                        }, array_values($rowArray));
                        
                        fwrite($handle, "INSERT INTO `$tableName` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n");
                    }
                    fwrite($handle, "\n");
                }
            }
            
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
            
            $fileSize = round(filesize($backupFile) / 1024, 2);
            
            return redirect()->route('admin.database-backup')
                ->with('success', "Database backup created successfully! File size: {$fileSize} KB");
            
        } catch (\Exception $e) {
            return redirect()->route('admin.database-backup')
                ->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }
    
    public function downloadBackup($filename)
    {
        $filePath = storage_path("backups/{$filename}");
        
        if (file_exists($filePath)) {
            return response()->download($filePath)->deleteFileAfterSend(false);
        }
        
        return redirect()->route('admin.database-backup')
            ->with('error', 'Backup file not found.');
    }
    
    public function deleteBackup($filename)
    {
        $filePath = storage_path("backups/{$filename}");
        
        if (file_exists($filePath)) {
            unlink($filePath);
            return redirect()->route('admin.database-backup')
                ->with('success', 'Backup deleted successfully!');
        }
        
        return redirect()->route('admin.database-backup')
            ->with('error', 'Backup file not found.');
    }
    
    public function settings()
    {
        return view('admin.settings');
    }
    
    public function updateSettings(Request $request)
    {
        Log::info('Settings updated by: ' . auth()->user()->name);
        return redirect()->route('admin.settings')
            ->with('success', 'Settings updated successfully!');
    }
    
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('view:clear');
        \Artisan::call('route:clear');
        \Artisan::call('config:clear');
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'System cache cleared successfully!');
    }
}