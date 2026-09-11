<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAchievementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'category'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'academic_year' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'score'         => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'rank'          => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'is_published'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'is_featured'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('achievements', true);
    }

    public function down()
    {
        $this->forge->dropTable('achievements', true);
    }
}
