<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetectionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mac_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 17,
            ],
            'manufacturer_id' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'rssi' => [
                'type'       => 'INTEGER',
                'constraint' => 11,
            ],
            'sensor_location' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'detected_at' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('manufacturer_id', 'manufacturers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('detections');
    }

    public function down()
    {
        $this->forge->dropTable('detections');
    }
}
