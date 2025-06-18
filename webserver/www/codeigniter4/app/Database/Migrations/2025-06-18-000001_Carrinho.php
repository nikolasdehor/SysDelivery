<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Carrinho extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'carrinho_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'carrinho_usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'carrinho_produto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'carrinho_quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'carrinho_preco_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'carrinho_data_adicao' => [
                'type'    => 'TIMESTAMP',
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('carrinho_id', true);
        $this->forge->addForeignKey('carrinho_usuario_id', 'usuarios', 'usuarios_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('carrinho_produto_id', 'produtos', 'produtos_id', 'CASCADE', 'CASCADE');
        
        // Índice único para evitar duplicatas de produto por usuário
        $this->forge->addUniqueKey(['carrinho_usuario_id', 'carrinho_produto_id']);
        
        $this->forge->createTable('carrinho');
    }

    public function down()
    {
        $this->forge->dropTable('carrinho');
    }
}
