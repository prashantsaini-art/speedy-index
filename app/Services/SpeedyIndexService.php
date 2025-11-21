<?php

namespace App\Services;

use App\Models\ApiLog;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeedyIndexService
{
    protected $apiKey;

    protected $baseUrl;

    protected $timeout;

    protected $retryTimes;

    protected $retryDelay;

    public function __construct()
    {
        $this->apiKey = config('services.speedyindex.api_key');
        $this->baseUrl = config('services.speedyindex.base_url');
        $this->timeout = config('services.speedyindex.timeout');
        $this->retryTimes = config('services.speedyindex.retry_times');
        $this->retryDelay = config('services.speedyindex.retry_delay');
    }

    /**
     * Get account balance
     */
    public function getBalance(): array
    {
        $response = $this->makeRequest('GET', '/account');

        return $response->json();
    }

    /**
     * Create indexing or checking task
     */
    public function createTask(
        string $searchEngine,
        string $taskType,
        array $urls,
        ?string $title = null
    ): array {
        $endpoint = "/task/{$searchEngine}/{$taskType}/create";
        $payload = [
            'urls' => $urls,
        ];
        if ($title) {
            $payload['title'] = $title;
        }
        $response = $this->makeRequest('POST', $endpoint, $payload);

        return $response->json();
    }

    /**
     * Get task list with pagination
     */
    public function getTaskList(
        string $searchEngine,
        string $taskType,
        int $page = 1
    ): array {
        $endpoint = "/task/{$searchEngine}/{$taskType}/list/{$page}";
        $response = $this->makeRequest('GET', $endpoint);

        return $response->json();
    }

    /**
     * Get task status by IDs
     */
    public function getTaskStatus(
        string $searchEngine,
        string $taskType,
        array $taskIds
    ): array {
        $endpoint = "/task/{$searchEngine}/{$taskType}/status";
        $response = $this->makeRequest('POST', $endpoint, ['ids' => $taskIds]);

        return $response->json();
    }

    /**
     * Get full task report
     */
    public function getFullReport(
        string $searchEngine,
        string $taskType,
        int $taskId
    ): array {
        $endpoint = "/task/{$searchEngine}/{$taskType}/fullreport";
        $response = $this->makeRequest('POST', $endpoint, ['id' => $taskId]);

        return $response->json();
    }

    /**
     * Index single URL
     */
    public function indexSingleUrl(string $searchEngine, string $url): array
    {
        $endpoint = "/{$searchEngine}/url";
        $response = $this->makeRequest('POST', $endpoint, ['link' => $url]);

        return $response->json();
    }

    /**
     * Create VIP task (priority queue)
     */
    public function createVipTask(array $urls, ?string $title = null): array
    {
        $endpoint = '/task/google/indexer/vip';
        $payload = ['urls' => $urls];
        if ($title) {
            $payload['title'] = $title;
        }
        $response = $this->makeRequest('POST', $endpoint, $payload);

        return $response->json();
    }

    /**
     * Make HTTP request with error handling and logging
     */
    protected function makeRequest(
        string $method,
        string $endpoint,
        array $data = []
    ): Response {
        $startTime = microtime(true);
        $fullUrl = $this->baseUrl.$endpoint;
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ])
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay)
                ->{strtolower($method)}($fullUrl, $data);
            $responseTime = microtime(true) - $startTime;
            // Log successful request
            $this->logRequest(
                $endpoint,
                $method,
                $data,
                $response->json(),
                $response->status(),
                null,
                $responseTime
            );

            return $response;
        } catch (\Exception $e) {
            $responseTime = microtime(true) - $startTime;
            // Log failed request
            $this->logRequest(
                $endpoint,
                $method,
                $data,
                null,
                null,
                $e->getMessage(),
                $responseTime
            );
            Log::error('SpeedyIndex API Error', [
                'endpoint' => $endpoint,
                'method' => $method,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Log API request/response
     */
    protected function logRequest(
        string $endpoint,
        string $method,
        ?array $requestData,
        ?array $responseData,
        ?int $statusCode,
        ?string $errorMessage,
        float $responseTime
    ): void {
        ApiLog::create([
            'user_id' => auth()->id(),
            'endpoint' => $endpoint,
            'method' => $method,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'status_code' => $statusCode,
            'error_message' => $errorMessage,
            'response_time' => $responseTime,
        ]);
    }
}
