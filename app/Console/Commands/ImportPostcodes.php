<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use ZipArchive;

class ImportPostcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-postcodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a list of postcodes from the mySociety open data downloads to be stored in the db';

    /**
     * Execute the console command.
     *
     * @throws \League\Csv\UnavailableStream
     * @throws \League\Csv\Exception
     */
    public function handle(): int
    {
        $url = 'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip';
        $zipFilename = 'uk_postcodes.zip';
        $csvFilePath = 'postcodes/Data/ONSPD_NOV_2022_UK.csv';

        if (! $this->downloadZipFile($url, $zipFilename)) {
            return 1;
        }

        if (! $this->extractZipFile($zipFilename, $csvFilePath)) {
            return 1;
        }

        if (! $this->processCsvFile(Storage::path($csvFilePath))) {
            return 1;
        }

        $this->info('All postcodes imported successfully.');

        return 0;
    }

    private function downloadZipFile(string $url, string $zipFilename): bool
    {
        if (Storage::disk('local')->exists($zipFilename)) {
            $this->info('Postcode ZIP file already downloaded.');

            return true;
        }

        $this->info('Downloading postcode ZIP file...');
        $response = Http::get($url);

        if ($response->failed()) {
            $this->error('Failed to download the postcode ZIP file.');

            return false;
        }

        Storage::disk('local')->put($zipFilename, $response->body());
        $this->info('Postcode ZIP file downloaded successfully.');

        return true;
    }

    private function extractZipFile(string $zipFilename, string $csvFilePath): bool
    {
        if (Storage::exists($csvFilePath)) {
            $this->info('ZIP file already extracted.');

            return true;
        }

        $this->info('Extracting ZIP file...');
        $zip = new ZipArchive;
        if ($zip->open(Storage::path($zipFilename)) !== true) {
            $this->error('Failed to extract the ZIP file.');

            return false;
        }

        $zip->extractTo(Storage::path('postcodes/'));
        $zip->close();
        $this->info('ZIP file extracted successfully.');

        return true;
    }

    /**
     * @throws \League\Csv\UnavailableStream
     * @throws \League\Csv\Exception
     */
    private function processCsvFile(string $csvFilePath): bool
    {
        if (! file_exists($csvFilePath)) {
            $this->error('CSV file does not exist: '.$csvFilePath);

            return false;
        }

        $this->info('Processing CSV file: '.$csvFilePath);
        $csv = Reader::createFromPath($csvFilePath);
        $csv->setHeaderOffset(0);

        $records = [];

        DB::transaction(function () use ($csv, &$records) {
            $batchSize = 1000;
            foreach ($csv as $record) {
                $records[] = $this->mapCsvRecord($record);

                if (count($records) === $batchSize) {
                    DB::table('postcodes')->insertOrIgnore($records);
                    $records = [];
                }
            }

            if (! empty($records)) {
                DB::table('postcodes')->insertOrIgnore($records);
            }
        });

        $this->info('Finished processing CSV file: '.$csvFilePath);

        return true;
    }

    private function mapCsvRecord(array $record): array
    {
        return [
            'postcode' => $record['pcd'],
            'latitude' => $record['lat'],
            'longitude' => $record['long'],
        ];
    }
}
