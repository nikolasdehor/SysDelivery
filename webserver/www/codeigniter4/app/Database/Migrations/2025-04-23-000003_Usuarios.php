<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Usuarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'usuarios_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'usuarios_nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'usuarios_sobrenome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'usuarios_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'unique'     => true,
            ],
            'usuarios_cpf' => [
                'type'       => 'CHAR',
                'constraint' => 14, // com pontos e traço: 000.000.000-00
                'unique'     => true,
            ],
            'usuarios_nivel' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0, // Ex: 0 = comum, 1 = admin
            ],
            'usuarios_fone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'usuarios_senha' => [
                'type'       => 'VARCHAR',
                'constraint' => 255, // para suportar hash bcrypt
            ],
            'usuarios_data_nasc' => [
                'type'       => 'DATE',
            ],
            'usuarios_data_cadastro' => [
                'type'       => 'DATETIME',
                'null'       => true,
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

        $this->forge->addKey('usuarios_id', true); // PRIMARY KEY
        $this->forge->createTable('usuarios');
    }

    public function down()
    {
        $this->forge->dropTable('usuarios');
    }
}
