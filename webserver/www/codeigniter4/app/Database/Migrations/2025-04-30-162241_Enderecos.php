<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Enderecos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'enderecos_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'enderecos_rua' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'enderecos_numero' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'enderecos_complemento' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'enderecos_status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1, // 1 = Ativo
                'null' => false,
            ],
            'enderecos_cidade_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'enderecos_usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addKey('enderecos_id', true);
        $this->forge->addForeignKey('enderecos_cidade_id', 'cidades', 'cidades_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('enderecos_usuario_id', 'usuarios', 'usuarios_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('enderecos');
    }

    public function down()
    {
        $this->forge->dropTable('enderecos');
    }
}