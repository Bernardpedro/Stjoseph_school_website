<?php

namespace App\Database\Seeds;

use App\Services\UserService;
use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $email = 'admin@stjosephtssnzuki.com';
        $existing = $this->db->table('users')->where('email', $email)->get()->getRowArray();

        if ($existing) {
            $this->db->table('users')->where('email', $email)->update(['role' => 'admin']);
            return;
        }

        (new UserService())->createUser([
            'firstName' => 'School',
            'lastName'  => 'Admin',
            'email'     => $email,
            'phone'     => '0780000000',
            'password'  => 'Admin@Nzuki2026',
            'role'      => 'admin',
        ]);
    }
}
