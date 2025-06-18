-- Criação da tabela carrinho
CREATE TABLE IF NOT EXISTS `carrinho` (
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

-- Inserir registro na tabela de migrations para controle
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) 
VALUES ('2025-06-18-000001', 'App\\Database\\Migrations\\Carrinho', 'default', 'App', UNIX_TIMESTAMP(), 
    (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp));
