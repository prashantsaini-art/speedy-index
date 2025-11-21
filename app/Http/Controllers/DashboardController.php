<?php

namespace App\Http\Controllers;

use App\Services\SpeedyIndexService;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $speedyIndexService;

    public function __construct(SpeedyIndexService $speedyIndexService)
    {
        $this->speedyIndexService = $speedyIndexService;
    }

    public function index()
    {
        // Initialize default values
        $balanceData = [
            'balance' => [
                'indexer' => 0,
                'checker' => 0
            ]
        ];
        
        $stats = [
            'total_tasks' => 0,
            'pending_tasks' => 0,
            'completed_tasks' => 0,
            'total_urls_indexed' => 0,
        ];
        
        $recentTasks = collect([]); // Empty collection

        try {
            $userId = Auth::id();

            // 1. Get user statistics from DB
            $stats = [
                'total_tasks' => Task::where('user_id', $userId)->count(),
                'pending_tasks' => Task::where('user_id', $userId)
                    ->where('status', 'pending')->count(),
                'completed_tasks' => Task::where('user_id', $userId)
                    ->where('status', 'completed')->count(),
                'total_urls_indexed' => Task::where('user_id', $userId)
                    ->sum('indexed_count'),
            ];

            // 2. Get recent tasks from DB
            $recentTasks = Task::where('user_id', $userId)
                ->latest()
                ->take(10)
                ->get();

            // 3. Fetch balance from API
            // We wrap this in its own try/catch so API failure doesn't break the whole dashboard
            try {
                $balanceData = $this->speedyIndexService->getBalance();
            } catch (\Exception $apiEx) {
                // If API fails, we just keep default balanceData and log error if needed
                // \Log::error('API Balance Fetch Failed: ' . $apiEx->getMessage());
            }

            return view('dashboard', compact('balanceData', 'stats', 'recentTasks'));

        } catch (\Exception $e) {
            // Fallback view if DB queries fail
            return view('dashboard', compact('balanceData', 'stats', 'recentTasks'))
                ->withErrors(['error' => 'Dashboard error: ' . $e->getMessage()]);
        }
    }
}
