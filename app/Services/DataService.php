<?php

namespace App\Services;

use App\Repositories\ExternalDataRepository;
use Exception;

class DataService
{
    private const REQUIRED_COLUMNS = ['YMD', 'NIM', 'NAMA'];

    public function __construct(
        protected ExternalDataRepository $repository
    ) {
    }

    /**
     * Get and parse external data with optional filters.
     *
     * @param array<string, string> $filters  Keys: nama, nim, ymd
     * @return array<int, array{YMD: string, NIM: string, NAMA: string}>
     * @throws Exception
     */
    public function getFilteredData(array $filters = []): array
    {
        $content = $this->getValidatedContent();
        $lines = explode("\n", trim($content['DATA']));

        $columnMap = $this->parseHeader(array_shift($lines));

        // Single-pass: parse + filter sekaligus
        $data = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = explode('|', $line);
            if (count($parts) < count(self::REQUIRED_COLUMNS)) {
                continue;
            }

            $row = [
                'YMD' => trim($parts[$columnMap['YMD']]),
                'NIM' => trim($parts[$columnMap['NIM']]),
                'NAMA' => trim($parts[$columnMap['NAMA']]),
            ];

            // Filter langsung saat parsing (skip yg tidak cocok)
            if ($this->matchesFilters($row, $filters)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Fetch & validate response from external source.
     *
     * @return array{RC: int, RCM: string, DATA: string}
     * @throws Exception
     */
    private function getValidatedContent(): array
    {
        $content = $this->repository->getRawData();

        if (!$content) {
            throw new Exception('Gagal mengambil data dari sumber eksternal.', 500);
        }

        if (!isset($content['RC']) || $content['RC'] != 200) {
            throw new Exception(
                $content['RCM'] ?? 'Error fetching data from external source',
                $content['RC'] ?? 500
            );
        }

        return $content;
    }

    /**
     * Parse header line dan return column map.
     *
     * @param  string $headerLine  e.g. "YMD|NIM|NAMA" atau "NAMA|YMD|NIM"
     * @return array<string, int>  e.g. ['YMD' => 0, 'NIM' => 1, 'NAMA' => 2]
     * @throws Exception
     */
    private function parseHeader(string $headerLine): array
    {
        $headers = array_map(fn($h) => strtoupper(trim($h)), explode('|', $headerLine));
        $columnMap = array_flip($headers);

        foreach (self::REQUIRED_COLUMNS as $col) {
            if (!isset($columnMap[$col])) {
                throw new Exception("Kolom '{$col}' tidak ditemukan di header: {$headerLine}", 500);
            }
        }

        return $columnMap;
    }

    /**
     * Check apakah row cocok dengan semua filter yang diberikan.
     *
     * @param  array{YMD: string, NIM: string, NAMA: string} $row
     * @param  array<string, string> $filters
     * @return bool
     */
    private function matchesFilters(array $row, array $filters): bool
    {
        if (
            !empty($filters['nama'])
            && !str_contains(strtolower($row['NAMA']), strtolower($filters['nama']))
        ) {
            return false;
        }

        if (!empty($filters['nim']) && $row['NIM'] !== $filters['nim']) {
            return false;
        }

        if (!empty($filters['ymd']) && $row['YMD'] !== $filters['ymd']) {
            return false;
        }

        return true;
    }
}
