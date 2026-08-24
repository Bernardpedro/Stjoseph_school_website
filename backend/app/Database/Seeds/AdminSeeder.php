<?php

namespace App\Database\Seeds;

use App\Services\UserService;
use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        (new UserService())->upsertOwner();
    }
}
