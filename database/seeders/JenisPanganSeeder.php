<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JenisPanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ['nama_pangan' => 'Beras Kualitas 1', 'harga' => 13000.00],
            ['nama_pangan' => 'Beras Kualitas 2', 'harga' => 12000.00],
            ['nama_pangan' => 'Beras Kualitas 3', 'harga' => 11000.00],
            ['nama_pangan' => 'Beras Kualitas 4', 'harga' => 10000.00],
            ['nama_pangan' => 'Beras Kualitas 5', 'harga' => 9500.00],
            ['nama_pangan' => 'Daging Sapi Kualitas 1', 'harga' => 40000.00],
            ['nama_pangan' => 'Daging Sapi Kualitas 2', 'harga' => 37000.00],
            ['nama_pangan' => 'Daging Sapi Kualitas 3', 'harga' => 35000.00],
            ['nama_pangan' => 'Daging Ayam Kualitas 1', 'harga' => 25000.00],
            ['nama_pangan' => 'Daging Ayam Kualitas 2', 'harga' => 23000.00],
            ['nama_pangan' => 'Daging Ayam Kualitas 3', 'harga' => 22000.00],
            ['nama_pangan' => 'Salmon', 'harga' => 50000.00],
            ['nama_pangan' => 'Ikan Tuna', 'harga' => 45000.00],
            ['nama_pangan' => 'Ikan Kembung', 'harga' => 12000.00],
            ['nama_pangan' => 'Ikan Tongkol', 'harga' => 15000.00],
            ['nama_pangan' => 'Udang', 'harga' => 60000.00],
            ['nama_pangan' => 'Cumi-cumi', 'harga' => 65000.00],
            ['nama_pangan' => 'Kerang', 'harga' => 25000.00],
            ['nama_pangan' => 'Ikan Lele', 'harga' => 15000.00],
            ['nama_pangan' => 'Ikan Nila', 'harga' => 20000.00],
            ['nama_pangan' => 'Ikan Mas', 'harga' => 18000.00],
            ['nama_pangan' => 'Tomat', 'harga' => 7500.00],
            ['nama_pangan' => 'Cabe Merah', 'harga' => 12000.00],
            ['nama_pangan' => 'Cabe Rawit', 'harga' => 16000.00],
            ['nama_pangan' => 'Bawang Merah Kualitas 1', 'harga' => 20000.00],
            ['nama_pangan' => 'Bawang Merah Kualitas 2', 'harga' => 17000.00],
            ['nama_pangan' => 'Bawang Merah Kualitas 3', 'harga' => 15000.00],
            ['nama_pangan' => 'Bawang Putih Kualitas 1', 'harga' => 18000.00],
            ['nama_pangan' => 'Bawang Putih Kualitas 2', 'harga' => 17000.00],
            ['nama_pangan' => 'Bawang Putih Kualitas 3', 'harga' => 16000.00],
            ['nama_pangan' => 'Kentang', 'harga' => 11000.00],
            ['nama_pangan' => 'Wortel', 'harga' => 12000.00],
            ['nama_pangan' => 'Kubis', 'harga' => 8000.00],
            ['nama_pangan' => 'Kacang Panjang', 'harga' => 9000.00],
            ['nama_pangan' => 'Sawi', 'harga' => 10000.00],
            ['nama_pangan' => 'Bayam', 'harga' => 8000.00],
            ['nama_pangan' => 'Kacang Tanah', 'harga' => 18000.00],
            ['nama_pangan' => 'Gandum', 'harga' => 8000.00],
            ['nama_pangan' => 'Kacang Kedelai', 'harga' => 10000.00],
            ['nama_pangan' => 'Kacang Hijau', 'harga' => 15000.00],
            ['nama_pangan' => 'Kacang Merah', 'harga' => 13000.00],
            ['nama_pangan' => 'Kacang Mede', 'harga' => 20000.00],
            ['nama_pangan' => 'Kedelai', 'harga' => 12000.00],
            ['nama_pangan' => 'Tepung Jagung', 'harga' => 7000.00],
            ['nama_pangan' => 'Tempe', 'harga' => 15000.00],
            ['nama_pangan' => 'Tahu', 'harga' => 14000.00],
            ['nama_pangan' => 'Jagung', 'harga' => 11000.00],
            ['nama_pangan' => 'Singkong', 'harga' => 9000.00],
            ['nama_pangan' => 'Ubi Jalar', 'harga' => 10000.00],
            ['nama_pangan' => 'Gula Pasir', 'harga' => 12000.00],
            ['nama_pangan' => 'Garam', 'harga' => 5000.00],
            ['nama_pangan' => 'Merica', 'harga' => 7000.00],
            ['nama_pangan' => 'Kopi', 'harga' => 35000.00],
            ['nama_pangan' => 'Teh', 'harga' => 25000.00],
            ['nama_pangan' => 'Kakao', 'harga' => 25000.00],
            ['nama_pangan' => 'Vanili', 'harga' => 20000.00],
            ['nama_pangan' => 'Ragi', 'harga' => 10000.00],
            ['nama_pangan' => 'Baking Soda', 'harga' => 8000.00],
            ['nama_pangan' => 'Asam Jawa', 'harga' => 5000.00],
            ['nama_pangan' => 'Lada', 'harga' => 10000.00],
            ['nama_pangan' => 'Bubuk Cabe', 'harga' => 10000.00],
            ['nama_pangan' => 'Bubuk Kunyit', 'harga' => 12000.00],
            ['nama_pangan' => 'Bubuk Jahe', 'harga' => 15000.00],
            ['nama_pangan' => 'Bubuk Ketumbar', 'harga' => 10000.00],
            ['nama_pangan' => 'Bubuk Lengkuas', 'harga' => 12000.00],
            ['nama_pangan' => 'Cuka Apel', 'harga' => 18000.00],
            ['nama_pangan' => 'Cuka Dapur', 'harga' => 8000.00],
            ['nama_pangan' => 'Kecap Manis', 'harga' => 12000.00],
            ['nama_pangan' => 'Kecap Asin', 'harga' => 10000.00],
            ['nama_pangan' => 'Madu Hutan', 'harga' => 60000.00],
            ['nama_pangan' => 'Madu Kelapa', 'harga' => 50000.00],
            ['nama_pangan' => 'Saos Tiram', 'harga' => 15000.00],
            ['nama_pangan' => 'Minyak Goreng Kualitas 1', 'harga' => 15000.00],
            ['nama_pangan' => 'Minyak Goreng Kualitas 2', 'harga' => 13000.00],
            ['nama_pangan' => 'Minyak Goreng Kualitas 3', 'harga' => 11000.00],
            ['nama_pangan' => 'Tepung Terigu', 'harga' => 8000.00],
            ['nama_pangan' => 'Tepung Beras', 'harga' => 10000.00],
            ['nama_pangan' => 'Susu Kualitas 1', 'harga' => 20000.00],
            ['nama_pangan' => 'Susu Kualitas 2', 'harga' => 18000.00],
            ['nama_pangan' => 'Susu Kualitas 3', 'harga' => 16000.00],
            ['nama_pangan' => 'Keju', 'harga' => 30000.00],
            ['nama_pangan' => 'Telur Ayam', 'harga' => 20000.00],
            ['nama_pangan' => 'Telur Bebek', 'harga' => 25000.00],
            // Tambahkan hingga 100 data sesuai kebutuhan
        ];

        // Menambahkan 100 data pangan
        foreach ($data as $item) {
            DB::table('jenis_pangan')->insert($item);
        }
    }
}
