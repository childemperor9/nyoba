<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Coa; // Import model Coa

class ImportCoaCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:coa-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import COA data from MASTER COA 1.csv into the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = 'MASTER COA 1.csv';
        $delimiter = ';'; // !!! PENTING: Pastikan ini sesuai dengan delimiter CSV Anda (';' atau ',') !!!

        if (!Storage::disk('local')->exists($filePath)) {
            $this->error('File MASTER COA 1.csv tidak ditemukan di direktori storage/app/.');
            return Command::FAILURE;
        }

        $this->info('Starting COA CSV import...');
        $fullPath = Storage::disk('local')->path($filePath);
        $importedCount = 0;

        if (($handle = fopen($fullPath, 'r')) !== FALSE) {
            // Baca baris pertama (header)
            $header = fgetcsv($handle, 0, $delimiter);

            if ($header === FALSE) {
                $this->error('Tidak dapat membaca header dari file CSV. Pastikan file tidak kosong atau rusak.');
                fclose($handle);
                return Command::FAILURE;
            }

            // --- PERBAIKAN BOM DIMULAI DI SINI ---
            // Hapus BOM dari elemen header pertama jika ada
            if (isset($header[0])) {
                $header[0] = $this->stripUtf8Bom($header[0]);
            }
            // --- PERBAIKAN BOM SELESAI DI SINI ---

            // Bersihkan setiap elemen header (hapus spasi di awal/akhir)
            $cleanedHeader = array_map('trim', $header);

            // Konversi header menjadi huruf kapital untuk pencarian case-insensitive
            $upperHeader = array_map('strtoupper', $cleanedHeader);

            $this->info("Original Header (after BOM strip): " . implode(', ', $header));
            $this->info("Cleaned Header: " . implode(', ', $cleanedHeader));
            $this->info("Uppercased Header: " . implode(', ', $upperHeader));


            // Mengidentifikasi index kolom menggunakan header yang sudah di-uppercase
            $accNumIndex = array_search('ACC_NUM', $upperHeader);
            $accNameIndex = array_search('ACC_NAME', $upperHeader);

            if ($accNumIndex === false || $accNameIndex === false) {
                $this->error('Kolom ACC_NUM atau ACC_NAME tidak ditemukan setelah pemrosesan header. Pastikan nama kolomnya benar dan sesuai.');
                $this->error('Header yang terdeteksi di CSV Anda: [' . implode(', ', $header) . ']');
                $this->error('Header yang sudah dibersihkan: [' . implode(', ', $cleanedHeader) . ']');
                $this->error('Header yang sudah di-uppercase: [' . implode(', ', $upperHeader) . ']');
                fclose($handle);
                return Command::FAILURE;
            }

            while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
                // Skip baris kosong yang mungkin ada di akhir file
                if (count(array_filter($row)) === 0) {
                    continue;
                }

                if (count($row) !== count($header)) {
                    $this->warn("Skipping malformed row (column count mismatch): " . implode(',', $row));
                    continue;
                }

                // Buat associative array dari baris data menggunakan header yang sudah bersih sebagai kunci
                $record = array_combine($cleanedHeader, $row);

                // Akses data menggunakan kunci dari $cleanedHeader (yang sudah di-trim)
                $accNum = $record[$cleanedHeader[$accNumIndex]];
                $accName = $record[$cleanedHeader[$accNameIndex]];


                try {
                    Coa::create([
                        'acc_num' => $accNum,
                        'acc_name' => $accName,
                    ]);
                    $importedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == 23000) { // SQLSTATE for integrity constraint violation (duplicate entry)
                        $this->warn("Skipping duplicate ACC_NUM: " . $accNum);
                    } else {
                        $this->error("Error importing row: " . $e->getMessage() . " Data: " . implode(',', $row));
                    }
                }
            }
            fclose($handle);
            $this->info("Import finished. Total imported COA: " . $importedCount);
        } else {
            $this->error('Tidak dapat membuka file CSV. Periksa izin akses.');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Remove UTF-8 BOM from a string.
     *
     * @param string $text
     * @return string
     */
    protected function stripUtf8Bom($text)
    {
        $bom = pack('H*', 'EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
}
