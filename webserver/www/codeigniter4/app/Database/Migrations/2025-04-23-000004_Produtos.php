<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Produtos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'produtos_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'produtos_nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'produtos_descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'produtos_preco_custo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'produtos_preco_venda' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'produtos_categorias_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('produtos_id', true);
        $this->forge->addForeignKey(
            'produtos_categorias_id',
            'categorias',
            'categorias_id',
            'CASCADE',  // ON UPDATE
            'CASCADE'  // ON DELETE
        );

        $this->forge->createTable('produtos');
    }

    public function down()
    {
        $this->forge->dropTable('produtos');
    }
}