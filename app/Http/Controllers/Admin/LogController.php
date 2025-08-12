<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    /**
     * Display the logs page
     */
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        $error = null;
        
        // Check if log file exists
        if (!File::exists($logFile)) {
            $error = 'Log file not found.';
        } else {
            try {
                // Read the log file
                $content = File::get($logFile);
                
                // Split by lines and reverse to show newest first
                $lines = array_filter(explode("\n", $content));
                $lines = array_reverse($lines);
                
                // Parse log entries
                $logs = $this->parseLogEntries($lines);
                
                // Apply search filter if provided
                if ($request->filled('search')) {
                    $search = $request->search;
                    $logs = array_filter($logs, function($log) use ($search) {
                        return stripos($log['message'], $search) !== false || 
                               stripos($log['level'], $search) !== false ||
                               stripos($log['context'], $search) !== false;
                    });
                }
                
                // Apply level filter if provided
                if ($request->filled('level') && $request->level !== 'all') {
                    $logs = array_filter($logs, function($log) use ($request) {
                        return $log['level'] === $request->level;
                    });
                }
                
                // Paginate results
                $perPage = 100;
                $currentPage = $request->get('page', 1);
                $offset = ($currentPage - 1) * $perPage;
                $logs = array_slice($logs, $offset, $perPage);
                
            } catch (\Exception $e) {
                $error = 'Error reading log file: ' . $e->getMessage();
            }
        }
        
        return view('admin.logs.index', compact('logs', 'error'));
    }
    
    /**
     * Clear the log file
     */
    public function clear()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            
            if (File::exists($logFile)) {
                File::put($logFile, '');
                return redirect()->route('admin.logs.index')->with('success', 'Log file cleared successfully.');
            }
            
            return redirect()->route('admin.logs.index')->with('error', 'Log file not found.');
        } catch (\Exception $e) {
            return redirect()->route('admin.logs.index')->with('error', 'Error clearing log file: ' . $e->getMessage());
        }
    }
    
    /**
     * Download the log file
     */
    public function download()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            return redirect()->route('admin.logs.index')->with('error', 'Log file not found.');
        }
        
        return response()->download($logFile, 'laravel-' . date('Y-m-d-H-i-s') . '.log');
    }
    
    /**
     * Parse log entries from raw log lines
     */
    private function parseLogEntries($lines)
    {
        $logs = [];
        $currentEntry = '';
        
        foreach ($lines as $line) {
            // Check if this line starts a new log entry (starts with timestamp)
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {
                // Save previous entry if exists
                if ($currentEntry) {
                    $logs[] = $this->parseSingleLogEntry($currentEntry);
                }
                
                // Start new entry
                $currentEntry = $line;
            } else {
                // Continue current entry
                $currentEntry .= "\n" . $line;
            }
        }
        
        // Don't forget the last entry
        if ($currentEntry) {
            $logs[] = $this->parseSingleLogEntry($currentEntry);
        }
        
        return $logs;
    }
    
    /**
     * Parse a single log entry
     */
    private function parseSingleLogEntry($entry)
    {
        $lines = explode("\n", $entry);
        $firstLine = $lines[0];
        
        // Extract timestamp, level, and message
        if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $firstLine, $matches)) {
            $timestamp = $matches[1];
            $level = strtoupper($matches[2]);
            $context = $matches[3];
            $message = $matches[4];
            
            // Get the full message (including stack trace)
            $fullMessage = implode("\n", array_slice($lines, 1));
            
            return [
                'timestamp' => $timestamp,
                'level' => $level,
                'context' => $context,
                'message' => $message,
                'full_message' => $fullMessage,
                'raw' => $entry
            ];
        }
        
        // Fallback for malformed entries
        return [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'level' => 'UNKNOWN',
            'context' => 'unknown',
            'message' => $entry,
            'full_message' => $entry,
            'raw' => $entry
        ];
    }
} 