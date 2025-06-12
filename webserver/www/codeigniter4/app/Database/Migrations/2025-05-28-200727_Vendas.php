<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Vendas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'vendas_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pedidos_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'data_venda' => [
                'type' => 'DATETIME',
                'default' => false,
            ],
            'forma_pagamento' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'valor_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
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

        $this->forge->addKey('vendas_id', true);
        $this->forge->addForeignKey('pedidos_id', 'pedidos', 'pedidos_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vendas');
    }

    public function down()
    {
        $this->forge->dropTable('vendas');
    }
}
