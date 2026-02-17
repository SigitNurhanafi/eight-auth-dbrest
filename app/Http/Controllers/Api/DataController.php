<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataController extends Controller
{
    protected $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Fetch data from external URL and filter it via DataService.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchData(Request $request): JsonResponse
    {
        try {
            $data = $this->dataService->getFilteredData($request->all());

            return response()->json([
                'RC' => 200,
                'RCM' => 'OK',
                'DATA' => $data
            ]);
        }
        catch (Exception $e) {
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
