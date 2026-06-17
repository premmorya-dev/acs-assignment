<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CsvImportService
{
    private const CHUNK_SIZE = 1000;

    public function import(UploadedFile $file): array
    {
        $path = Storage::disk('local')->putFile('uploads', $file);
        $fullPath = Storage::disk('local')->path($path);

        try {
            return DB::transaction(function () use ($fullPath) {

                $handle = fopen($fullPath, 'rb');

                if ($handle === false) {
                    throw new \RuntimeException('Unable to open uploaded CSV file.');
                }

                $header = fgetcsv($handle);

                if ($header === false) {
                    throw new \RuntimeException('CSV file is empty.');
                }

                $header = array_map('trim', $header);

                $rows = [];
                $seenEmails = [];
                $duplicateCount = 0;
                $totalRecords = 0;
                $insertedRecords = 0;

                while (($data = fgetcsv($handle)) !== false) {

                    $totalRecords++;

                    $row = array_combine($header, array_map('trim', $data));

                    if (! $row) {
                        continue;
                    }

                    $validator = Validator::make($row, [
                        'Name' => 'required|string|max:255',
                        'Phone Number' => 'required|string|max:50',
                        'Email' => 'required|email|max:255',
                        'Payment Amount' => 'required|numeric|min:0',
                    ]);

                    if ($validator->fails()) {
                        continue;
                    }

                    $email = $row['Email'];

                    if (
                        isset($seenEmails[$email]) ||
                        Customer::where('email', $email)->exists()
                    ) {
                        $duplicateCount++;
                        $seenEmails[$email] = true;
                        continue;
                    }

                    $seenEmails[$email] = true;

                    $rows[] = [
                        'name' => $row['Name'],
                        'phone_number' => $row['Phone Number'],
                        'email' => $email,
                        'payment_amount' => $row['Payment Amount'],
                        'payment_status' => 'Pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Insert chunk
                    if (count($rows) >= self::CHUNK_SIZE) {

                        Customer::insert($rows);

                        $insertedRecords += count($rows);

                        // Memory free
                        $rows = [];
                    }
                }

                // Remaining records
                if (! empty($rows)) {

                    Customer::insert($rows);

                    $insertedRecords += count($rows);
                }

                fclose($handle);

                return [
                    'success' => true,
                    'total_records' => $totalRecords,
                    'inserted_records' => $insertedRecords,
                    'duplicate_records' => $duplicateCount,
                ];
            });
        } finally {
            Storage::disk('local')->delete($path);
        }
    }
}