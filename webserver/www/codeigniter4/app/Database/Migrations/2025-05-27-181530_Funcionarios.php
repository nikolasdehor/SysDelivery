<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Funcionarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'funcionarios_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'funcionarios_usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'funcionarios_cargo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'funcionarios_salario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'funcionarios_data_admissao' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'funcionarios_observacoes' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('funcionarios_id', true);
        $this->forge->addForeignKey('funcionarios_usuario_id', 'usuarios', 'usuarios_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('funcionarios');
    }

    public function down()
    {
        $this->forge->dropTable('funcionarios');
    }
}
