<?php

namespace Tests\Unit;

use App\Repositories\ExternalDataRepository;
use App\Services\DataService;
use Exception;
use PHPUnit\Framework\TestCase;

class DataServiceTest extends TestCase
{
    private function makeMockRepo(array|null $returnData): ExternalDataRepository
    {
        $mock = $this->createMock(ExternalDataRepository::class);
        $mock->method('getRawData')->willReturn($returnData);

        return $mock;
    }

    private function fakeApiResponse(string $header = 'YMD|NIM|NAMA'): array
    {
        return [
            'RC' => 200,
            'RCM' => 'OK',
            'DATA' => implode("\n", [
                $header,
                '20220713|9352078461|Turner Mia',
                '20230405|1206485739|Aiden Hayes',
                '20230405|8761043925|Sophia Martinez',
                '20221014|0471953286|Adams Noah',
            ]),
        ];
    }

    // ─── Header Order Tests ──────────────────────────────────────

    public function test_parse_standard_header_ymd_nim_nama(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse('YMD|NIM|NAMA')));

        $data = $service->getFilteredData();

        $this->assertCount(4, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
        $this->assertEquals('9352078461', $data[0]['NIM']);
        $this->assertEquals('20220713', $data[0]['YMD']);
    }

    public function test_parse_reversed_header_nama_ymd_nim(): void
    {
        $response = [
            'RC' => 200,
            'RCM' => 'OK',
            'DATA' => "NAMA|YMD|NIM\nTurner Mia|20220713|9352078461\nAiden Hayes|20230405|1206485739",
        ];
        $service = new DataService($this->makeMockRepo($response));

        $data = $service->getFilteredData();

        $this->assertCount(2, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
        $this->assertEquals('20220713', $data[0]['YMD']);
        $this->assertEquals('9352078461', $data[0]['NIM']);
    }

    public function test_parse_header_nim_nama_ymd(): void
    {
        $response = [
            'RC' => 200,
            'RCM' => 'OK',
            'DATA' => "NIM|NAMA|YMD\n9352078461|Turner Mia|20220713",
        ];
        $service = new DataService($this->makeMockRepo($response));

        $data = $service->getFilteredData();

        $this->assertCount(1, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
        $this->assertEquals('9352078461', $data[0]['NIM']);
        $this->assertEquals('20220713', $data[0]['YMD']);
    }

    // ─── Filter Tests ────────────────────────────────────────────

    public function test_filter_by_nama(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData(['nama' => 'Turner Mia']);

        $this->assertCount(1, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
    }

    public function test_filter_by_nama_partial_match(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData(['nama' => 'Turner']);

        $this->assertCount(1, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
    }

    public function test_filter_by_nama_case_insensitive(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData(['nama' => 'turner mia']);

        $this->assertCount(1, $data);
    }

    public function test_filter_by_nim(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData(['nim' => '9352078461']);

        $this->assertCount(1, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
    }

    public function test_filter_by_ymd(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData(['ymd' => '20230405']);

        $this->assertCount(2, $data);
    }

    public function test_filter_by_nim_only_others_null(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData(['nim' => '9352078461', 'nama' => null, 'ymd' => null]);

        $this->assertCount(1, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
    }

    public function test_filter_combined_all_match(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData([
            'nama' => 'Turner Mia',
            'nim' => '9352078461',
            'ymd' => '20220713',
        ]);

        $this->assertCount(1, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
    }

    public function test_filter_combined_no_match(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData([
            'nama' => 'Turner Mia',
            'nim' => '9352078461',
            'ymd' => '20230405', // YMD salah untuk Turner Mia
        ]);

        $this->assertCount(0, $data);
    }

    public function test_no_filter_returns_all(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData();

        $this->assertCount(4, $data);
    }

    public function test_filter_no_result(): void
    {
        $service = new DataService($this->makeMockRepo($this->fakeApiResponse()));

        $data = $service->getFilteredData(['nama' => 'Tidak Ada']);

        $this->assertCount(0, $data);
    }

    // ─── Error Handling Tests ────────────────────────────────────

    public function test_throws_exception_when_repo_returns_null(): void
    {
        $service = new DataService($this->makeMockRepo(null));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Gagal mengambil data dari sumber eksternal.');

        $service->getFilteredData();
    }

    public function test_throws_exception_when_rc_not_200(): void
    {
        $service = new DataService($this->makeMockRepo([
            'RC' => 500,
            'RCM' => 'Server Error',
        ]));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Server Error');

        $service->getFilteredData();
    }

    public function test_throws_exception_when_header_missing_column(): void
    {
        $service = new DataService($this->makeMockRepo([
            'RC' => 200,
            'RCM' => 'OK',
            'DATA' => "YMD|NIM\n20220713|9352078461",
        ]));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Kolom 'NAMA' tidak ditemukan");

        $service->getFilteredData();
    }

    // ─── Edge Cases ──────────────────────────────────────────────

    public function test_skips_empty_lines(): void
    {
        $response = [
            'RC' => 200,
            'RCM' => 'OK',
            'DATA' => "YMD|NIM|NAMA\n20220713|9352078461|Turner Mia\n\n\n20230405|1206485739|Aiden Hayes\n",
        ];
        $service = new DataService($this->makeMockRepo($response));

        $data = $service->getFilteredData();

        $this->assertCount(2, $data);
    }

    public function test_trims_whitespace_in_values(): void
    {
        $response = [
            'RC' => 200,
            'RCM' => 'OK',
            'DATA' => " YMD | NIM | NAMA \n 20220713 | 9352078461 | Turner Mia ",
        ];
        $service = new DataService($this->makeMockRepo($response));

        $data = $service->getFilteredData();

        $this->assertCount(1, $data);
        $this->assertEquals('Turner Mia', $data[0]['NAMA']);
        $this->assertEquals('9352078461', $data[0]['NIM']);
        $this->assertEquals('20220713', $data[0]['YMD']);
    }
}
