<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateManufacturersTable extends Migration
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
            'oui' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('manufacturers');
    }

    public function down()
    {
        $this->forge->dropTable('manufacturers');
    }
}
