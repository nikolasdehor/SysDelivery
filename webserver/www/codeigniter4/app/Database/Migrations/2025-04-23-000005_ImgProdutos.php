<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ImgProdutos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'imgprodutos_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'imgprodutos_link' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'imgprodutos_descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'imgprodutos_produtos_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('imgprodutos_id', true); // PRIMARY KEY

        // Chave estrangeira para produtos
        $this->forge->addForeignKey(
            'imgprodutos_produtos_id',
            'produtos',
            'produtos_id',
            'CASCADE',   // ON UPDATE
            'CASCADE'    // ON DELETE → apaga as imagens se o produto for deletado
        );

        $this->forge->createTable('imgprodutos');
    }

    public function down()
    {
        $this->forge->dropTable('imgprodutos');
    }
}
