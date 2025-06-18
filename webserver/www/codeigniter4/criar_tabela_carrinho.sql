-- Script para criar a tabela carrinho no banco SysDelivery
-- Execute este SQL no phpMyAdmin

CREATE TABLE `carrinho` (
  `carrinho_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `carrinho_usuario_id` int(11) UNSIGNED NOT NULL,
  `carrinho_produto_id` int(11) UNSIGNED NOT NULL,
  `carrinho_quantidade` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `carrinho_preco_unitario` decimal(10,2) NOT NULL,
  `carrinho_data_adicao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`carrinho_id`),
  UNIQUE KEY `unique_user_product` (`carrinho_usuario_id`,`carrinho_produto_id`),
  KEY `idx_usuario` (`carrinho_usuario_id`),
  KEY `idx_produto` (`carrinho_produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adicionar as chaves estrangeiras
ALTER TABLE `carrinho`
  ADD CONSTRAINT `fk_carrinho_usuario` FOREIGN KEY (`carrinho_usuario_id`) REFERENCES `usuarios` (`usuarios_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carrinho_produto` FOREIGN KEY (`carrinho_produto_id`) REFERENCES `produtos` (`produtos_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Inserir registro na tabela migrations para controle
INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) 
VALUES ('2025-06-18-000001', 'App\\Database\\Migrations\\Carrinho', 'default', 'App', UNIX_TIMESTAMP(), 
    (SELECT COALESCE(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp));
