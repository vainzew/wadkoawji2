<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupplierSeeder extends Seeder
{
    // Indonesian company name prefixes
    private $companyPrefixes = [
        'CV.', 'PT.', 'UD.', 'Toko', 'Supplier', 'Distributor', 'Grosir', 'Pusat'
    ];

    // Indonesian company name suffixes
    private $companySuffixes = [
        'Makmur', 'Jaya', 'Abadi', 'Sentosa', 'Mandiri', 'Bersama', 'Utama', 'Sumber',
        'Rejeki', 'Lancar', 'Sukses', 'Maju', 'Terpercaya', 'Amanah', 'Barokah', 'Rahayu'
    ];

    // Indonesian common addresses
    private $addresses = [
        'Jl. Merdeka No. 123, Jakarta Pusat',
        'Jl. Sudirman Kav. 45, Jakarta Selatan',
        'Jl. Diponegoro No. 67, Bandung',
        'Jl. Ahmad Yani No. 89, Surabaya',
        'Jl. Pahlawan No. 34, Semarang',
        'Jl. Malioboro No. 56, Yogyakarta',
        'Jl. Asia Afrika No. 78, Bandung',
        'Jl. Imam Bonjol No. 90, Medan',
        'Jl. Thamrin No. 23, Jakarta Pusat',
        'Jl. Hayam Wuruk No. 45, Surabaya',
        'Jl. Gajah Mada No. 67, Jakarta Barat',
        'Jl. Raya Bogor Km. 12, Bogor',
        'Jl. Raya Solo No. 89, Yogyakarta',
        'Jl. Raya Jakarta-Bogor No. 34, Depok',
        'Jl. Raya Bekasi No. 56, Bekasi',
        'Jl. Raya Tangerang No. 78, Tangerang',
        'Jl. Raya Serpong No. 90, Tangerang Selatan',
        'Jl. Raya Cibinong No. 23, Bogor',
        'Jl. Raya Cikupa No. 45, Tangerang',
        'Jl. Raya Karawang No. 67, Karawang'
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
        
        // Clear existing suppliers
        DB::table('supplier')->truncate();
        
        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
        
        // Generate 58 fake suppliers with realistic Indonesian data
        for ($i = 1; $i <= 58; $i++) {
            $prefix = $this->companyPrefixes[array_rand($this->companyPrefixes)];
            $suffix = $this->companySuffixes[array_rand($this->companySuffixes)];
            $name = $prefix . ' ' . $suffix . ' ' . $i;
            
            $address = $this->addresses[array_rand($this->addresses)];
            
            // Generate realistic Indonesian phone numbers
            $areaCodes = ['021', '022', '024', '0274', '031', '061', '0812', '0813', '0821', '0822', '0852', '0853'];
            $areaCode = $areaCodes[array_rand($areaCodes)];
            $number = rand(10000000, 99999999);
            
            if (strlen($areaCode) == 3) {
                $phone = $areaCode . '-' . substr($number, 0, 4) . '-' . substr($number, 4, 4);
            } else if (strlen($areaCode) == 4) {
                $phone = $areaCode . '-' . substr($number, 0, 3) . '-' . substr($number, 3, 5);
            } else {
                $phone = $areaCode . substr($number, 0, 4) . substr($number, 4, 4);
            }
            
            DB::table('supplier')->insert([
                'nama' => $name,
                'alamat' => $address,
                'telepon' => $phone,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}