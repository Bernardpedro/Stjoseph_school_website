<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTestimonialsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'role'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'message'    => ['type' => 'TEXT', 'null' => true],
            'photo'      => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'rating'     => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'null' => true],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('testimonials', true);
    }

    public function down()
    {
        $this->forge->dropTable('testimonials', true);
    }
}
