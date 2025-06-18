<?php

namespace App\Controllers;

class SetupController extends BaseController
{
    public function criarTabelaCarrinho()
    {
        $db = \Config\Database::connect();
        
        try {
            // Verificar se a tabela já existe
            if ($db->tableExists('carrinho')) {
                return "Tabela 'carrinho' já existe!";
            }
            
            // SQL para criar a tabela
            $sql = "
                CREATE TABLE `carrinho` (
                    `carrinho_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `carrinho_usuario_id` INT(11) UNSIGNED NOT NULL,
                    `carrinho_produto_id` INT(11) UNSIGNED NOT NULL,
                    `carrinho_quantidade` INT(11) UNSIGNED NOT NULL DEFAULT 1,
                    `carrinho_preco_unitario` DECIMAL(10,2) NOT NULL,
                    `carrinho_data_adicao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`carrinho_id`),
                    UNIQUE KEY `unique_user_product` (`carrinho_usuario_id`, `carrinho_produto_id`),
                    KEY `idx_usuario` (`carrinho_usuario_id`),
                    KEY `idx_produto` (`carrinho_produto_id`),
                    CONSTRAINT `fk_carrinho_usuario` FOREIGN KEY (`carrinho_usuario_id`) REFERENCES `usuarios` (`usuarios_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk_carrinho_produto` FOREIGN KEY (`carrinho_produto_id`) REFERENCES `produtos` (`produtos_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            $db->query($sql);
            
            // Adicionar registro na tabela de migrations
            $migrationSql = "
                INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) 
                VALUES ('2025-06-18-000001', 'App\\\\Database\\\\Migrations\\\\Carrinho', 'default', 'App', " . time() . ", 
                    (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp))
            ";
            
            $db->query($migrationSql);
            
            return "Tabela 'carrinho' criada com sucesso!";
            
        } catch (\Exception $e) {
            return "Erro ao criar tabela: " . $e->getMessage();
        }
    }
    
    public function verificarTabelas()
    {
        $db = \Config\Database::connect();
        
        $tabelas = [
            'usuarios',
            'produtos', 
            'carrinho',
            'pedidos',
            'enderecos'
        ];
        
        $resultado = "<h3>Status das Tabelas:</h3><ul>";
        
        foreach ($tabelas as $tabela) {
            $existe = $db->tableExists($tabela);
            $status = $existe ? "✅ Existe" : "❌ Não existe";
            $resultado .= "<li><strong>{$tabela}:</strong> {$status}</li>";
        }
        
        $resultado .= "</ul>";
        
        if (!$db->tableExists('carrinho')) {
            $resultado .= "<p><a href='" . base_url('setup/criar-tabela-carrinho') . "' class='btn btn-primary'>Criar Tabela Carrinho</a></p>";
        }
        
        return $resultado;
    }
}
