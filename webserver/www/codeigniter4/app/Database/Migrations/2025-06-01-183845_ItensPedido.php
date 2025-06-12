<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ItensPedido extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'itens_pedido_id' => [
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
            'produtos_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'preco_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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

        $this->forge->addKey('itens_pedido_id', true);
        $this->forge->addForeignKey('pedidos_id', 'pedidos', 'pedidos_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produtos_id', 'produtos', 'produtos_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('itens_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('itens_pedido', true);
    }
}
