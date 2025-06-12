-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Tempo de geração: 16/04/2025 às 21:29
-- Versão do servidor: 8.0.41
-- Versão do PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projeto`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `categorias_id` int NOT NULL,
  `categorias_nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`categorias_id`, `categorias_nome`) VALUES
(1, 'Sanduíches2'),
(2, 'Pizzas');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cidades`
--

CREATE TABLE `cidades` (
  `cidades_id` int NOT NULL,
  `cidades_nome` varchar(255) NOT NULL,
  `cidades_uf` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `cidades`
--

INSERT INTO `cidades` (`cidades_id`, `cidades_nome`, `cidades_uf`) VALUES
(1, 'Ceres', 'GO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `imgprodutos`
--

CREATE TABLE `imgprodutos` (
  `imgprodutos_id` int NOT NULL,
  `imgprodutos_link` varchar(255) NOT NULL,
  `imgprodutos_descricao` text NOT NULL,
  `imgprodutos_produtos_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `imgprodutos`
--

INSERT INTO `imgprodutos` (`imgprodutos_id`, `imgprodutos_link`, `imgprodutos_descricao`, `imgprodutos_produtos_id`) VALUES
(1, 'uploads/20250416/1744801962_9165428592f42702f939.jpg', 'Pizza1', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `produtos_id` int NOT NULL,
  `produtos_nome` varchar(255) NOT NULL,
  `produtos_descricao` text NOT NULL,
  `produtos_preco_custo` float(9,2) NOT NULL,
  `produtos_preco_venda` float(9,2) NOT NULL,
  `produtos_categorias_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`produtos_id`, `produtos_nome`, `produtos_descricao`, `produtos_preco_custo`, `produtos_preco_venda`, `produtos_categorias_id`) VALUES
(1, 'Pizza Calabresa', 'Pizza Calabresa', 35.00, 60.00, 2),
(2, 'X-Tudo', 'X-Tudo', 15.50, 24.99, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `usuarios_id` int NOT NULL,
  `usuarios_nome` varchar(255) NOT NULL,
  `usuarios_sobrenome` varchar(255) NOT NULL,
  `usuarios_email` varchar(255) NOT NULL,
  `usuarios_cpf` varchar(14) NOT NULL,
  `usuarios_data_nasc` date NOT NULL,
  `usuarios_nivel` int NOT NULL,
  `usuarios_fone` varchar(15) NOT NULL,
  `usuarios_senha` varchar(32) NOT NULL,
  `usuarios_data_cadastro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`usuarios_id`, `usuarios_nome`, `usuarios_sobrenome`, `usuarios_email`, `usuarios_cpf`, `usuarios_data_nasc`, `usuarios_nivel`, `usuarios_fone`, `usuarios_senha`, `usuarios_data_cadastro`) VALUES
