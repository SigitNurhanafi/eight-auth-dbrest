<?php

namespace App\Services;

use App\Repositories\ExternalDataRepository;
use Exception;

class DataService
{
    protected $repository;

    public function __construct(ExternalDataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get and parse external data with optional filters.
     *
     * @param array $filters
     * @return array
     * @throws Exception
     */
    public function getFilteredData(array $filters = []): array
    {
        $content = $this->repository->getRawData();

        if (!$content) {
            throw new Exception('Gagal mengambil data dari sumber eksternal.', 500);
        }

        if (!isset($content['RC']) || $content['RC'] != 200) {
            throw new Exception($content['RCM'] ?? 'Error fetching data from external source', $content['RC'] ?? 500);
        }

        $rawData = $content['DATA'];
        $lines = explode("\n", trim($rawData));

        // Remove header (YMD|NIM|NAMA)
        array_shift($lines);

        $data = [];
        foreach ($lines as $line) {
            if (empty($line))
                continue;

            $parts = explode("|", $line);
            if (count($parts) === 3) {
                $data[] = [
                    'YMD' => $parts[0],
                    'NIM' => $parts[1],
                    'NAMA' => $parts[2],
                ];
            }
        }

        // Apply filters
        if (!empty($filters['nama'])) {
            $query = strtolower($filters['nama']);
            $data = array_filter($data, function ($item) use ($query) {
                return str_contains(strtolower($item['NAMA']), $query);
            });
        }

        if (!empty($filters['nim'])) {
            $query = $filters['nim'];
            $data = array_filter($data, function ($item) use ($query) {
                return $item['NIM'] == $query;
            });
        }

        if (!empty($filters['ymd'])) {
            $query = $filters['ymd'];
            $data = array_filter($data, function ($item) use ($query) {
                return $item['YMD'] == $query;
            });
        }

        return array_values($data);
    }
}
