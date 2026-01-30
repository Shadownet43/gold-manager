<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ImportOldTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data dari database lama:
     * - tabel: transaksi
     * - kolom lama: tanggal, jam, jumlah_gold
     * - kolom baru: date, time, gold_amount
     */
    public function run(): void
    {
        // Data dari database lama
        $oldData = [
            [
                'id' => 1,
                'tanggal' => '2026-01-26',
                'jam' => '08:16:31',
                'rate' => 912,
                'jumlah_gold' => 700,
                'tax_status' => 0,
                'total_rupiah' => 638400,
                'catatan' => null
            ],
            [
                'id' => 2,
                'tanggal' => '2026-01-19',
                'jam' => '10:24:27',
                'rate' => 1230,
                'jumlah_gold' => 1010,
                'tax_status' => 1,
                'total_rupiah' => 1223665.5,
                'catatan' => null
            ],
            [
                'id' => 3,
                'tanggal' => '2026-01-13',
                'jam' => '10:27:40',
                'rate' => 500,
                'jumlah_gold' => 1500,
                'tax_status' => 0,
                'total_rupiah' => 750000,
                'catatan' => null
            ],
            [
                'id' => 4,
                'tanggal' => '2026-01-16',
                'jam' => '10:28:18',
                'rate' => 1000,
                'jumlah_gold' => 1000,
                'tax_status' => 0,
                'total_rupiah' => 1000000,
                'catatan' => null
            ],
            [
                'id' => 5,
                'tanggal' => '2026-01-17',
                'jam' => '10:29:05',
                'rate' => 1120,
                'jumlah_gold' => 1000,
                'tax_status' => 0,
                'total_rupiah' => 1120000,
                'catatan' => null
            ],
            [
                'id' => 6,
                'tanggal' => '2026-01-04',
                'jam' => '10:30:22',
                'rate' => 400,
                'jumlah_gold' => 1400,
                'tax_status' => 0,
                'total_rupiah' => 560000,
                'catatan' => null
            ],
        ];

        // Import data dengan mapping kolom
        foreach ($oldData as $old) {
            // Cek apakah ID sudah ada (untuk menghindari duplikasi)
            $exists = DB::table('transactions')->where('id', $old['id'])->exists();
            
            if (!$exists) {
                // Gunakan DB::table()->insert() untuk bisa set ID manual
                DB::table('transactions')->insert([
                    'id' => $old['id'],
                    'user_id' => 1,
                    'date' => $old['tanggal'],
                    'time' => $old['jam'],
                    'rate' => $old['rate'],
                    'gold_amount' => $old['jumlah_gold'],
                    'tax_status' => $old['tax_status'],
                    'total_rupiah' => $old['total_rupiah'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->command->info("✓ Imported transaction ID: {$old['id']} - {$old['tanggal']}");
            } else {
                $this->command->warn("⚠ Transaction ID {$old['id']} sudah ada, dilewati.");
            }
        }

        $this->command->info("\n✅ Import selesai!");
    }
}