(1, 'Vilson', 'Soares de Siqueira', 'vilsonsoares@gmail.com', '999.999.999-99', '1981-12-03', 1, '6398474-3380', 'e10adc3949ba59abbe56e057f20f883e', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela enderecos
--

CREATE TABLE `enderecos` (
  `enderecos_id` int NOT NULL,
  `enderecos_rua` VARCHAR(255) NOT NULL,
  `enderecos_numero` VARCHAR(10) NOT NULL,
  `enderecos_complemento` VARCHAR(255),
  `enderecos_status` TINYINT NOT NULL,
  `enderecos_cidade_id` INT NOT NULL,
  `enderecos_usuario_id` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `carrinho_id` int NOT NULL,
  `carrinho_usuario_id` int NOT NULL,
  `carrinho_produto_id` int NOT NULL,
  `carrinho_quantidade` int NOT NULL DEFAULT 1,
  `carrinho_preco_unitario` decimal(10,2) NOT NULL,
  `carrinho_data_adicao` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `avaliacoes_id` int NOT NULL,
  `avaliacoes_produto_id` int NOT NULL,
  `avaliacoes_usuario_id` int NOT NULL,
  `avaliacoes_nota` tinyint NOT NULL CHECK (avaliacoes_nota >= 1 AND avaliacoes_nota <= 5),
  `avaliacoes_comentario` text,
  `avaliacoes_data` timestamp DEFAULT CURRENT_TIMESTAMP,
  `avaliacoes_status` tinyint DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `cupons_id` int NOT NULL,
  `cupons_codigo` varchar(50) NOT NULL,
  `cupons_descricao` varchar(255) NOT NULL,
  `cupons_tipo` enum('percentual','valor_fixo') NOT NULL,
  `cupons_valor` decimal(10,2) NOT NULL,
  `cupons_valor_minimo` decimal(10,2) DEFAULT 0,
  `cupons_data_inicio` date NOT NULL,
  `cupons_data_fim` date NOT NULL,
  `cupons_limite_uso` int DEFAULT NULL,
  `cupons_usado` int DEFAULT 0,
  `cupons_ativo` tinyint DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `notificacoes_id` int NOT NULL,
  `notificacoes_usuario_id` int NOT NULL,
  `notificacoes_titulo` varchar(255) NOT NULL,
  `notificacoes_mensagem` text NOT NULL,
  `notificacoes_tipo` enum('info','success','warning','danger') DEFAULT 'info',
  `notificacoes_lida` tinyint DEFAULT 0,
  `notificacoes_data` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `enderecos`
--

INSERT INTO `enderecos` (`enderecos_id`, `enderecos_rua`, `enderecos_numero`, `enderecos_complemento`, `enderecos_status`, `enderecos_cidade_id`, `enderecos_usuario_id`) VALUES
(1, 'Avenida Brasil', 'S/N', 'Qd. 40 LT. 10A', 1, 1, 1);

--
-- Despejando dados para a tabela `cupons`
--

INSERT INTO `cupons` (`cupons_id`, `cupons_codigo`, `cupons_descricao`, `cupons_tipo`, `cupons_valor`, `cupons_valor_minimo`, `cupons_data_inicio`, `cupons_data_fim`, `cupons_limite_uso`, `cupons_usado`, `cupons_ativo`) VALUES
(1, 'BEMVINDO10', 'Desconto de boas-vindas', 'percentual', 10.00, 20.00, '2025-01-01', '2025-12-31', 100, 0, 1),
(2, 'FRETE5', 'Desconto no frete', 'valor_fixo', 5.00, 15.00, '2025-01-01', '2025-06-30', NULL, 0, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`categorias_id`);

--
-- Índices de tabela `cidades`
--
ALTER TABLE `cidades`
  ADD PRIMARY KEY (`cidades_id`);

--
-- Índices de tabela `imgprodutos`
--
ALTER TABLE `imgprodutos`
  ADD PRIMARY KEY (`imgprodutos_id`),
  ADD KEY `fk_imagens_produtos` (`imgprodutos_produtos_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`produtos_id`),
  ADD KEY `fk_categorias_produto` (`produtos_categorias_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuarios_id`);
  
--
-- Índices de tabela `enderecos`
--
ALTER TABLE `enderecos`
  ADD PRIMARY KEY (`enderecos_id`),
  ADD KEY `fk_enderecos_cidade` (`enderecos_cidade_id`),
  ADD KEY `fk_enderecos_usuario` (`enderecos_usuario_id`);

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`carrinho_id`),
  ADD KEY `fk_carrinho_usuario` (`carrinho_usuario_id`),
  ADD KEY `fk_carrinho_produto` (`carrinho_produto_id`);

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`avaliacoes_id`),
  ADD KEY `fk_avaliacoes_produto` (`avaliacoes_produto_id`),
  ADD KEY `fk_avaliacoes_usuario` (`avaliacoes_usuario_id`),
  ADD UNIQUE KEY `unique_avaliacao_usuario_produto` (`avaliacoes_produto_id`, `avaliacoes_usuario_id`);

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`cupons_id`),
  ADD UNIQUE KEY `cupons_codigo` (`cupons_codigo`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`notificacoes_id`),
  ADD KEY `fk_notificacoes_usuario` (`notificacoes_usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `categorias_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `cidades`
--
ALTER TABLE `cidades`
  MODIFY `cidades_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `imgprodutos`
--
ALTER TABLE `imgprodutos`
  MODIFY `imgprodutos_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `produtos_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuarios_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
  
--
-- AUTO_INCREMENT de tabela `enderecos`
--
ALTER TABLE `enderecos`
	MODIFY `enderecos_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT =2;

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `carrinho_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `avaliacoes_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `cupons_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `notificacoes_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `imgprodutos`
--
ALTER TABLE `imgprodutos`
  ADD CONSTRAINT `fk_imagens_produtos` FOREIGN KEY (`imgprodutos_produtos_id`) REFERENCES `produtos` (`produtos_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_categorias_produto` FOREIGN KEY (`produtos_categorias_id`) REFERENCES `categorias` (`categorias_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `enderecos`
--
ALTER TABLE `enderecos`
  ADD CONSTRAINT `fk_enderecos_cidade` FOREIGN KEY (`enderecos_cidade_id`) REFERENCES `cidades` (`cidades_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_enderecos_usuario` FOREIGN KEY (`enderecos_usuario_id`) REFERENCES `usuarios` (`usuarios_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `fk_carrinho_usuario` FOREIGN KEY (`carrinho_usuario_id`) REFERENCES `usuarios` (`usuarios_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_carrinho_produto` FOREIGN KEY (`carrinho_produto_id`) REFERENCES `produtos` (`produtos_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `fk_avaliacoes_produto` FOREIGN KEY (`avaliacoes_produto_id`) REFERENCES `produtos` (`produtos_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_avaliacoes_usuario` FOREIGN KEY (`avaliacoes_usuario_id`) REFERENCES `usuarios` (`usuarios_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `fk_notificacoes_usuario` FOREIGN KEY (`notificacoes_usuario_id`) REFERENCES `usuarios` (`usuarios_id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
