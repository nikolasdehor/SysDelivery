<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cupons extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cupons_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cupons_codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'cupons_descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'cupons_tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['percentual', 'valor_fixo'],
                'default'    => 'percentual',
            ],
            'cupons_valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'cupons_valor_minimo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'cupons_data_inicio' => [
                'type' => 'DATE',
            ],
            'cupons_data_fim' => [
                'type' => 'DATE',
            ],
            'cupons_limite_uso' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'cupons_usado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'cupons_ativo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('cupons_id', true);
        $this->forge->addUniqueKey('cupons_codigo');
        $this->forge->addKey('cupons_ativo');
        $this->forge->addKey(['cupons_data_inicio', 'cupons_data_fim']);
        
        $this->forge->createTable('cupons');
    }

    public function down()
    {
        $this->forge->dropTable('cupons');
    }
}
