<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\JenisPangan;
use App\Models\HargaHistori;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HargaHistoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data jenis pangan
        $jenisPangans = JenisPangan::all();

        foreach ($jenisPangans as $jenisPangan) {
            // Generate 5 histori harga untuk tiap jenis pangan
            $hargaSaatIni = $jenisPangan->harga;
            $tanggal = Carbon::now()->subDays(5); // Mulai dari 5 hari lalu

            for ($i = 0; $i < 5; $i++) {
                // Harga naik atau turun secara acak
                $hargaBaru = $hargaSaatIni + rand(-3000, 3000); // Maksimal perubahan harga adalah 3000
                $hargaBaru = max(5000, $hargaBaru); // Pastikan harga tidak kurang dari 5000

                HargaHistori::create([
                    'jenis_pangan_id' => $jenisPangan->id,
                    'harga' => $hargaBaru,
                    'created_at' => $tanggal,
                ]);

                // Update harga saat ini untuk iterasi berikutnya
                $hargaSaatIni = $hargaBaru;

                // Kurangi tanggal untuk histori berikutnya
                $tanggal = $tanggal->subDay();
            }
        }
    }
}
