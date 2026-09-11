<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmsMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'recipient'           => ['type' => 'VARCHAR', 'constraint' => 30],
            'sender_id'           => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'message'             => ['type' => 'TEXT'],
            'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'provider_message_id' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'provider_response'   => ['type' => 'TEXT', 'null' => true],
            'error_message'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sent_by'             => ['type' => 'VARCHAR', 'constraint' => 36, 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->createTable('sms_messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('sms_messages', true);
    }
}
