<?php

namespace App\Http\Controllers;

use App\Services\SpeedyIndexService;
use App\Models\Task;
use App\Models\TaskUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests; // Ensure authorization methods work

    protected $speedyIndexService;

    public function __construct(SpeedyIndexService $speedyIndexService)
    {
        $this->speedyIndexService = $speedyIndexService;
    }

    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())
            ->with('urls')
            ->latest()
            ->paginate(20);
            
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'search_engine' => 'required|in:google,yandex',
            'task_type' => 'required|in:indexer,checker',
            'urls' => 'required|string',
            'vip' => 'nullable|boolean',
        ]);

        // Parse URLs (one per line)
        $urls = array_filter(
            array_map('trim', explode("\n", $validated['urls'])),
            fn($url) => !empty($url)
        );

        if (count($urls) > 10000) {
            // Fixed unclosed string and missing closing parenthesis/semicolon
            return back()->withErrors(['urls' => 'Maximum 10,000 URLs allowed per task.']);
        }

        try {
            DB::beginTransaction();

            // Create task via API
            if ($validated['vip'] ?? false) {
                $apiResponse = $this->speedyIndexService->createVipTask(
                    $urls,
                    $validated['title'] ?? null
                );
            } else {
                $apiResponse = $this->speedyIndexService->createTask(
                    $validated['search_engine'],
                    $validated['task_type'],
                    $urls,
                    $validated['title'] ?? null
                );
            }

            // Store task in database
            $task = Task::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'] ?? 'Untitled Task',
                'search_engine' => $validated['search_engine'],
                'task_type' => $validated['task_type'],
                'external_task_id' => $apiResponse['id'] ?? null,
                'status' => 'pending',
                'total_urls' => count($urls),
                'metadata' => $apiResponse,
            ]);

            // Store URLs efficiently
            $taskUrlsData = [];
            foreach ($urls as $url) {
                $taskUrlsData[] = [
                    'task_id' => $task->id,
                    'url' => $url,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            // Bulk insert for better performance
            TaskUrl::insert($taskUrlsData);

            DB::commit();

            return redirect()->route('tasks.show', $task)
                ->with('success', 'Task created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Fixed unclosed string and missing closing parenthesis/semicolon
            return back()->withErrors(['error' => 'Failed to create task: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        $task->load('urls');

        // Fetch latest status from API if external_task_id exists
        if ($task->external_task_id) {
            try {
                $statusData = $this->speedyIndexService->getTaskStatus(
                    $task->search_engine,
                    $task->task_type,
                    [$task->external_task_id]
                );

                // Update local task status
                $this->updateTaskFromApi($task, $statusData);
            } catch (\Exception $e) {
                // Log error but continue showing page
                // Log::error('Failed to update task status: ' . $e->getMessage());
            }
        }

        return view('tasks.show', compact('task'));
    }

    public function downloadReport(Task $task)
    {
        $this->authorize('view', $task);

        try {
            $report = $this->speedyIndexService->getFullReport(
                $task->search_engine,
                $task->task_type,
                $task->external_task_id
            );

            // Generate CSV
            $filename = "task_{$task->id}_report_" . date('Y-m-d_His') . ".csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($report) {
                $file = fopen('php://output', 'w');
                
                // Headers
                fputcsv($file, ['URL', 'Status', 'Indexed At', 'Error']);

                // Data rows
                // Ensure 'urls' key exists to avoid warnings
                $rows = isset($report['urls']) ? $report['urls'] : [];
                
                foreach ($rows as $urlData) {
                    fputcsv($file, [
                        $urlData['url'] ?? '',
                        $urlData['status'] ?? 'unknown',
                        $urlData['indexed_at'] ?? '',
                        $urlData['error'] ?? '',
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            // Fixed unclosed string and missing parenthesis
            return back()->withErrors(['error' => 'Failed to download report: ' . $e->getMessage()]);
        }
    }

    protected function updateTaskFromApi(Task $task, array $statusData): void
    {
        // Update task based on API response
        // Added check to ensure statusData['tasks'] exists and has elements
        if (empty($statusData) || !isset($statusData['tasks']) || empty($statusData['tasks'])) {
            return;
        }

        // Assuming the API returns a list of tasks keyed by ID or array
        // Adjust logic based on exact API response structure
        $taskData = null;
        foreach ($statusData['tasks'] as $t) {
             if ((isset($t['id']) && $t['id'] == $task->external_task_id)) {
                 $taskData = $t;
                 break;
             }
        }
        
        // Fallback if we just take the first one (from original code logic)
        if (!$taskData) {
            $taskData = $statusData['tasks'][0] ?? [];
        }

        $task->update([
            'status' => $taskData['status'] ?? $task->status,
            'indexed_count' => $taskData['indexed_count'] ?? $task->indexed_count,
            'pending_count' => $taskData['pending_count'] ?? $task->pending_count,
            'error_count' => $taskData['error_count'] ?? $task->error_count,
        ]);
    }
}
