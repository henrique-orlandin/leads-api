<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Logs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'ip' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'uri' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'headers' => [
                'type' => 'JSON',
            ],
            'body' => [
                'type' => 'JSON',
            ],
            'response' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => date('Y-m-d H:i:s'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('logs');
    }

    public function down()
    {
        $this->forge->dropTable('logs');
    }
}
