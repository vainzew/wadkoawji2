<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MemberSeeder extends Seeder
{
    // Indonesian male names
    private $maleNames = [
        'Budi', 'Agus', 'Andi', 'Dedi', 'Eko', 'Fajar', 'Guntur', 'Hadi', 'Iwan', 'Joko',
        'Kurniawan', 'Lukman', 'Mulyadi', 'Nugroho', 'Oka', 'Putu', 'Rudi', 'Surya', 'Teguh', 'Umar',
        'Viktor', 'Wawan', 'Yudi', 'Zainal', 'Ahmad', 'Bayu', 'Cahyo', 'Dodi', 'Edi', 'Ferry',
        'Gilang', 'Hendra', 'Imam', 'Joni', 'Krisna', 'Lingga', 'Maman', 'Nanda', 'Oman', 'Prasetya',
        'Rio', 'Sigit', 'Taufik', 'Usman', 'Vino', 'Wahyu', 'Yoga', 'Zulkifli'
    ];

    // Indonesian female names
    private $femaleNames = [
        'Ani', 'Bunga', 'Cinta', 'Dewi', 'Eka', 'Fitri', 'Gladys', 'Hana', 'Indah', 'Juli',
        'Kartika', 'Lestari', 'Murni', 'Nurul', 'Oktavia', 'Putri', 'Ratna', 'Sari', 'Tuti', 'Umi',
        'Vina', 'Wulan', 'Yuni', 'Zahra', 'Ayu', 'Belinda', 'Citra', 'Dina', 'Endah', 'Fina',
        'Gita', 'Hesti', 'Intan', 'Jenny', 'Kirana', 'Lia', 'Maya', 'Nova', 'Olivia', 'Puspita',
        'Queen', 'Rina', 'Sinta', 'Tania', 'Ulfa', 'Vivi', 'Winda', 'Yulia', 'Zara'
    ];

    // Indonesian surnames
    private $surnames = [
        'Santoso', 'Setiawan', 'Suryanto', 'Wijaya', 'Saputra', 'Pratama', 'Kusuma', 'Hidayat',
        'Sulistyo', 'Nugroho', 'Permadi', 'Wibowo', 'Sihombing', 'Salim', 'Siregar', 'Utama',
        'Hakim', 'Zain', 'Firdaus', 'Fauzi', 'Prasetyo', 'Putra', 'Aditya', 'Suryadi', 'Hermawan',
        'Saputro', 'Samosir', 'Manullang', 'Harahap', 'Pangestu', 'Gunawan', 'Yulianto', 'Widodo',
        'Susanto', 'Maulana', 'Irawan', 'Fernando', 'Wicaksono', 'Hardianto', 'Budiman', 'Suryono'
    ];

    // Indonesian addresses
    private $addresses = [
        'Jl. Kenanga No. 5, RT 03/RW 07, Jakarta Selatan',
        'Jl. Melati No. 12, RT 05/RW 09, Bandung',
        'Jl. Mawar No. 8, RT 02/RW 06, Surabaya',
        'Jl. Anggrek No. 15, RT 04/RW 08, Yogyakarta',
        'Jl. Dahlia No. 20, RT 01/RW 05, Semarang',
        'Jl. Cempaka No. 3, RT 06/RW 10, Medan',
        'Jl. Kamboja No. 18, RT 03/RW 07, Makassar',
        'Jl. Bougenville No. 25, RT 02/RW 04, Palembang',
        'Jl. Flamboyan No. 7, RT 05/RW 09, Denpasar',
        'Jl. Sakura No. 30, RT 01/RW 03, Malang',
        'Jl. Teratai No. 10, RT 04/RW 08, Solo',
        'Jl. Yasmin No. 22, RT 03/RW 06, Pekanbaru',
        'Jl. Kenari No. 14, RT 02/RW 05, Pontianak',
        'Jl. Cendrawasih No. 17, RT 06/RW 10, Balikpapan',
        'Jl. Rajawali No. 9, RT 01/RW 04, Manado',
        'Jl. Pelanduk No. 28, RT 05/RW 09, Padang',
        'Jl. Kijang No. 11, RT 03/RW 07, Batam',
        'Jl. Rusa No. 23, RT 02/RW 06, Bandar Lampung',
        'Jl. Kucing No. 6, RT 04/RW 08, Jambi',
        'Jl. Anjing No. 19, RT 01/RW 05, Bengkulu'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key constraints
        Schema::disableForeignKeyConstraints();
        
        // Clear existing members
        DB::table('member')->truncate();
        
        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
        
        // Generate 187 fake members with realistic Indonesian data
        for ($i = 1; $i <= 187; $i++) {
            // Randomly choose gender
            $isMale = rand(0, 1) == 1;
            
            // Select first name based on gender
            $firstName = $isMale ? 
                $this->maleNames[array_rand($this->maleNames)] : 
                $this->femaleNames[array_rand($this->femaleNames)];
            
            // Randomly decide whether to include surname (70% chance)
            $fullName = $firstName;
            if (rand(1, 100) <= 70) {
                $surname = $this->surnames[array_rand($this->surnames)];
                $fullName = $firstName . ' ' . $surname;
            }
            
            // Generate member code
            $memberCode = 'M' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            // Select random address
            $address = $this->addresses[array_rand($this->addresses)];
            
            // Generate realistic Indonesian phone numbers
            $mobilePrefixes = ['0812', '0813', '0821', '0822', '0852', '0853', '0811', '0814', '0815', '0816'];
            $prefix = $mobilePrefixes[array_rand($mobilePrefixes)];
            $number = rand(10000000, 99999999);
            $phone = $prefix . '-' . substr($number, 0, 4) . '-' . substr($number, 4, 4);
            
            DB::table('member')->insert([
                'kode_member' => $memberCode,
                'nama' => $fullName,
                'alamat' => $address,
                'telepon' => $phone,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}