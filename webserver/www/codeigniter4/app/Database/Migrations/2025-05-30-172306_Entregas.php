<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Entregas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'entregas_id' => [
                'type'=> 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pedido_id' => [
                'type'=> 'INT',
                'constraint'=> 11,
                'null'=> true,
                'unsigned' => true,
            ],
            'endereco_id' => [
                'type' => 'INT',
                'constraint'=> 11,
                'null'=> true,
                'unsigned' => true,
            ],
            'funcionario_id' => [
                'type'=> 'INT',
                'constraint' => 11,
                'null'=> true,
                'unsigned' => true,
            ],
            'status_entrega' => [
                'type' => 'ENUM',
                'constraint' => ['A CAMINHO', 'ENTREGUE', 'CANCELADO'],
                'null'=> true,
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
            $this->forge->addKey('entregas_id', true);
            $this->forge->addForeignKey('funcionario_id', 'funcionarios', 'funcionarios_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('endereco_id', 'enderecos', 'enderecos_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('pedido_id', 'pedidos', 'pedidos_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('entregas');
    }

    public function down()
    {
        $this->forge->dropTable('entregas');
    }
}