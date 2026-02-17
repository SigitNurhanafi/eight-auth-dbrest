<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalDataRepository
{
    /**
     * Fetch raw data from the external API.
     *
     * @return array|null
     */
    public function getRawData(): ?array
    {
        $url = env('EXTERNAL_DATA_URL');

        if (!$url) {
            Log::error('EXTERNAL_DATA_URL is not configured in .env');
            return null;
        }

        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                Log::error('External API returned error status: ' . $response->status());
                return null;
            }

            return $response->json();
        }
        catch (\Exception $e) {
            Log::error('External Repository Error: ' . $e->getMessage());
            return null;
        }
    }
}
