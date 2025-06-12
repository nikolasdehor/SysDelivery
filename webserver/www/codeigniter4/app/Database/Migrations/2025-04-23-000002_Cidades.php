<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cidades extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cidades_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cidades_nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'cidades_uf' => [
                'type'       => 'CHAR',
                'constraint' => 2,
            ],
            
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('cidades_id', true); // PRIMARY KEY
        $this->forge->createTable('cidades');
    }

    public function down()
    {
        $this->forge->dropTable('cidades');
    }
}
