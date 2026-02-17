<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchDataRequest;
use App\Services\ExternalDataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    protected $dataService;

    public function __construct(ExternalDataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Fetch data from external URL and filter it via ExternalDataService.
     *
     * @param SearchDataRequest $request
     * @return JsonResponse
     */
    public function fetchData(SearchDataRequest $request): JsonResponse
    {
        try {
            $data = $this->dataService->getFilteredData($request->validated());

            return response()->json([
                'RC' => 200,
                'RCM' => 'OK',
                'DATA' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Data Fetch Error: ' . $e->getMessage());

            $statusCode = $e->getCode();
            // Ensure status code is valid for HTTP response
            if (!is_int($statusCode) || $statusCode < 100 || $statusCode > 599) {
                $statusCode = 500;
            }

            return response()->json([
                'RC' => $statusCode,
                'RCM' => $e->getMessage(),
                'DATA' => []
            ], $statusCode);
        }
    }
}
