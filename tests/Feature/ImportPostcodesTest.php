<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class ImportPostcodesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Set up necessary database structures for testing
        $this->artisan('migrate');

        Http::preventStrayRequests();

        $this->mockHttpResponse();
    }

    private function mockHttpResponse(): void
    {
        $csvContent = "pcd,lat,long\nAB10 1XG,57.144165,-2.114848\nAB10 6RN,57.137879,-2.121487\n";

        // Create a temporary CSV file
        $csvFilePath = tempnam(sys_get_temp_dir(), 'ONSPD_NOV_2022_UK').'.csv';
        file_put_contents($csvFilePath, $csvContent);

        // Create a temporary ZIP file containing the CSV file
        $zip = new ZipArchive();
        $zipFilePath = tempnam(sys_get_temp_dir(), '2022-11').'.zip';
        $zip->open($zipFilePath, ZipArchive::CREATE);
        $zip->addFile($csvFilePath, 'Data/ONSPD_NOV_2022_UK.csv');
        $zip->close();

        // Mock the HTTP response to return the ZIP file content
        Http::fake([
            'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip' => Http::response(file_get_contents($zipFilePath)),
        ]);

        // Clean up temporary files
        unlink($csvFilePath);
        unlink($zipFilePath);
    }

    public function test_import_postcodes_command(): void
    {
        // Mock the Storage facade
        Storage::fake('local');

        // Run the command
        Artisan::call('app:import-postcodes');

        // Assert the file was saved
        Storage::disk('local')->assertExists('uk_postcodes.zip');

        // Assert the CSV file was extracted
        Storage::disk('local')->assertExists('postcodes/Data/ONSPD_NOV_2022_UK.csv');

        // Assert the data was inserted into the database
        $this->assertDatabaseHas('postcodes', [
            'postcode' => 'AB10 1XG',
            'latitude' => '57.144165',
            'longitude' => '-2.114848',
        ]);

        $this->assertDatabaseHas('postcodes', [
            'postcode' => 'AB10 6RN',
            'latitude' => '57.137879',
            'longitude' => '-2.121487',
        ]);
    }
}
