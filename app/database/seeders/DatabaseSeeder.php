<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Leasing;
use App\Models\LeasingConstruction;
use App\Models\Owner;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CSVSeeder::class);
    }
}
