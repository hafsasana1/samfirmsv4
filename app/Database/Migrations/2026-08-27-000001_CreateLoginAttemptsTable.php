<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginAttemptsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'attemptId' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ipAddress' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'attemptTime' => [
                'type' => 'DATETIME',
            ],
            'success' => [
                'type'       => 'ENUM',
                'constraint' => ['Yes', 'No'],
                'default'    => 'No',
            ],
        ]);
        
        $this->forge->addKey('attemptId', true);
        $this->forge->addKey('ipAddress');
        $this->forge->addKey('attemptTime');
        $this->forge->createTable('fw_login_attempts');
    }

    public function down()
    {
        $this->forge->dropTable('fw_login_attempts');
    }
}
