<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        // Create devices with different health statuses
        Device::factory()->perfectHealth()->count(10)->create();
        Device::factory()->goodHealth()->count(15)->create();
        Device::factory()->fairHealth()->count(8)->create();
        Device::factory()->poorHealth()->count(5)->create();
        Device::factory()->notDeployed()->count(7)->create();

        // Create some random devices
        Device::factory()->count(25)->create();
    }
}
