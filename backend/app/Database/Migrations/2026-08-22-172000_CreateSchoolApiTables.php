<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Migration;
use Config\Database;

class CreateSchoolApiTables extends Migration
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
        if ($db->tableExists('users') && !$db->fieldExists('role', 'users')) {
            $this->forge->addColumn('users', [
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'user',
                    'null'       => false,
                    'after'      => 'roleId',
                ],
            ]);
        }

        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'setting_key'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'setting_value' => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('settings', true);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'description'=> ['type' => 'TEXT', 'null' => true],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'urls'       => ['type' => 'LONGTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('requirement_levels', true);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'item_text'  => ['type' => 'VARCHAR', 'constraint' => 500],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('admission_requirement_items', true);

        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'student_name'    => ['type' => 'VARCHAR', 'constraint' => 200],
            'date_of_birth'   => ['type' => 'DATE', 'null' => true],
            'gender'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'level'           => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'program'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'previous_school' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'parent_name'     => ['type' => 'VARCHAR', 'constraint' => 200],
            'parent_phone'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'parent_email'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'province'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'district'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'address'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'message'         => ['type' => 'TEXT', 'null' => true],
            'documents'       => ['type' => 'LONGTEXT', 'null' => true],
            'status'          => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->createTable('admissions', true);

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT', 'null' => true],
            'partner'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'year'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Ongoing'],
            'category'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'media'       => ['type' => 'LONGTEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('projects', true);

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'date'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'time'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'location'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'type'         => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'organizer'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'youtubeLink'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'images'       => ['type' => 'LONGTEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('events', true);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'message'    => ['type' => 'TEXT', 'null' => true],
            'cta_text'   => ['type' => 'VARCHAR', 'constraint' => 80, 'default' => 'Apply Now'],
            'link'       => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => '/admission'],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('announcements', true);

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'message'    => ['type' => 'TEXT', 'null' => true],
            'link'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_read'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
        $this->forge->dropTable('announcements', true);
        $this->forge->dropTable('events', true);
        $this->forge->dropTable('projects', true);
        $this->forge->dropTable('admissions', true);
        $this->forge->dropTable('admission_requirement_items', true);
        $this->forge->dropTable('requirement_levels', true);
        $this->forge->dropTable('settings', true);

        $db = $this->connection();
        if ($db->tableExists('users') && $db->fieldExists('role', 'users')) {
            $this->forge->dropColumn('users', 'role');
        }
    }
}