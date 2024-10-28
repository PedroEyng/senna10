-- phpMyAdmin SQL Dump Ajustado
-- version 5.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Banco de dados: `pedro`

-- Estrutura da tabela `imagem_produtos`
CREATE TABLE `imagem_produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) DEFAULT NULL,
  `caminho_imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `produto_id` (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados para a tabela `imagem_produtos`
INSERT INTO `imagem_produtos` (`id`, `produto_id`, `caminho_imagem`) VALUES
(2, 3, 'assets/images/produtos/big-04.jpg');

-- Estrutura da tabela `orders`
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pagamento` enum('cartao_credito','pix') NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura da tabela `produtos`
CREATE TABLE `produtos` (
  `produto_id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_nome` text NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `modelo` text NOT NULL,
  PRIMARY KEY (`produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados para a tabela `produtos`
INSERT INTO `produtos` (`produto_id`, `produto_nome`, `descricao`, `preco`, `imagem`, `modelo`) VALUES
(3, 'calça que você usaria', 'Casual', 550.00, 'big-03.jpg', 'jeans');

-- Estrutura da tabela `tamanhos`
CREATE TABLE `tamanhos` (
  `tamanho_id` int(11) NOT NULL AUTO_INCREMENT,
  `tamanho` varchar(4) NOT NULL,
  PRIMARY KEY (`tamanho_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados para a tabela `tamanhos`
INSERT INTO `tamanhos` (`tamanho_id`, `tamanho`) VALUES
(1, 'PP'), (2, 'P'), (3, 'M'), (4, 'G'), (5, 'GG'), (6, 'XXL'), (7, 'XXXL'), 
(8, '34'), (9, '36'), (10, '38'), (11, '40'), (12, '42'), (13, '44'), (14, '46'), 
(15, '48'), (16, '50'), (17, '52'), (18, '54'), (19, '56');

-- Estrutura da tabela `tamanho_produto`
CREATE TABLE `tamanho_produto` (
  `tamanho_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  PRIMARY KEY (`tamanho_id`, `produto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estrutura da tabela `user`
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_nome` text NOT NULL,
  `email` varchar(550) NOT NULL,
  `senha` varchar(550) NOT NULL,
  `admin` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados para a tabela `user`
INSERT INTO `user` (`user_id`, `user_nome`, `email`, `senha`, `admin`) VALUES
(2, 'morozini', 'morozini@gmail', '123', 1),
(3, 'pedro', 'pedro@gmail', '123', 1),
(5, 'Gabriel Vatrin Peres Morozini', 'eee@gmail', '123', NULL),
(6, 'eu mesmo', 'teste@gmail', '123', NULL);

-- Restrições e chaves estrangeiras
ALTER TABLE `imagem_produtos`
  ADD CONSTRAINT `imagem_produtos_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`) ON DELETE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
