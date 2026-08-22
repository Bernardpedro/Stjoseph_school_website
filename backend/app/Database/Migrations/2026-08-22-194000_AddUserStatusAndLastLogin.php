<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Migration;
use Config\Database;

class AddUserStatusAndLastLogin extends Migration
{
    private function connection(): BaseConnection
    {
        $db = Database::connect();
        if (!$db instanceof BaseConnection) {
            throw new \RuntimeException('Expected a CodeIgniter database connection.');
        }

        return $db;
    }

    public function up()
    {
        $db = $this->connection();
        if (!$db->tableExists('users')) {
            return;
        }

        if (!$db->fieldExists('status', 'users')) {
            $this->forge->addColumn('users', [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'active',
                    'null'       => false,
                    'after'      => 'role',
                ],
            ]);
        }

        if (!$db->fieldExists('last_login', 'users')) {
            $this->forge->addColumn('users', [
                'last_login' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'status',
                ],
            ]);
        }
    }

    public function down()
    {
        $db = $this->connection();
        if ($db->tableExists('users') && $db->fieldExists('last_login', 'users')) {
            $this->forge->dropColumn('users', 'last_login');
        }
        if ($db->tableExists('users') && $db->fieldExists('status', 'users')) {
            $this->forge->dropColumn('users', 'status');
        }
    }
}
