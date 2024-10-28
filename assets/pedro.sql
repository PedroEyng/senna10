-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/10/2024 às 20:07
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
(8, 9, 'assets/images/produtos/4955e9d19210bf584af05c73ee312c379e884cffcdaf81d0b8fcf62f2c5fd1de_1.jpg'),
(9, 10, 'assets/images/produtos/WhatsApp Image 2024-10-24 at 19.47.23 (1).jpeg'),
(10, 10, 'assets/images/produtos/big-01.jpg'),
(11, 10, 'assets/images/produtos/item-03.jpg'),
(12, 10, 'assets/images/produtos/item-05.jpg');

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
(2, 9, 6, 999.00, 1, 'M', 'teste@gmail', '0', '2024-10-28 02:32:24', 'pix');

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
(9, 'calça', 'jeans', 999.00, 'top demais'),
(10, 'teste', 'teste', 999.00, 'teste');

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
(1, 10),
(2, 4),
(2, 9),
(2, 10),
(3, 4),
(3, 6),
(3, 9),
(3, 10),
(4, 4),
(4, 6),
(4, 9),
(4, 10),
(5, 9),
(6, 9),
(7, 9),
(8, 5),
(9, 5),
(10, 5),
(11, 5),
(12, 5),
(13, 5),
(14, 5),
(15, 5),
(16, 5),
(17, 5),
(18, 5),
(19, 5);

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
(5, 'Gabriel Vatrin Peres Morozini', 'eee@gmail', '123', NULL),
(6, 'eu mesmo', 'teste@gmail', '123', NULL);

--
-- Índices para tabelas despejadas
--

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
-- AUTO_INCREMENT de tabela `imagem_produtos`
--
ALTER TABLE `imagem_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `produto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `tamanhos`
--
ALTER TABLE `tamanhos`
  MODIFY `tamanho_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

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
