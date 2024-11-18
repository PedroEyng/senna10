-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/11/2024 às 18:55
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pedro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `carrinho`
--

CREATE TABLE `carrinho` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagem_produtos`
--

CREATE TABLE `imagem_produtos` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `caminho_imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `imagem_produtos`
--

INSERT INTO `imagem_produtos` (`id`, `produto_id`, `caminho_imagem`) VALUES
(9, 10, 'assets/images/produtos/WhatsApp Image 2024-10-24 at 19.47.23 (1).jpeg'),
(10, 10, 'assets/images/produtos/big-01.jpg'),
(11, 10, 'assets/images/produtos/item-03.jpg'),
(12, 10, 'assets/images/produtos/item-05.jpg'),
(21, 16, 'uploads/6732b39498043_big-02.jpg'),
(22, 16, 'uploads/6732b394984c2_big-03.jpg'),
(23, 17, 'uploads/6733c15230ff2_product-05.jpg'),
(24, 17, 'uploads/6733c1523177d_product-06.jpg'),
(25, 18, 'uploads/6733e1c37b044_item-03.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `tamanho` text NOT NULL,
  `email` text NOT NULL,
  `endereco` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pagamento` enum('cartao_credito','pix') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`order_id`, `produto_id`, `user_id`, `total`, `quantidade`, `tamanho`, `email`, `endereco`, `created_at`, `pagamento`) VALUES
(1, 9, 6, 999.00, 1, 'PP', 'teste@gmail', 'Paraná,Guarapuava,09405', '2024-10-28 02:10:31', 'cartao_credito'),
(2, 9, 6, 999.00, 1, 'M', 'teste@gmail', '0', '2024-10-28 02:32:24', 'pix'),
(3, 10, 2, 9990.00, 10, 'M', 'morozini@gmail', '0', '2024-10-29 20:18:05', 'cartao_credito'),
(4, 10, 6, 999.00, 1, 'P', 'teste@gmail', '123', '2024-11-08 13:46:18', 'pix'),
(5, 10, 6, 999.00, 1, 'P', 'teste@gmail', '123', '2024-11-08 13:46:56', 'cartao_credito'),
(6, 16, 7, 123.00, 1, 'P', 'LEANDRO@LEANDRO', '123', '2024-11-08 14:02:50', 'pix'),
(7, 10, 7, 999.00, 1, 'P', 'leandro@leandro', '0', '2024-11-11 00:00:20', 'cartao_credito'),
(8, 10, 7, 9990.00, 10, 'M', 'leandro@leandro', '0', '2024-11-11 00:02:34', 'cartao_credito');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `produto_id` int(11) NOT NULL,
  `produto_nome` text NOT NULL,
  `descricao` text NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `modelo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`produto_id`, `produto_nome`, `descricao`, `preco`, `modelo`) VALUES
(10, 'Tênis Senna', 'Tênis leve para esportes', 999.00, 'Esporte'),
(16, 'calça que você usaria', 'Calça Jeans Casual', 123.00, 'calça'),
(17, 'bolça', 'Bolça de couro especial para eventos especiais', 650.00, 'Bolça de couro'),
(18, 'Colete', 'Colete acolchoado para uso casual', 340.00, 'De algodão');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tamanhos`
--

CREATE TABLE `tamanhos` (
  `tamanho_id` int(11) NOT NULL,
  `tamanho` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tamanhos`
--

INSERT INTO `tamanhos` (`tamanho_id`, `tamanho`) VALUES
(1, 'PP'),
(2, 'P'),
(3, 'M'),
(4, 'G'),
(5, 'GG'),
(6, 'XXL'),
(7, 'XXXL'),
(8, '34'),
(9, '36'),
(10, '38'),
(11, '40'),
(12, '42'),
(13, '44'),
(14, '46'),
(15, '48'),
(16, '50'),
(17, '52'),
(18, '54'),
(19, '56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tamanho_produto`
--

CREATE TABLE `tamanho_produto` (
  `tamanho_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tamanho_produto`
--

INSERT INTO `tamanho_produto` (`tamanho_id`, `produto_id`) VALUES
(1, 4),
(1, 9),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 18),
(2, 4),
(2, 9),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 18),
(3, 4),
(3, 6),
(3, 9),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 18),
(4, 4),
(4, 6),
(4, 9),
(4, 11),
(4, 12),
(4, 13),
(4, 14),
(4, 15),
(4, 16),
(4, 18),
(5, 9),
(5, 11),
(5, 12),
(5, 15),
(5, 18),
(6, 9),
(6, 11),
(6, 12),
(6, 15),
(6, 18),
(7, 9),
(7, 11),
(7, 12),
(7, 18),
(8, 5),
(8, 10),
(8, 17),
(9, 5),
(9, 10),
(9, 17),
(10, 5),
(10, 10),
(10, 17),
(11, 5),
(11, 10),
(11, 17),
(12, 5),
(12, 10),
(12, 17),
(13, 5),
(13, 10),
(13, 17),
(14, 5),
(14, 10),
(14, 17),
(15, 5),
(15, 10),
(15, 17),
(16, 5),
(16, 10),
(16, 17),
(17, 5),
(17, 10),
(17, 17),
(18, 5),
(18, 10),
(18, 17),
(19, 5),
(19, 10),
(19, 17);

-- --------------------------------------------------------

--
-- Estrutura para tabela `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_nome` text NOT NULL,
  `email` varchar(550) NOT NULL,
  `senha` varchar(550) NOT NULL,
  `admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `user`
--

INSERT INTO `user` (`user_id`, `user_nome`, `email`, `senha`, `admin`) VALUES
(2, 'morozini', 'morozini@gmail', '123', 1),
(3, 'pedro', 'pedro@gmail', '123', 1),
(6, 'teste', 'teste@gmail', '123', NULL),
(7, 'leandro', 'leandro@leandro', '123', NULL),
(8, 'um cara', 'a@a', '12', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `carrinho`
--
ALTER TABLE `carrinho`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `carrinho_ibfk_1` (`produto_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Índices de tabela `imagem_produtos`
--
ALTER TABLE `imagem_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`produto_id`);

--
-- Índices de tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  ADD PRIMARY KEY (`tamanho_id`);

--
-- Índices de tabela `tamanho_produto`
--
ALTER TABLE `tamanho_produto`
  ADD PRIMARY KEY (`tamanho_id`,`produto_id`);

--
-- Índices de tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `carrinho`
--
ALTER TABLE `carrinho`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- AUTO_INCREMENT de tabela `imagem_produtos`
--
ALTER TABLE `imagem_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `produto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  MODIFY `tamanho_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `carrinho`
--
ALTER TABLE `carrinho`
  ADD CONSTRAINT `carrinho_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `imagem_produtos`
--
ALTER TABLE `imagem_produtos`
  ADD CONSTRAINT `imagem_produtos_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`produto_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
