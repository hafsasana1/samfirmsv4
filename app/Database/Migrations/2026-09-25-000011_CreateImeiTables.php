<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateImeiTables extends Migration
{
    public function up()
    {
        // Table: fw_imei_checks - Logs all IMEI check requests
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'imei' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('imei');
        $this->forge->addKey('created_time');
        $this->forge->addKey('ip_address');
        
        $this->forge->createTable('fw_imei_checks', true);

        // Table: fw_imei_info - Stores detailed device information from API
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'orderId' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'price' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'imei' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'modelInfo' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'serial' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'modelDesc' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'modelName' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'modelNumber' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'warrantyStatus' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'estWarrantyEnd' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'productionLocation' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'productionDate' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'country' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'carrier' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'result' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'visitorIP' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'createdTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updatedTime' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('imei');
        $this->forge->addKey('createdTime');
        
        $this->forge->createTable('fw_imei_info', true);
    }

    public function down()
    {
        $this->forge->dropTable('fw_imei_checks', true);
        $this->forge->dropTable('fw_imei_info', true);
    }
}
