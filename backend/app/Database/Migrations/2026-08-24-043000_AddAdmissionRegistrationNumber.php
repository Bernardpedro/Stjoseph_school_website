<?php

namespace App\Database\Migrations;

use App\Services\RegistrationNumberService;
use CodeIgniter\Database\Migration;

class AddAdmissionRegistrationNumber extends Migration
{
    public function up()
    {
        $this->forge->addColumn('admissions', [
            'registration_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        $used = [];
        $rows = $this->db->table('admissions')->get()->getResultArray();
        foreach ($rows as $row) {
            $year = !empty($row['created_at']) ? (int) date('Y', strtotime((string) $row['created_at'])) : null;
            do {
                $number = RegistrationNumberService::generate($year);
            } while (isset($used[$number]));
            $used[$number] = true;
            $this->db->table('admissions')
                ->where('id', $row['id'])
                ->update(['registration_number' => $number]);
        }

        $this->forge->modifyColumn('admissions', [
            'registration_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
        ]);

        $this->db->query('ALTER TABLE `admissions` ADD UNIQUE KEY `admissions_registration_number_unique` (`registration_number`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `admissions` DROP INDEX `admissions_registration_number_unique`');
        $this->forge->dropColumn('admissions', 'registration_number');
    }
}
