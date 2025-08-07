-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/08/2025 às 00:04
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
-- Banco de dados: `kivitz`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `dieta`
--

CREATE TABLE `dieta` (
  `Id` int(11) NOT NULL,
  `Lote` varchar(20) NOT NULL,
  `Vacas` int(11) NOT NULL,
  `Silagem` int(11) NOT NULL,
  `Milho` decimal(10,2) NOT NULL,
  `Soja` decimal(10,2) NOT NULL,
  `Casca` decimal(10,2) NOT NULL,
  `Polpa` decimal(10,2) NOT NULL,
  `Caroco` decimal(10,0) NOT NULL,
  `Feno` decimal(10,2) NOT NULL,
  `Mineral` decimal(10,2) NOT NULL,
  `Equalizer` decimal(10,2) NOT NULL,
  `Notox` decimal(10,2) NOT NULL,
  `Ureia` decimal(10,2) NOT NULL,
  `Ice` decimal(10,2) NOT NULL,
  `Ativo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `dieta`
--

INSERT INTO `dieta` (`Id`, `Lote`, `Vacas`, `Silagem`, `Milho`, `Soja`, `Casca`, `Polpa`, `Caroco`, `Feno`, `Mineral`, `Equalizer`, `Notox`, `Ureia`, `Ice`, `Ativo`) VALUES
(1, 'Lactantes', 30, 35, 6.00, 5.50, 1.00, 2.00, 0, 1.30, 0.54, 0.12, 0.02, 0.14, 0.00, 1),
(2, 'Campo', 0, 18, 0.00, 1.00, 0.00, 0.00, 0, 0.50, 0.10, 0.00, 0.01, 0.05, 0.00, 1),
(3, 'Pré-Parto', 1, 15, 0.00, 2.00, 2.00, 0.00, 0, 2.00, 0.40, 0.00, 0.00, 0.00, 0.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `envios_leite`
--

CREATE TABLE `envios_leite` (
  `id` int(11) NOT NULL,
  `data_envio` date NOT NULL,
  `litros_enviados` int(11) NOT NULL,
  `numero_vacas` int(11) NOT NULL,
  `leite_bezerros` int(11) NOT NULL DEFAULT 0,
  `observacoes` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `envios_leite`
--

INSERT INTO `envios_leite` (`id`, `data_envio`, `litros_enviados`, `numero_vacas`, `leite_bezerros`, `observacoes`, `data_cadastro`, `ativo`) VALUES
(4, '2025-07-02', 1590, 25, 24, NULL, '2025-07-30 18:40:48', 1),
(5, '2025-07-04', 1720, 26, 24, NULL, '2025-07-30 18:40:48', 1),
(6, '2025-07-06', 1820, 26, 24, NULL, '2025-07-30 18:40:48', 1),
(7, '2025-07-08', 1720, 26, 24, NULL, '2025-07-30 18:40:48', 1),
(8, '2025-07-10', 1665, 26, 24, NULL, '2025-07-30 18:40:48', 1),
(9, '2025-07-12', 1640, 26, 24, NULL, '2025-07-30 18:40:48', 1),
(10, '2025-07-14', 1760, 27, 24, NULL, '2025-07-30 18:40:48', 1),
(11, '2025-07-16', 1843, 27, 24, NULL, '2025-07-30 18:40:48', 1),
(12, '2025-07-18', 1895, 28, 24, NULL, '2025-07-30 18:40:48', 1),
(13, '2025-07-20', 1873, 28, 24, NULL, '2025-07-30 18:40:48', 1),
(14, '2025-07-22', 1830, 28, 24, NULL, '2025-07-30 18:40:48', 1),
(15, '2025-07-24', 1848, 28, 24, NULL, '2025-07-30 18:40:48', 1),
(16, '2025-07-26', 1870, 29, 24, NULL, '2025-07-30 18:40:48', 1),
(17, '2025-07-28', 1935, 29, 24, NULL, '2025-07-30 18:40:48', 1),
(18, '2025-07-30', 1995, 29, 24, NULL, '2025-07-30 18:40:48', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `Id` int(10) NOT NULL,
  `Produto` varchar(50) NOT NULL,
  `unidade_compra` varchar(50) DEFAULT 'kg' COMMENT 'Unidade na qual o produto é comprado (ex: Saco, Fardo, Galão)',
  `unidade_consumo` varchar(50) DEFAULT 'kg' COMMENT 'Unidade na qual o estoque é controlado e consumido (ex: kg, ml, Unidade)',
  `fator_conversao` decimal(10,4) NOT NULL DEFAULT 1.0000 COMMENT 'Fator para converter da unidade de compra para a de consumo',
  `valor_compra` decimal(10,2) DEFAULT NULL COMMENT 'Valor por unidade de compra (ex: por saco)',
  `categoria` varchar(100) DEFAULT 'Alimentação',
  `id_categoria_financeira` int(11) DEFAULT NULL,
  `Quantidade` int(11) NOT NULL,
  `Consumo_dia` int(11) NOT NULL,
  `Valor` varchar(11) NOT NULL,
  `ativo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`Id`, `Produto`, `unidade_compra`, `unidade_consumo`, `fator_conversao`, `valor_compra`, `categoria`, `id_categoria_financeira`, `Quantidade`, `Consumo_dia`, `Valor`, `ativo`) VALUES
(1, 'Milho', 'kg', 'kg', 1.0000, 1.85, 'Alimentação', 8, 219, 30, '1.85', 1),
(2, 'Soja', 'kg', 'kg', 1.0000, 2.19, 'Alimentação', 8, 2574, 60, '2.19', 1),
(3, 'Feno', 'Fardo', 'kg', 14.0000, 18.48, 'Alimentação', 9, 3941, 20, '1.32', 1),
(4, 'Casca', 'kg', 'kg', 1.0000, 1.35, 'Alimentação', 8, 1743, 0, '1.35', 1),
(5, 'Polpa', 'kg', 'kg', 1.0000, 1.95, 'Alimentação', 8, 996, 0, '1.95', 1),
(6, 'Ice', 'saco', 'kg', 25.0000, 25.00, 'Alimentação', 10, 0, 0, '1', 1),
(7, 'Mineral', 'saco', 'kg', 20.0000, 158.80, 'Alimentação', 10, 334, 3, '7.94', 1),
(8, 'Equalizer', 'saco', 'kg', 20.0000, 110.40, 'Alimentação', 10, 140, 0, '5.52', 1),
(9, 'Notox', 'saco', 'kg', 25.0000, 789.75, 'Alimentação', 10, 23, 0, '31.59', 1),
(10, 'Ureia', 'saco', 'kg', 50.0000, 145.00, 'Alimentação', 10, 144, 1, '2.90', 1),
(11, 'Silagem', 'kg', 'kg', 1.0000, 0.13, 'Alimentação', 9, 722201, 360, '0.13', 1),
(12, 'Caroco', 'kg', 'kg', 1.0000, 0.00, 'Alimentação', 8, 0, 1, '', 1),
(14, 'teste', 'saco', 'kg', 20.0000, 10.00, 'Alimentação', 10, 100, 0, '0.5', 1),
(15, 'Sêmen', 'dose', 'dose', 1.0000, 40.00, 'Alimentação', NULL, -5, 0, '40', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `data_evento` date NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo_evento` enum('Saúde','Vacina','Parto','Cio','Geral') NOT NULL DEFAULT 'Geral',
  `id_vaca` int(11) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_registro_manejo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `titulo`, `data_evento`, `descricao`, `tipo_evento`, `id_vaca`, `ativo`, `created_at`, `updated_at`, `id_registro_manejo`) VALUES
(1, 'Previsão de Parto (071)', '2025-08-21', '', 'Parto', 6, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 1),
(2, 'Previsão de Secagem (071)', '2025-06-22', '', 'Geral', 6, 0, '2025-06-17 14:40:32', '2025-07-06 11:03:02', 1),
(3, 'Protocolo Pré-parto (071)', '2025-07-31', '', 'Saúde', 6, 0, '2025-06-17 14:40:32', '2025-08-03 13:16:24', 1),
(4, 'Previsão de Parto (079)', '2025-11-10', '', 'Parto', 7, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 2),
(5, 'Previsão de Secagem (079)', '2025-09-11', '', 'Geral', 7, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 2),
(6, 'Protocolo Pré-parto (079)', '2025-10-20', '', 'Saúde', 7, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 2),
(7, 'Previsão de Parto (114)', '2025-07-08', '', 'Parto', 9, 0, '2025-06-17 14:40:32', '2025-07-03 20:05:40', 3),
(8, 'Previsão de Secagem (114)', '2025-05-09', '', 'Geral', 9, 0, '2025-06-17 14:40:32', '2025-06-17 14:41:57', 3),
(9, 'Protocolo Pré-parto (114)', '2025-06-17', '', 'Saúde', 9, 0, '2025-06-17 14:40:32', '2025-06-18 16:28:57', 3),
(10, 'Previsão de Parto (120)', '2025-09-30', '', 'Parto', 12, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 4),
(11, 'Previsão de Secagem (120)', '2025-08-01', '', 'Geral', 12, 0, '2025-06-17 14:40:32', '2025-07-29 23:18:03', 4),
(12, 'Protocolo Pré-parto (120)', '2025-09-09', '', 'Saúde', 12, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 4),
(13, 'Previsão de Parto (132)', '2026-02-11', '', 'Parto', 13, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 5),
(14, 'Previsão de Secagem (132)', '2025-12-13', '', 'Geral', 13, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 5),
(15, 'Protocolo Pré-parto (132)', '2026-01-21', '', 'Saúde', 13, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 5),
(16, 'Previsão de Parto (242)', '2025-07-08', '', 'Parto', 22, 0, '2025-06-17 14:40:32', '2025-07-11 15:28:13', 6),
(17, 'Previsão de Secagem (242)', '2025-05-09', '', 'Geral', 22, 0, '2025-06-17 14:40:32', '2025-06-17 14:41:59', 6),
(18, 'Protocolo Pré-parto (242)', '2025-06-17', '', 'Saúde', 22, 0, '2025-06-17 14:40:32', '2025-06-18 16:28:46', 6),
(19, 'Previsão de Parto (249)', '2026-01-02', '', 'Parto', 24, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 7),
(20, 'Previsão de Secagem (249)', '2025-11-03', '', 'Geral', 24, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 7),
(21, 'Protocolo Pré-parto (249)', '2025-12-12', '', 'Saúde', 24, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 7),
(22, 'Previsão de Parto (253)', '2026-02-09', '', 'Parto', 25, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 8),
(23, 'Previsão de Secagem (253)', '2025-12-11', '', 'Geral', 25, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 8),
(24, 'Protocolo Pré-parto (253)', '2026-01-19', '', 'Saúde', 25, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 8),
(25, 'Previsão de Parto (261)', '2025-07-08', '', 'Parto', 29, 0, '2025-06-17 14:40:32', '2025-07-18 10:38:44', 9),
(26, 'Previsão de Secagem (261)', '2025-05-09', '', 'Geral', 29, 0, '2025-06-17 14:40:32', '2025-06-17 14:42:01', 9),
(27, 'Protocolo Pré-parto (261)', '2025-06-17', '', 'Saúde', 29, 0, '2025-06-17 14:40:32', '2025-06-18 16:29:14', 9),
(28, 'Previsão de Parto (265)', '2025-07-08', '', 'Parto', 32, 0, '2025-06-17 14:40:32', '2025-07-03 20:06:15', 10),
(29, 'Previsão de Secagem (265)', '2025-05-09', '', 'Geral', 32, 0, '2025-06-17 14:40:32', '2025-06-17 14:42:02', 10),
(30, 'Protocolo Pré-parto (265)', '2025-06-17', '', 'Saúde', 32, 0, '2025-06-17 14:40:32', '2025-06-18 16:28:39', 10),
(31, 'Previsão de Parto (266)', '2025-08-21', '', 'Parto', 33, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 11),
(32, 'Previsão de Secagem (266)', '2025-06-22', '', 'Geral', 33, 0, '2025-06-17 14:40:32', '2025-07-06 11:03:05', 11),
(33, 'Protocolo Pré-parto (266)', '2025-07-31', '', 'Saúde', 33, 0, '2025-06-17 14:40:32', '2025-08-03 13:16:21', 11),
(34, 'Previsão de Parto (274)', '2025-05-25', '', 'Parto', 38, 0, '2025-06-17 14:40:32', '2025-06-17 14:42:06', 12),
(35, 'Previsão de Secagem (274)', '2025-03-26', '', 'Geral', 38, 0, '2025-06-17 14:40:32', '2025-06-17 14:41:40', 12),
(36, 'Protocolo Pré-parto (274)', '2025-05-04', '', 'Saúde', 38, 0, '2025-06-17 14:40:32', '2025-06-17 14:41:50', 12),
(37, 'Previsão de Parto (276)', '2026-01-04', '', 'Parto', 39, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 13),
(38, 'Previsão de Secagem (276)', '2025-11-05', '', 'Geral', 39, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 13),
(39, 'Protocolo Pré-parto (276)', '2025-12-14', '', 'Saúde', 39, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 13),
(40, 'Previsão de Parto (277)', '2025-07-08', '', 'Parto', 40, 0, '2025-06-17 14:40:32', '2025-07-12 19:39:28', 14),
(41, 'Previsão de Secagem (277)', '2025-05-09', '', 'Geral', 40, 0, '2025-06-17 14:40:32', '2025-06-17 14:42:04', 14),
(42, 'Protocolo Pré-parto (277)', '2025-06-17', '', 'Saúde', 40, 0, '2025-06-17 14:40:32', '2025-06-18 16:29:31', 14),
(43, 'Previsão de Parto (287)', '2026-01-02', '', 'Parto', 43, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 15),
(44, 'Previsão de Secagem (287)', '2025-11-03', '', 'Geral', 43, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 15),
(45, 'Protocolo Pré-parto (287)', '2025-12-12', '', 'Saúde', 43, 1, '2025-06-17 14:40:32', '2025-06-17 14:40:32', 15),
(46, 'Previsão de Parto (295)', '2025-05-25', '', 'Parto', 48, 0, '2025-06-17 14:40:32', '2025-06-17 14:42:07', 16),
(47, 'Previsão de Secagem (295)', '2025-03-26', '', 'Geral', 48, 0, '2025-06-17 14:40:32', '2025-06-17 14:41:42', 16),
(48, 'Protocolo Pré-parto (295)', '2025-05-04', '', 'Saúde', 48, 0, '2025-06-17 14:40:32', '2025-06-17 14:41:52', 16),
(49, 'Colocar implante (224)', '2025-06-10', '', 'Saúde', 19, 0, '2025-06-17 14:50:54', '2025-06-17 14:52:42', NULL),
(50, '5ml lutalyse + 0.75 de ECP e retirar implante (224)', '2025-06-18', '', 'Saúde', 19, 0, '2025-06-17 14:50:54', '2025-06-18 17:58:12', NULL),
(51, 'Inseminar (224)', '2025-06-20', '', 'Saúde', 19, 0, '2025-06-17 14:50:54', '2025-06-21 10:15:15', NULL),
(52, 'Colocar implante (273)', '2025-06-10', '', 'Saúde', 37, 0, '2025-06-17 14:51:47', '2025-06-17 14:52:44', NULL),
(53, '5ml lutalyse + 0.75 de ECP e retirar implante (273)', '2025-06-18', '', 'Saúde', 37, 0, '2025-06-17 14:51:47', '2025-06-18 17:58:15', NULL),
(54, 'Inseminar (273)', '2025-06-20', '', 'Saúde', 37, 0, '2025-06-17 14:51:47', '2025-06-21 10:15:17', NULL),
(55, 'Manejo (REBANHO): BST', '2025-06-18', '', 'Geral', NULL, 0, '2025-06-18 09:01:47', '2025-06-18 09:02:10', 19),
(56, 'Manejo (REBANHO): BST', '2025-06-30', 'Recorrência automática de BST a partir de 18/06/2025', 'Geral', NULL, 0, '2025-06-18 09:02:10', '2025-07-01 10:44:48', 19),
(57, 'Manejo (REBANHO): Mastite', '2025-06-09', '', 'Vacina', NULL, 0, '2025-06-18 15:40:54', '2025-06-18 15:46:23', 20),
(58, 'Manejo (REBANHO): IBR', '2025-05-20', '', 'Vacina', NULL, 0, '2025-06-18 15:41:25', '2025-06-18 15:45:34', 21),
(59, 'Manejo (REBANHO): Vermífugo injetavel novilhas', '2025-06-24', '', 'Vacina', NULL, 0, '2025-06-18 15:42:03', '2025-06-25 11:54:56', 22),
(60, 'Manejo (REBANHO): Leptospirose', '2025-09-24', '', 'Vacina', NULL, 1, '2025-06-18 15:42:42', '2025-06-18 15:42:42', 23),
(61, 'Manejo (REBANHO): Clostridioses', '2024-10-11', '', 'Vacina', NULL, 0, '2025-06-18 15:44:15', '2025-06-18 15:45:17', 24),
(62, 'Manejo (REBANHO): Mastite', '2025-09-09', 'Recorrência automática de Vacinas a partir de 09/06/2025', 'Vacina', NULL, 1, '2025-06-18 15:46:23', '2025-06-18 15:46:23', 20),
(63, 'Inseminar ', '2025-06-19', '', 'Cio', 16, 0, '2025-06-18 17:06:12', '2025-06-19 11:11:07', NULL),
(67, 'Desmame (300)', '2025-05-24', '', '', 53, 0, '2025-06-18 23:16:49', '2025-06-18 23:18:12', 28),
(68, 'Brucelose (300)', '2025-07-20', '', 'Saúde', 53, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:09', 29),
(69, 'Mochar (300)', '2025-07-20', '', 'Saúde', 53, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:10', 30),
(70, 'Desmame (301)', '2025-06-06', '', '', 54, 0, '2025-06-18 23:16:49', '2025-06-18 23:18:20', 31),
(71, 'Brucelose (301)', '2025-07-20', '', 'Saúde', 54, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:12', 32),
(72, 'Mochar (301)', '2025-07-20', '', 'Saúde', 54, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:13', 33),
(73, 'Desmame (302)', '2025-06-08', '', '', 55, 0, '2025-06-18 23:16:49', '2025-06-18 23:18:22', 34),
(74, 'Brucelose (302)', '2025-07-20', '', 'Saúde', 55, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:15', 35),
(75, 'Mochar (302)', '2025-07-20', '', 'Saúde', 55, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:16', 36),
(76, 'Desmame (303)', '2025-06-08', '', '', 56, 0, '2025-06-18 23:16:49', '2025-06-18 23:18:25', 37),
(77, 'Brucelose (303)', '2025-07-20', '', 'Saúde', 56, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:17', 38),
(78, 'Mochar (303)', '2025-07-20', '', 'Saúde', 56, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:19', 39),
(79, 'Desmame (304)', '2025-06-09', '', '', 57, 0, '2025-06-18 23:16:49', '2025-06-18 23:18:36', 40),
(80, 'Brucelose (304)', '2025-07-20', '', 'Saúde', 57, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:22', 41),
(81, 'Mochar (304)', '2025-07-20', '', 'Saúde', 57, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:23', 42),
(82, 'Desmame (305)', '2025-06-05', '', '', 58, 0, '2025-06-18 23:16:49', '2025-06-18 23:18:17', 43),
(83, 'Brucelose (305)', '2025-07-20', '', 'Saúde', 58, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:25', 44),
(84, 'Mochar (305)', '2025-07-20', '', 'Saúde', 58, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:27', 45),
(85, 'Desmame (306)', '2025-06-20', '', '', 59, 0, '2025-06-18 23:16:49', '2025-06-18 23:19:12', 46),
(86, 'Brucelose (306)', '2025-07-20', '', '', 59, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:28', 47),
(87, 'Mochar (306)', '2025-07-20', '', '', 59, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:30', 48),
(88, 'Desmame (307)', '2025-07-18', '', '', 60, 0, '2025-06-18 23:16:49', '2025-07-18 15:51:51', 49),
(89, 'Brucelose (307)', '2025-08-18', '', '', 60, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 50),
(90, 'Mochar (307)', '2025-08-18', '', '', 60, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 51),
(91, 'Desmame (308)', '2025-07-24', '', '', 61, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:32', 52),
(92, 'Brucelose (308)', '2025-08-24', '', '', 61, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 53),
(93, 'Mochar (308)', '2025-08-24', '', '', 61, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 54),
(94, 'Desmame (309)', '2025-07-24', '', '', 62, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:33', 55),
(95, 'Brucelose (309)', '2025-08-24', '', '', 62, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 56),
(96, 'Mochar (309)', '2025-08-24', '', '', 62, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 57),
(97, 'Desmame (310)', '2025-07-25', '', '', 63, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:35', 58),
(98, 'Brucelose (310)', '2025-08-25', '', '', 63, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 59),
(99, 'Mochar (310)', '2025-08-25', '', '', 63, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 60),
(100, 'Desmame (311)', '2025-07-28', '', '', 64, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:37', 61),
(101, 'Brucelose (311)', '2025-08-28', '', '', 64, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 62),
(102, 'Mochar (311)', '2025-08-28', '', '', 64, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 63),
(103, 'Desmame (312)', '2025-07-28', '', '', 65, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:39', 64),
(104, 'Brucelose (312)', '2025-08-28', '', '', 65, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 65),
(105, 'Mochar (312)', '2025-08-28', '', '', 65, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 66),
(106, 'Desmame (313)', '2025-07-29', '', '', 66, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:41', 67),
(107, 'Brucelose (313)', '2025-08-29', '', '', 66, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 68),
(108, 'Mochar (313)', '2025-08-29', '', '', 66, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 69),
(109, 'Desmame (314)', '2025-07-30', '', '', 67, 0, '2025-06-18 23:16:49', '2025-07-29 18:56:43', 70),
(110, 'Brucelose (314)', '2025-08-30', '', '', 67, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 71),
(111, 'Mochar (314)', '2025-08-30', '', '', 67, 1, '2025-06-18 23:16:49', '2025-06-18 23:16:49', 72),
(115, 'Inseminar ', '2025-06-20', '', 'Cio', 26, 0, '2025-06-20 09:38:41', '2025-06-21 10:15:22', NULL),
(116, 'Encomendar soja', '2025-06-30', '', 'Geral', NULL, 0, '2025-06-29 17:07:39', '2025-07-01 10:44:51', NULL),
(117, 'Previsão de Parto (263)', '2026-03-01', '', 'Parto', 31, 1, '2025-06-30 14:37:09', '2025-06-30 14:37:09', 74),
(118, 'Previsão de Secagem (263)', '2025-12-31', '', 'Geral', 31, 1, '2025-06-30 14:37:09', '2025-06-30 14:37:09', 74),
(119, 'Protocolo Pré-parto (263)', '2026-02-08', '', 'Saúde', 31, 1, '2025-06-30 14:37:09', '2025-06-30 14:37:09', 74),
(120, 'Manejo (REBANHO): BST', '2025-07-12', 'Recorrência automática de BST a partir de 30/06/2025', 'Geral', NULL, 0, '2025-07-01 10:44:48', '2025-07-12 15:46:49', 19),
(121, 'Desmame (315)', '2025-08-27', '', '', 74, 1, '2025-07-08 16:33:15', '2025-07-08 16:33:15', 75),
(122, 'Brucelose (315)', '2025-09-27', '', '', 74, 1, '2025-07-08 16:33:15', '2025-07-08 16:33:15', 76),
(123, 'Mochar (315)', '2025-09-27', '', '', 74, 1, '2025-07-08 16:33:15', '2025-07-08 16:33:15', 77),
(124, 'Desmame (317)', '2025-09-09', '', '', 75, 1, '2025-07-11 15:26:34', '2025-07-11 15:26:34', 78),
(125, 'Brucelose (317)', '2025-10-09', '', '', 75, 1, '2025-07-11 15:26:34', '2025-07-11 15:26:34', 79),
(126, 'Mochar (317)', '2025-10-09', '', '', 75, 1, '2025-07-11 15:26:34', '2025-07-11 15:26:34', 80),
(127, 'Desmame (316)', '2025-09-11', '', '', 76, 1, '2025-07-11 15:29:55', '2025-07-11 15:29:55', 81),
(128, 'Brucelose (316)', '2025-10-11', '', '', 76, 1, '2025-07-11 15:29:55', '2025-07-11 15:29:55', 82),
(129, 'Mochar (316)', '2025-10-11', '', '', 76, 1, '2025-07-11 15:29:55', '2025-07-11 15:29:55', 83),
(130, 'Manejo (REBANHO): BST', '2025-07-24', 'Recorrência automática de BST a partir de 12/07/2025', 'Geral', NULL, 0, '2025-07-12 15:46:49', '2025-07-25 08:23:11', 19),
(131, 'Desmame (001)', '2025-09-18', '', '', 77, 1, '2025-07-17 21:57:03', '2025-07-17 21:57:03', 84),
(132, 'Brucelose (001)', '2025-10-18', '', '', 77, 1, '2025-07-17 21:57:03', '2025-07-17 21:57:03', 85),
(133, 'Mochar (001)', '2025-10-18', '', '', 77, 1, '2025-07-17 21:57:03', '2025-07-17 21:57:03', 86),
(134, 'Desmame (002)', '2025-09-18', '', '', 78, 1, '2025-07-17 21:57:14', '2025-07-17 21:57:14', 87),
(135, 'Brucelose (002)', '2025-10-18', '', '', 78, 1, '2025-07-17 21:57:14', '2025-07-17 21:57:14', 88),
(136, 'Mochar (002)', '2025-10-18', '', '', 78, 1, '2025-07-17 21:57:14', '2025-07-17 21:57:14', 89),
(137, 'Desmame (003)', '2025-09-18', '', '', 79, 1, '2025-07-17 21:57:26', '2025-07-17 21:57:26', 90),
(138, 'Brucelose (003)', '2025-10-18', '', '', 79, 1, '2025-07-17 21:57:26', '2025-07-17 21:57:26', 91),
(139, 'Mochar (003)', '2025-10-18', '', '', 79, 1, '2025-07-17 21:57:26', '2025-07-17 21:57:26', 92),
(140, 'Desmame (004)', '2025-09-18', '', '', 80, 1, '2025-07-17 21:57:36', '2025-07-17 21:57:36', 93),
(141, 'Brucelose (004)', '2025-10-18', '', '', 80, 1, '2025-07-17 21:57:36', '2025-07-17 21:57:36', 94),
(142, 'Mochar (004)', '2025-10-18', '', '', 80, 1, '2025-07-17 21:57:36', '2025-07-17 21:57:36', 95),
(143, 'Desmame (318)', '2025-09-18', '', '', 81, 1, '2025-07-18 10:13:45', '2025-07-18 10:13:45', 96),
(144, 'Brucelose (318)', '2025-10-18', '', '', 81, 1, '2025-07-18 10:13:45', '2025-07-18 10:13:45', 97),
(145, 'Mochar (318)', '2025-10-18', '', '', 81, 1, '2025-07-18 10:13:45', '2025-07-18 10:13:45', 98),
(146, 'Colocar implante (030)', '2025-07-17', '', 'Saúde', 3, 0, '2025-07-18 10:33:01', '2025-07-18 10:38:24', NULL),
(147, '5ml lutalyse + 0.75 de ECP e retirar implante (030)', '2025-07-25', '', 'Saúde', 3, 0, '2025-07-18 10:33:01', '2025-07-24 18:31:43', NULL),
(148, 'Inseminar (030)', '2025-07-27', '', 'Saúde', 3, 0, '2025-07-18 10:33:01', '2025-07-25 17:56:08', NULL),
(149, 'Colocar implante (115)', '2025-07-17', '', 'Saúde', 10, 0, '2025-07-18 10:33:20', '2025-07-18 10:38:26', NULL),
(150, '5ml lutalyse + 0.75 de ECP e retirar implante (115)', '2025-07-25', '', 'Saúde', 10, 0, '2025-07-18 10:33:20', '2025-07-24 18:33:55', NULL),
(151, 'Inseminar (115)', '2025-07-27', '', 'Saúde', 10, 0, '2025-07-18 10:33:20', '2025-07-25 18:28:32', NULL),
(152, 'Colocar implante (282)', '2025-07-17', '', 'Saúde', 41, 0, '2025-07-18 10:33:35', '2025-07-18 10:38:28', NULL),
(153, '5ml lutalyse + 0.75 de ECP e retirar implante (282)', '2025-07-25', '', 'Saúde', 41, 0, '2025-07-18 10:33:35', '2025-07-24 18:38:47', NULL),
(154, 'Inseminar (282)', '2025-07-27', '', 'Saúde', 41, 0, '2025-07-18 10:33:35', '2025-07-25 18:11:25', NULL),
(155, 'Colocar implante (143)', '2025-07-17', '', 'Saúde', 16, 0, '2025-07-18 10:33:56', '2025-07-18 10:38:29', NULL),
(156, '5ml lutalyse + 0.75 de ECP e retirar implante (143)', '2025-07-25', '', 'Saúde', 16, 0, '2025-07-18 10:33:56', '2025-07-24 18:28:00', NULL),
(157, 'Inseminar (143)', '2025-07-27', '', 'Saúde', 16, 0, '2025-07-18 10:33:56', '2025-07-25 18:06:04', NULL),
(158, 'Colocar implante (067)', '2025-07-17', '', 'Saúde', 5, 0, '2025-07-18 10:34:14', '2025-07-18 10:38:31', NULL),
(159, '5ml lutalyse + 0.75 de ECP e retirar implante (067)', '2025-07-25', '', 'Saúde', 5, 0, '2025-07-18 10:34:14', '2025-07-24 18:30:32', NULL),
(160, 'Inseminar (067)', '2025-07-27', '', 'Saúde', 5, 0, '2025-07-18 10:34:14', '2025-07-25 18:22:20', NULL),
(161, 'Colocar implante (294)', '2025-07-17', '', 'Saúde', 47, 0, '2025-07-18 10:34:29', '2025-07-18 10:38:32', NULL),
(162, '5ml lutalyse + 0.75 de ECP e retirar implante (294)', '2025-07-25', '', 'Saúde', 47, 0, '2025-07-18 10:34:29', '2025-07-24 18:29:57', NULL),
(163, 'Inseminar (294)', '2025-07-27', '', 'Saúde', 47, 0, '2025-07-18 10:34:29', '2025-07-25 18:01:05', NULL),
(164, 'Colocar implante (065)', '2025-07-17', '', 'Saúde', 4, 0, '2025-07-18 10:34:43', '2025-07-18 10:38:35', NULL),
(165, '5ml lutalyse + 0.75 de ECP e retirar implante (065)', '2025-07-25', '', 'Saúde', 4, 0, '2025-07-18 10:34:43', '2025-07-24 18:38:35', NULL),
(166, 'Inseminar (065)', '2025-07-27', '', 'Saúde', 4, 0, '2025-07-18 10:34:43', '2025-07-25 18:17:25', NULL),
(167, 'Colocar implante (296)', '2025-07-17', '', 'Saúde', 49, 0, '2025-07-18 10:34:58', '2025-07-18 10:38:37', NULL),
(168, '5ml lutalyse + 0.75 de ECP e retirar implante (296)', '2025-07-25', '', 'Saúde', 49, 0, '2025-07-18 10:34:58', '2025-07-24 18:33:05', NULL),
(169, 'Inseminar (296)', '2025-07-27', '', 'Saúde', 49, 0, '2025-07-18 10:34:58', '2025-07-25 18:35:09', NULL),
(170, 'Colocar implante (262)', '2025-07-18', '', 'Saúde', 30, 0, '2025-07-18 10:35:14', '2025-07-18 18:53:00', NULL),
(171, '5ml lutalyse + 0.75 de ECP e retirar implante (262)', '2025-07-26', '', 'Saúde', 30, 0, '2025-07-18 10:35:14', '2025-07-28 22:24:23', NULL),
(172, 'Inseminar (262)', '2025-07-28', '', 'Saúde', 30, 0, '2025-07-18 10:35:14', '2025-07-26 18:33:23', NULL),
(173, 'Colocar implante (227)', '2025-07-18', '', 'Saúde', 20, 0, '2025-07-18 10:35:33', '2025-07-18 18:53:02', NULL),
(174, '5ml lutalyse + 0.75 de ECP e retirar implante (227)', '2025-07-26', '', 'Saúde', 20, 0, '2025-07-18 10:35:33', '2025-07-28 22:24:26', NULL),
(175, 'Inseminar (227)', '2025-07-28', '', 'Saúde', 20, 0, '2025-07-18 10:35:33', '2025-07-28 22:24:47', NULL),
(176, 'Colocar implante (284)', '2025-07-18', '', 'Saúde', 42, 0, '2025-07-18 10:35:46', '2025-07-18 18:53:04', NULL),
(177, '5ml lutalyse + 0.75 de ECP e retirar implante (284)', '2025-07-26', '', 'Saúde', 42, 0, '2025-07-18 10:35:46', '2025-07-28 22:24:28', NULL),
(178, 'Inseminar (284)', '2025-07-28', '', 'Saúde', 42, 0, '2025-07-18 10:35:46', '2025-07-26 18:32:55', NULL),
(179, 'Colocar implante (299)', '2025-07-18', '', 'Saúde', 52, 0, '2025-07-18 10:36:00', '2025-07-18 18:53:06', NULL),
(180, '5ml lutalyse + 0.75 de ECP e retirar implante (299)', '2025-07-26', '', 'Saúde', 52, 0, '2025-07-18 10:36:00', '2025-07-28 22:24:29', NULL),
(181, 'Inseminar (299)', '2025-07-28', '', 'Saúde', 52, 0, '2025-07-18 10:36:00', '2025-07-26 18:29:17', NULL),
(182, 'Colocar implante (139)', '2025-07-19', '', 'Saúde', 15, 0, '2025-07-18 10:36:20', '2025-07-19 18:12:26', NULL),
(183, '5ml lutalyse + 0.75 de ECP e retirar implante (139)', '2025-07-27', '', 'Saúde', 15, 0, '2025-07-18 10:36:20', '2025-07-28 22:24:31', NULL),
(184, 'Inseminar (139)', '2025-07-29', '', 'Saúde', 15, 0, '2025-07-18 10:36:20', '2025-07-28 22:24:50', NULL),
(185, 'Colocar implante (118)', '2025-07-19', '', 'Saúde', 11, 0, '2025-07-18 10:36:34', '2025-07-19 18:11:51', NULL),
(186, '5ml lutalyse + 0.75 de ECP e retirar implante (118)', '2025-07-27', '', 'Saúde', 11, 0, '2025-07-18 10:36:34', '2025-07-28 22:24:33', NULL),
(187, 'Inseminar (118)', '2025-07-29', '', 'Saúde', 11, 0, '2025-07-18 10:36:34', '2025-07-28 22:24:52', NULL),
(188, 'Colocar implante (293)', '2025-07-19', '', 'Saúde', 46, 0, '2025-07-18 10:36:54', '2025-07-19 18:12:48', NULL),
(189, '5ml lutalyse + 0.75 de ECP e retirar implante (293)', '2025-07-27', '', 'Saúde', 46, 0, '2025-07-18 10:36:54', '2025-07-28 22:24:35', NULL),
(190, 'Inseminar (293)', '2025-07-29', '', 'Saúde', 46, 0, '2025-07-18 10:36:54', '2025-07-28 22:24:54', NULL),
(191, 'Colocar implante (272)', '2025-07-19', '', 'Saúde', 36, 0, '2025-07-18 10:37:09', '2025-07-19 18:09:21', NULL),
(192, '5ml lutalyse + 0.75 de ECP e retirar implante (272)', '2025-07-27', '', 'Saúde', 36, 0, '2025-07-18 10:37:09', '2025-07-28 22:24:37', NULL),
(193, 'Inseminar (272)', '2025-07-29', '', 'Saúde', 36, 0, '2025-07-18 10:37:09', '2025-07-28 22:24:56', NULL),
(194, 'Colocar implante (146)', '2025-07-19', '', 'Saúde', 17, 0, '2025-07-18 10:37:29', '2025-07-19 18:10:12', NULL),
(195, '5ml lutalyse + 0.75 de ECP e retirar implante (146)', '2025-07-27', '', 'Saúde', 17, 0, '2025-07-18 10:37:29', '2025-07-28 22:24:39', NULL),
(196, 'Inseminar (146)', '2025-07-29', '', 'Saúde', 17, 0, '2025-07-18 10:37:29', '2025-07-28 22:24:58', NULL),
(197, 'Colocar implante (270)', '2025-07-19', '', 'Saúde', 35, 0, '2025-07-18 10:37:52', '2025-07-19 18:09:03', NULL),
(198, '5ml lutalyse + 0.75 de ECP e retirar implante (270)', '2025-07-27', '', 'Saúde', 35, 0, '2025-07-18 10:37:52', '2025-07-28 22:24:41', NULL),
(199, 'Inseminar (270)', '2025-07-29', '', 'Saúde', 35, 0, '2025-07-18 10:37:52', '2025-07-28 22:25:00', NULL),
(200, 'Colocar implante (298)', '2025-07-19', '', 'Saúde', 51, 0, '2025-07-18 10:38:05', '2025-07-19 18:08:57', NULL),
(201, '5ml lutalyse + 0.75 de ECP e retirar implante (298)', '2025-07-27', '', 'Saúde', 51, 0, '2025-07-18 10:38:05', '2025-07-28 22:24:43', NULL),
(202, 'Inseminar (298)', '2025-07-29', '', 'Saúde', 51, 0, '2025-07-18 10:38:05', '2025-07-28 22:25:02', NULL),
(203, 'Manejo (REBANHO): BST', '2025-08-05', 'Recorrência automática de BST a partir de 24/07/2025', 'Geral', NULL, 1, '2025-07-25 08:23:11', '2025-07-25 08:23:11', 19);

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_categorias`
--

CREATE TABLE `financeiro_categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('PAGAR','RECEBER') NOT NULL COMMENT 'Define se a categoria é de despesa ou receita',
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `financeiro_categorias`
--

INSERT INTO `financeiro_categorias` (`id`, `nome`, `tipo`, `parent_id`) VALUES
(1, 'Venda de Produção', 'RECEBER', NULL),
(2, 'Venda de Leite', 'RECEBER', 1),
(3, 'Venda de Animais', 'RECEBER', NULL),
(4, 'Venda de Bezerro(a)', 'RECEBER', 3),
(5, 'Venda de Novilha', 'RECEBER', 3),
(6, 'Venda de Animal de Descarte', 'RECEBER', 3),
(7, 'Alimentação', 'PAGAR', NULL),
(8, 'Compra de Concentrados', 'PAGAR', 7),
(9, 'Compra de Volumosos', 'PAGAR', 7),
(10, 'Compra de Aditivos e Minerais', 'PAGAR', 7),
(11, 'Saúde Animal', 'PAGAR', NULL),
(12, 'Compra de Medicamentos', 'PAGAR', 11),
(13, 'Compra de Vacinas', 'PAGAR', 11),
(14, 'Materiais de Consumo', 'PAGAR', NULL),
(15, 'Consumíveis de Ordenha', 'PAGAR', 14),
(16, 'Consumíveis de Inseminação', 'PAGAR', 14),
(17, 'Consumíveis para Bezerros', 'PAGAR', 14),
(18, 'Material de Limpeza', 'PAGAR', 14),
(19, 'Manutenção', 'PAGAR', NULL),
(20, 'Manutenção de Máquinas e Tratores', 'PAGAR', 19),
(21, 'Manutenção de Instalações', 'PAGAR', 19),
(22, 'Serviços Terceirizados', 'PAGAR', NULL),
(23, 'Serviços Veterinários', 'PAGAR', 22),
(24, 'Serviços de Inseminador', 'PAGAR', 22),
(25, 'Contabilidade', 'PAGAR', 22),
(26, 'Salários e Mão de Obra', 'PAGAR', NULL),
(27, 'Salários', 'PAGAR', 26),
(28, 'Encargos', 'PAGAR', 26),
(29, 'Despesas Administrativas', 'PAGAR', NULL),
(30, 'Impostos e Taxas', 'PAGAR', NULL),
(33, 'Semem', 'PAGAR', NULL),
(34, 'Holandês', 'PAGAR', 33),
(35, 'Angus', 'PAGAR', 33);

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_contatos`
--

CREATE TABLE `financeiro_contatos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo` enum('Fornecedor','Cliente','Outro') NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `financeiro_contatos`
--

INSERT INTO `financeiro_contatos` (`id`, `nome`, `tipo`, `telefone`, `cpf_cnpj`) VALUES
(2, 'Santa Clara', 'Fornecedor', '', ''),
(3, 'Nutritec', 'Fornecedor', '', ''),
(4, 'Agtech', 'Fornecedor', '', '20263495000159');

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_lancamentos`
--

CREATE TABLE `financeiro_lancamentos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `tipo` enum('PAGAR','RECEBER') NOT NULL,
  `id_contato` int(11) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `financeiro_lancamentos`
--

INSERT INTO `financeiro_lancamentos` (`id`, `descricao`, `valor_total`, `tipo`, `id_contato`, `id_categoria`, `observacoes`, `created_at`, `updated_at`) VALUES
(26, 'Nota 26855', 4699.50, 'PAGAR', 3, NULL, '', '2025-06-25 17:55:50', '2025-06-25 17:55:50'),
(29, '483239', 5232.50, 'PAGAR', 2, NULL, '', '2025-06-27 20:07:41', '2025-06-27 20:07:41'),
(30, 'nota', 5289.29, 'PAGAR', 2, NULL, '', '2025-06-30 00:32:15', '2025-06-30 00:34:08'),
(31, '003.304', 3574.00, 'PAGAR', 4, 21, '', '2025-07-02 11:59:43', '2025-07-02 11:59:43'),
(32, 'Soja ', 5584.50, 'PAGAR', 2, NULL, '', '2025-07-02 12:12:57', '2025-07-02 12:12:57'),
(33, '3653', 11362.50, 'PAGAR', 2, NULL, '', '2025-07-18 15:09:02', '2025-07-18 15:09:02'),
(34, '17866', 2355.00, 'PAGAR', 3, NULL, '', '2025-07-23 16:37:23', '2025-07-23 16:37:23'),
(35, '176', 6147.27, 'PAGAR', 2, NULL, '', '2025-07-24 19:20:44', '2025-07-24 19:20:44'),
(36, 'Demem', 2000.00, 'PAGAR', 2, NULL, '', '2025-07-29 01:23:33', '2025-07-29 01:23:33'),
(37, '555', 5670.00, 'PAGAR', 2, NULL, '', '2025-08-07 14:20:45', '2025-08-07 14:20:45'),
(38, 'Fgyy', 1969.50, 'PAGAR', 3, NULL, '', '2025-08-07 15:30:23', '2025-08-07 15:30:23');

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_lancamento_itens`
--

CREATE TABLE `financeiro_lancamento_itens` (
  `id` int(11) NOT NULL,
  `id_lancamento` int(11) NOT NULL,
  `id_produto_estoque` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `quantidade` decimal(10,2) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `financeiro_lancamento_itens`
--

INSERT INTO `financeiro_lancamento_itens` (`id`, `id_lancamento`, `id_produto_estoque`, `id_categoria`, `quantidade`, `valor_unitario`) VALUES
(25, 26, 5, NULL, 2410.00, 1.95),
(28, 29, 1, NULL, 2990.00, 1.75),
(38, 30, 7, NULL, 26.00, 158.87),
(39, 30, 8, NULL, 3.00, 110.46),
(40, 30, 9, NULL, 1.00, 827.29),
(41, 32, 2, NULL, 2550.00, 2.19),
(42, 33, 1, NULL, 3030.00, 1.71),
(43, 33, 2, NULL, 3030.00, 2.04),
(44, 34, 4, NULL, 1570.00, 1.50),
(45, 35, 7, NULL, 26.00, 154.48),
(46, 35, 8, NULL, 7.00, 112.50),
(47, 35, 9, NULL, 1.00, 827.29),
(48, 35, 10, NULL, 3.00, 172.00),
(49, 36, 15, NULL, 10.00, 125.00),
(50, 36, 15, NULL, 10.00, 75.00),
(51, 37, 2, NULL, 2700.00, 2.10),
(52, 38, 5, NULL, 1010.00, 1.95);

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_parcelas`
--

CREATE TABLE `financeiro_parcelas` (
  `id` int(11) NOT NULL,
  `id_lancamento` int(11) NOT NULL,
  `numero_parcela` int(11) NOT NULL,
  `valor_parcela` decimal(10,2) NOT NULL,
  `data_vencimento` date NOT NULL,
  `status` enum('Aberto','Pago','Atrasado','Cancelado') NOT NULL DEFAULT 'Aberto',
  `data_pagamento` date DEFAULT NULL,
  `forma_pagamento` varchar(100) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `financeiro_parcelas`
--

INSERT INTO `financeiro_parcelas` (`id`, `id_lancamento`, `numero_parcela`, `valor_parcela`, `data_vencimento`, `status`, `data_pagamento`, `forma_pagamento`, `valor_pago`, `observacoes`) VALUES
(76, 26, 1, 4699.50, '2025-06-25', 'Aberto', NULL, 'Boleto', NULL, NULL),
(79, 29, 1, 5232.50, '2025-08-15', 'Aberto', NULL, 'Boleto', NULL, NULL),
(89, 30, 1, 1763.10, '2025-07-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(90, 30, 2, 1763.10, '2025-08-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(91, 30, 3, 1763.10, '2025-09-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(92, 31, 1, 893.50, '2025-07-16', 'Aberto', NULL, 'Boleto', NULL, NULL),
(93, 31, 2, 893.50, '2025-08-16', 'Aberto', NULL, 'Boleto', NULL, NULL),
(94, 31, 3, 893.50, '2025-09-16', 'Aberto', NULL, 'Boleto', NULL, NULL),
(95, 31, 4, 893.50, '2025-10-16', 'Aberto', NULL, 'Boleto', NULL, NULL),
(96, 32, 1, 5584.50, '2025-08-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(97, 33, 1, 11362.50, '2025-09-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(98, 34, 1, 2355.00, '2025-07-23', 'Aberto', NULL, 'PIX', NULL, NULL),
(99, 35, 1, 2049.09, '2025-08-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(100, 35, 2, 2049.09, '2025-09-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(101, 35, 3, 2049.09, '2025-10-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(102, 36, 1, 2000.00, '2025-08-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(103, 37, 1, 5670.00, '2025-08-15', 'Pago', NULL, 'Desconto no Leite', NULL, NULL),
(104, 38, 1, 1969.50, '2025-08-07', 'Aberto', NULL, 'PIX', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `gado`
--

CREATE TABLE `gado` (
  `id` int(11) NOT NULL,
  `grupo` enum('Bezerra','Novilha','Lactante','Seca','Corte') NOT NULL,
  `status` enum('Vazia','Inseminada','Prenha') NOT NULL,
  `bst` tinyint(1) DEFAULT 0,
  `leite_descarte` enum('Sim','Não') NOT NULL DEFAULT 'Não',
  `cor_bastao` enum('','Azul','Verde','Vermelho') NOT NULL DEFAULT '',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `brinco` varchar(50) NOT NULL,
  `nascimento` date NOT NULL,
  `sexo` varchar(10) NOT NULL DEFAULT 'Fêmea',
  `escore` decimal(3,2) NOT NULL,
  `id_pai` int(11) DEFAULT NULL,
  `id_mae` int(11) DEFAULT NULL,
  `descarte` tinyint(1) DEFAULT 0,
  `observacoes` text DEFAULT NULL,
  `raca` enum('Holandês','Angus','Jersey') DEFAULT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `Data_secagem` date DEFAULT NULL,
  `Data_preparto` date DEFAULT NULL,
  `Data_parto` date DEFAULT NULL,
  `data_monitoramento_cio` date DEFAULT NULL,
  `pai` varchar(100) DEFAULT NULL,
  `mae` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `gado`
--

INSERT INTO `gado` (`id`, `grupo`, `status`, `bst`, `leite_descarte`, `cor_bastao`, `ativo`, `created_at`, `updated_at`, `brinco`, `nascimento`, `sexo`, `escore`, `id_pai`, `id_mae`, `descarte`, `observacoes`, `raca`, `nome`, `Data_secagem`, `Data_preparto`, `Data_parto`, `data_monitoramento_cio`, `pai`, `mae`) VALUES
(1, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 12:42:47', '017', '2024-08-19', 'Fêmea', 0.00, 102, 49, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-18 10:40:18', '020', '2024-08-21', 'Fêmea', 5.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 17:57:00', '030', '2017-01-01', 'Fêmea', 3.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Lactante', 'Inseminada', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 18:16:53', '065', '2023-03-13', 'Fêmea', 0.00, 104, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 18:20:38', '067', '2016-01-01', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Seca', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-30 11:03:03', '071', '2021-09-19', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, '2025-07-31', '2025-08-21', NULL, NULL, NULL),
(7, 'Novilha', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-29 23:00:59', '079', '2023-07-17', 'Fêmea', 0.00, NULL, 25, 0, 'Importado via CSV', 'Holandês', '', NULL, '2025-10-20', '2025-11-10', NULL, NULL, NULL),
(8, 'Lactante', 'Vazia', 0, 'Não', '', 0, '2025-06-17 10:22:53', '2025-07-01 20:12:05', '108', '2017-01-01', 'Fêmea', 5.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Lactante', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-30 23:14:44', '114', '2021-03-01', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', '2025-05-09', '2025-06-17', '2025-07-08', NULL, NULL, NULL),
(10, 'Lactante', 'Inseminada', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 18:28:20', '115', '2017-01-01', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-28 22:27:41', '118', '2017-01-01', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'Seca', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-30 11:01:55', '120', '2018-01-01', 'Fêmea', 1.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, '2025-09-09', '2025-09-30', NULL, NULL, NULL),
(13, 'Lactante', 'Prenha', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 14:40:32', '132', '2021-05-01', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', '2025-12-13', '2026-01-21', '2026-02-11', NULL, NULL, NULL),
(14, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-19 07:48:56', '136', '2021-11-23', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-08-05 13:53:20', '139', '2021-05-19', 'Fêmea', 5.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-23 00:10:00', '143', '2023-03-02', 'Fêmea', 0.00, 104, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-28 22:25:44', '146', '2021-03-01', 'Fêmea', 3.50, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Lactante', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-30 22:56:04', '221', '2023-03-09', 'Fêmea', 0.00, 105, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-27 10:03:38', '224', '2022-01-11', 'Fêmea', 3.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:22:53', '227', '2023-05-10', 'Fêmea', 0.00, 104, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '239', '2024-06-21', 'Fêmea', 0.00, 102, 25, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'Lactante', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-11 15:26:13', '242', '2020-01-18', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', '2025-05-09', '2025-06-17', '2025-07-08', NULL, NULL, NULL),
(23, 'Lactante', 'Inseminada', 1, 'Não', '', 0, '2025-06-17 10:22:53', '2025-07-24 18:22:08', '247', '2020-03-06', 'Fêmea', 3.50, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'Novilha', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-29 23:00:59', '249', '2023-03-20', 'Fêmea', 0.00, 104, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, '2025-12-12', '2026-01-02', NULL, NULL, NULL),
(25, 'Lactante', 'Prenha', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 14:40:32', '253', '2020-07-27', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', '2025-12-11', '2026-01-19', '2026-02-09', NULL, NULL, NULL),
(26, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-29 11:45:13', '254', '2020-05-17', 'Fêmea', 3.50, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'Seca', 'Inseminada', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:22:53', '255', '2017-01-01', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'Lactante', 'Vazia', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-08-05 13:53:53', '260', '2020-01-09', 'Fêmea', 5.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'Lactante', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 20:22:58', '261', '2017-04-30', 'Fêmea', 1.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-30 14:36:06', '262', '2023-12-08', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Novilha', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-29 23:00:59', '263', '2023-12-17', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, '2026-02-08', '2026-03-01', NULL, NULL, NULL),
(32, 'Lactante', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-02 14:06:30', '265', '2020-05-16', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', '2025-05-09', '2025-06-17', '2025-07-08', NULL, NULL, NULL),
(33, 'Novilha', 'Prenha', 0, 'Não', '', 0, '2025-06-17 10:22:53', '2025-08-03 13:16:09', '266', '2022-07-19', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'Seca', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:22:53', '269', '2022-06-11', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-28 22:27:19', '270', '2022-06-11', 'Fêmea', 3.50, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-28 22:26:25', '272', '2021-02-01', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-10 07:24:07', '273', '2022-07-09', 'Fêmea', 4.00, NULL, NULL, 0, 'Não deu inseminação. Sangrou vagina e não achamos entrada cervix', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'Novilha', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-29 23:00:59', '274', '2023-01-11', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, '2025-05-04', '2025-05-25', NULL, NULL, NULL),
(39, 'Lactante', 'Prenha', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 14:40:32', '276', '2018-02-19', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', '2025-11-05', '2025-12-14', '2026-01-04', NULL, NULL, NULL),
(40, 'Lactante', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-11 15:29:42', '277', '2021-06-04', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', '2025-05-09', '2025-06-17', '2025-07-08', NULL, NULL, NULL),
(41, 'Lactante', 'Inseminada', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 18:11:13', '282', '2023-01-01', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-30 14:40:03', '284', '2023-07-26', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'Novilha', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-29 23:00:59', '287', '2023-05-15', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, '2025-12-12', '2026-01-02', NULL, NULL, NULL),
(44, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '291', '2024-06-13', 'Fêmea', 0.00, 102, 36, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '292', '2024-06-05', 'Fêmea', 0.00, 106, 29, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'Lactante', 'Inseminada', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-28 22:27:02', '293', '2023-05-16', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'Lactante', 'Inseminada', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 18:00:55', '294', '2022-01-11', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'Novilha', 'Prenha', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-29 23:00:59', '295', '2022-01-11', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, '2025-05-04', '2025-05-25', NULL, NULL, NULL),
(49, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-25 18:34:45', '296', '2022-02-01', 'Fêmea', 0.00, 103, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-27 10:03:04', '297', '2023-05-11', 'Fêmea', 1.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'Lactante', 'Inseminada', 1, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-28 22:26:45', '298', '2022-03-23', 'Fêmea', 3.25, NULL, NULL, 0, 'Importado via CSV', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'Novilha', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-07-14 00:10:00', '299', '2023-07-08', 'Fêmea', 4.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '300', '2025-03-24', 'Fêmea', 0.00, 106, 26, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '301', '2025-04-06', 'Fêmea', 0.00, 102, 35, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:22:53', '302', '2025-04-08', 'Fêmea', 0.00, 102, NULL, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '303', '2025-04-08', 'Fêmea', 0.00, 102, 34, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '304', '2025-04-09', 'Fêmea', 0.00, 102, 19, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '305', '2025-04-05', 'Fêmea', 0.00, 108, 16, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '306', '2025-04-20', 'Fêmea', 0.00, 102, 37, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '307', '2025-05-18', 'Fêmea', 0.00, 108, 28, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '308', '2025-05-24', 'Fêmea', 0.00, 109, 47, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'Bezerra', 'Vazia', 0, 'Não', '', 0, '2025-06-17 10:22:53', '2025-07-06 11:00:42', '309', '2025-05-24', 'Fêmea', 1.00, NULL, NULL, 0, 'Importado via CSV', 'Holandês', 'Deprecated:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated', NULL, NULL, NULL, NULL, NULL, NULL),
(63, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '310', '2025-05-25', 'Fêmea', 0.00, 110, 46, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '311', '2025-05-28', 'Fêmea', 0.00, 109, 41, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '312', '2025-05-28', 'Fêmea', 0.00, 109, 4, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '313', '2025-05-29', 'Fêmea', 0.00, 111, 10, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-06-17 10:22:53', '2025-06-17 10:28:41', '314', '2025-05-30', 'Fêmea', 0.00, 102, 18, 0, 'Importado via CSV', 'Holandês', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-07-08 16:33:15', '2025-07-08 16:33:15', '315', '2025-06-27', 'Fêmea', 0.00, 108, 32, 0, 'Filhote do parto da vaca 265 em 27/06/2025', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-07-11 15:26:34', '2025-07-11 15:26:34', '317', '2025-07-09', 'Fêmea', 0.00, 108, 22, 0, 'Filhote do parto da vaca 242 em 09/07/2025', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-07-11 15:29:55', '2025-07-11 15:29:55', '316', '2025-07-11', 'Fêmea', 0.00, 111, 40, 0, 'Filhote do parto da vaca 277 em 11/07/2025', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'Bezerra', 'Vazia', 0, 'Sim', '', 1, '2025-07-17 21:57:03', '2025-07-18 13:32:00', '001', '2025-07-18', 'Fêmea', 1.00, NULL, NULL, 0, '', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'Bezerra', 'Vazia', 0, 'Sim', 'Verde', 1, '2025-07-17 21:57:14', '2025-07-19 22:00:47', '002', '2025-07-18', 'Fêmea', 1.00, NULL, NULL, 0, '', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(79, 'Bezerra', 'Vazia', 0, 'Não', 'Azul', 1, '2025-07-17 21:57:26', '2025-07-18 13:32:34', '003', '2025-07-18', 'Fêmea', 1.00, NULL, NULL, 0, '', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'Bezerra', 'Vazia', 0, 'Não', '', 1, '2025-07-17 21:57:36', '2025-07-17 21:57:36', '004', '2025-07-18', 'Fêmea', 0.00, NULL, NULL, 0, '', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'Bezerra', 'Vazia', 0, '', '', 1, '2025-07-18 10:13:45', '2025-07-18 10:13:45', '318', '2025-07-18', 'Fêmea', 0.00, 111, 29, 0, 'Filhote do parto da vaca 261 em 18/07/2025', 'Holandês', '', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `gado_fotos`
--

CREATE TABLE `gado_fotos` (
  `id` int(11) NOT NULL,
  `id_gado` int(11) NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `foto_principal` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `inseminacoes`
--

CREATE TABLE `inseminacoes` (
  `id` int(11) NOT NULL,
  `tipo` enum('IATF','Cio') NOT NULL DEFAULT 'IATF',
  `id_vaca` int(11) DEFAULT NULL,
  `data_inseminacao` date NOT NULL,
  `id_inseminador` int(11) DEFAULT NULL,
  `id_touro` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status_inseminacao` varchar(50) NOT NULL DEFAULT 'Aguardando Diagnostico',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `inseminacoes`
--

INSERT INTO `inseminacoes` (`id`, `tipo`, `id_vaca`, `data_inseminacao`, `id_inseminador`, `id_touro`, `observacoes`, `status_inseminacao`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'IATF', 11, '2021-08-28', 3, 103, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(2, 'IATF', 8, '2021-08-30', 3, 107, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(3, 'IATF', 10, '2021-09-01', 3, 103, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(4, 'IATF', 11, '2021-09-18', 3, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(5, 'IATF', 10, '2021-09-22', 3, 107, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(6, 'IATF', 12, '2022-01-08', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(7, 'IATF', 23, '2022-03-25', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(8, 'IATF', 5, '2022-04-03', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(9, 'IATF', 28, '2022-04-03', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(10, 'IATF', 3, '2022-05-04', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(11, 'IATF', 36, '2022-05-04', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(12, 'IATF', 17, '2022-06-25', 3, 105, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(13, 'IATF', 15, '2022-07-11', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(14, 'IATF', 22, '2022-07-25', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(15, 'IATF', 8, '2022-08-02', 3, 121, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(16, 'IATF', 9, '2022-08-11', 3, 104, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(17, 'IATF', 17, '2022-08-11', 3, 104, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(18, 'IATF', 8, '2022-08-23', 3, 122, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(19, 'IATF', 29, '2022-09-05', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(20, 'IATF', 11, '2022-09-10', 3, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(21, 'IATF', 10, '2022-09-10', 3, 104, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(22, 'IATF', 8, '2022-09-13', 3, 104, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(23, 'IATF', 11, '2022-09-26', 3, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(24, 'IATF', 11, '2022-10-03', 3, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(25, 'IATF', 25, '2022-10-11', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(26, 'IATF', 32, '2022-10-11', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(27, 'IATF', 27, '2022-10-11', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(28, 'IATF', 26, '2022-10-11', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(29, 'IATF', 8, '2022-10-30', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(30, 'IATF', 13, '2022-11-16', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(31, 'IATF', 12, '2022-12-18', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(32, 'IATF', 13, '2023-01-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(33, 'IATF', 12, '2023-01-11', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(34, 'IATF', 39, '2023-01-23', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(35, 'IATF', 6, '2023-01-25', 3, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(36, 'IATF', 28, '2023-02-08', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(37, 'IATF', 28, '2023-03-03', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(38, 'IATF', 39, '2023-03-04', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(39, 'IATF', 23, '2023-03-04', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(40, 'IATF', 5, '2023-03-04', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(41, 'IATF', 12, '2023-03-04', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(42, 'IATF', 40, '2023-03-18', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(43, 'IATF', 14, '2023-03-18', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(44, 'IATF', 23, '2023-04-23', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(45, 'IATF', 5, '2023-04-23', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(46, 'IATF', 3, '2023-04-23', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(47, 'IATF', 28, '2023-04-23', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(48, 'IATF', 36, '2023-04-28', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(49, 'IATF', 12, '2023-04-28', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(50, 'IATF', 39, '2023-04-28', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(51, 'IATF', 36, '2023-05-17', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(52, 'IATF', 40, '2023-05-18', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(53, 'IATF', 39, '2023-05-18', 3, NULL, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(54, 'IATF', 23, '2023-05-18', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(55, 'IATF', 23, '2023-06-08', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(56, 'IATF', 9, '2023-06-13', 3, 103, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(57, 'IATF', 32, '2023-06-14', 3, 103, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(58, 'IATF', 13, '2023-06-21', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(59, 'IATF', 5, '2023-06-23', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(60, 'IATF', 51, '2023-06-29', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(61, 'IATF', 22, '2023-07-01', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(62, 'IATF', 9, '2023-07-05', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(63, 'IATF', 5, '2023-07-13', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(64, 'IATF', 15, '2023-07-22', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(65, 'IATF', 17, '2023-07-22', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(66, 'IATF', 32, '2023-07-22', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(67, 'IATF', 36, '2023-07-22', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(68, 'IATF', 9, '2023-08-11', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(69, 'IATF', 40, '2023-08-26', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(70, 'IATF', 29, '2023-08-30', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(71, 'IATF', 10, '2023-08-30', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(72, 'IATF', 32, '2023-08-30', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(73, 'IATF', 28, '2023-08-30', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(74, 'IATF', 22, '2023-08-30', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(75, 'IATF', 27, '2023-08-31', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(76, 'IATF', 11, '2023-09-01', 3, 112, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(77, 'IATF', 8, '2023-09-02', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(78, 'IATF', 9, '2023-09-13', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(79, 'IATF', 15, '2023-09-14', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(80, 'IATF', 25, '2023-09-14', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(81, 'IATF', 36, '2023-09-14', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(82, 'IATF', 8, '2023-09-15', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(83, 'IATF', 32, '2023-09-18', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(84, 'IATF', 51, '2023-09-29', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(85, 'IATF', 26, '2023-09-29', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(86, 'IATF', 49, '2023-09-29', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(87, 'IATF', 22, '2023-10-07', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(88, 'IATF', 27, '2023-10-30', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(89, 'IATF', 13, '2023-10-30', 3, 112, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(90, 'IATF', 15, '2023-10-30', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(91, 'IATF', 40, '2023-10-30', 3, 112, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(92, 'IATF', 49, '2023-11-17', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(93, 'IATF', 51, '2023-11-17', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(94, 'IATF', 26, '2023-11-21', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(95, 'IATF', 6, '2023-12-06', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(96, 'IATF', 26, '2024-01-11', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(97, 'IATF', 15, '2024-01-13', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(98, 'IATF', 14, '2024-02-28', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(99, 'IATF', 3, '2024-02-28', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(100, 'IATF', 15, '2024-02-28', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(101, 'IATF', 26, '2024-03-16', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(102, 'IATF', 12, '2024-03-29', 3, 115, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(103, 'IATF', 39, '2024-04-25', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(104, 'IATF', 26, '2024-04-29', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(105, 'IATF', 15, '2024-04-29', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(106, 'IATF', 14, '2024-04-29', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(107, 'IATF', 3, '2024-04-29', 3, 115, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(108, 'IATF', 23, '2024-05-06', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(109, 'IATF', 14, '2024-05-22', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(110, 'IATF', 3, '2024-05-24', 3, 115, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(111, 'IATF', 46, '2024-05-25', 3, 110, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(112, 'IATF', 23, '2024-05-26', 3, 115, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(113, 'IATF', 33, '2024-06-11', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(114, 'IATF', 5, '2024-06-12', 3, 115, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(115, 'IATF', 14, '2024-06-17', 3, 115, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(116, 'IATF', 26, '2024-06-23', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(117, 'IATF', 17, '2024-06-23', 3, 115, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(118, 'IATF', 15, '2024-06-23', 3, 106, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(119, 'IATF', 37, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(120, 'IATF', 38, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(121, 'IATF', 19, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(122, 'IATF', 41, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(123, 'IATF', 16, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(124, 'IATF', 18, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(125, 'IATF', 34, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(126, 'IATF', 47, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(127, 'IATF', 35, '2024-07-09', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(128, 'IATF', 47, '2024-08-22', 3, 109, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(129, 'IATF', 4, '2024-08-22', 3, 109, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(130, 'IATF', 48, '2024-08-22', 3, 108, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:40'),
(131, 'IATF', 38, '2024-08-22', 3, 108, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:40'),
(132, 'IATF', 16, '2024-08-22', 3, 108, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(133, 'IATF', 41, '2024-08-22', 3, 109, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(134, 'IATF', 33, '2024-08-22', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(135, 'IATF', 15, '2024-08-24', 3, 108, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(136, 'IATF', 12, '2024-08-24', 3, 109, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(137, 'IATF', 10, '2024-08-24', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(138, 'IATF', 9, '2024-08-24', 3, 109, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(139, 'IATF', 40, '2024-08-24', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(140, 'IATF', 36, '2024-08-24', 3, 109, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(141, 'IATF', 32, '2024-08-24', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(142, 'IATF', 29, '2024-08-24', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(143, 'IATF', 28, '2024-08-24', 3, 108, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(144, 'IATF', 25, '2024-08-24', 3, 108, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(145, 'IATF', 14, '2024-08-24', 3, 109, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(146, 'IATF', 14, '2024-10-05', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(147, 'IATF', 13, '2024-10-05', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(148, 'IATF', 12, '2024-10-05', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(149, 'IATF', 9, '2024-10-05', 3, 108, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:40'),
(150, 'IATF', 8, '2024-10-05', 3, 108, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(151, 'IATF', 40, '2024-10-05', 3, 111, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:40'),
(152, 'IATF', 32, '2024-10-05', 3, 108, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:40'),
(153, 'IATF', 29, '2024-10-05', 3, 111, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:40'),
(154, 'IATF', 27, '2024-10-05', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(155, 'IATF', 36, '2024-10-05', 3, 108, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(156, 'IATF', 25, '2024-10-05', 3, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(157, 'IATF', 22, '2024-10-05', 3, 108, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:40'),
(158, 'IATF', 49, '2024-11-18', 3, 123, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(159, 'IATF', 12, '2024-11-18', 3, 124, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(160, 'IATF', 14, '2024-11-18', 3, 124, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(161, 'IATF', 36, '2024-11-18', 3, 123, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(162, 'IATF', 13, '2024-11-18', 3, 123, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(163, 'IATF', 8, '2024-11-18', 3, 123, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:10', '2025-06-17 14:21:34'),
(164, 'IATF', 6, '2024-11-18', 3, 123, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(165, 'IATF', 27, '2024-11-18', 3, 124, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(166, 'IATF', 51, '2024-11-18', 3, 124, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(167, 'IATF', 33, '2024-11-18', 3, 109, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(168, 'IATF', 20, '2024-11-18', 3, 108, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(169, 'IATF', 11, '2024-11-18', 3, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(170, 'IATF', 13, '2024-12-28', 3, 117, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(171, 'IATF', 49, '2024-12-28', 3, 102, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(172, 'IATF', 36, '2024-12-28', 3, 117, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(173, 'IATF', 20, '2024-12-28', 3, 125, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(174, 'IATF', 14, '2024-12-28', 3, 117, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(175, 'IATF', 12, '2024-12-28', 3, 117, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(176, 'IATF', 11, '2024-12-28', 3, 117, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(177, 'IATF', 8, '2024-12-28', 3, 117, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(178, 'IATF', 8, '2025-02-06', 1, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(179, 'IATF', 13, '2025-02-06', 2, 117, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(180, 'IATF', 11, '2025-02-06', 1, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(181, 'IATF', 20, '2025-02-06', 1, 126, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(182, 'IATF', 24, '2025-02-06', 1, 126, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(183, 'IATF', 43, '2025-02-06', 1, 126, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(184, 'IATF', 49, '2025-02-06', 1, 127, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(185, 'IATF', 36, '2025-02-06', 1, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(186, 'IATF', 25, '2025-02-06', 2, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(187, 'IATF', 14, '2025-02-06', 1, 113, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(188, 'IATF', 52, '2025-02-07', 1, 126, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(189, 'IATF', 7, '2025-02-07', 1, 126, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(190, 'IATF', 42, '2025-02-07', 1, 126, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(191, 'IATF', 24, '2025-04-01', 2, 128, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(192, 'IATF', 52, '2025-04-01', 1, 128, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(193, 'IATF', 43, '2025-04-01', 1, 128, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(194, 'IATF', 42, '2025-04-01', 1, 128, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(195, 'IATF', 25, '2025-04-03', 1, 129, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(196, 'IATF', 39, '2025-04-03', 1, 130, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(197, 'IATF', 14, '2025-04-03', 1, 130, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(198, 'IATF', 11, '2025-04-03', 1, 130, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(199, 'IATF', 49, '2025-04-03', 3, 129, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(200, 'IATF', 36, '2025-04-03', 3, 129, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(201, 'IATF', 49, '2025-05-06', 1, 110, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(202, 'IATF', 25, '2025-05-09', 1, 110, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(203, 'IATF', 13, '2025-05-11', 2, 110, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:40'),
(204, 'IATF', 50, '2025-05-11', 1, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(205, 'IATF', 5, '2025-05-12', 1, 130, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(206, 'IATF', 26, '2025-05-14', 1, 110, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(207, 'IATF', 36, '2025-05-15', 1, 110, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(208, 'IATF', 51, '2025-05-15', 1, 110, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(209, 'IATF', 52, '2025-05-29', 1, 131, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(210, 'IATF', 14, '2025-05-29', 2, 131, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(211, 'IATF', 42, '2025-05-29', 1, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(212, 'IATF', 30, '2025-05-29', 1, 111, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(213, 'IATF', 16, '2025-05-29', 1, 131, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(214, 'IATF', 17, '2025-05-29', 1, 129, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(215, 'IATF', 23, '2025-05-29', 2, 129, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(216, 'IATF', 31, '2025-05-29', 1, 131, 'Importado via CSV', 'Confirmada (Prenha)', 1, '2025-06-17 12:45:11', '2025-06-30 14:37:09'),
(217, 'IATF', 35, '2025-05-29', 1, 131, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(218, 'IATF', 3, '2025-05-29', 1, 131, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(219, 'IATF', 11, '2025-05-29', 1, 110, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(220, 'IATF', 27, '2025-05-30', 2, 129, 'Importado via CSV', 'Aguardando Diagnostico', 1, '2025-06-17 12:45:11', '2025-06-17 14:21:34'),
(221, 'Cio', 14, '2025-06-19', 1, 131, '', 'Aguardando Diagnostico', 1, '2025-06-19 07:44:24', '2025-06-19 07:44:24'),
(222, 'Cio', 16, '2025-06-19', 1, 131, '', 'Aguardando Diagnostico', 1, '2025-06-19 07:44:46', '2025-06-19 17:37:00'),
(224, 'Cio', 26, '2025-06-20', 1, 126, '', 'Aguardando Diagnostico', 1, '2025-06-20 18:18:54', '2025-06-20 18:18:54'),
(225, 'IATF', 19, '2025-06-20', 1, 131, '', 'Aguardando Diagnostico', 1, '2025-06-20 18:19:29', '2025-06-20 18:19:29'),
(226, 'IATF', 37, '2025-06-20', 1, 131, 'Não achado entrada cerco e houve sangramento vaginal', 'Aguardando Diagnostico', 1, '2025-06-20 19:22:21', '2025-06-20 19:22:21'),
(227, 'IATF', 3, '2025-07-25', 3, 132, '', 'Aguardando Diagnostico', 1, '2025-07-25 17:57:00', '2025-07-25 17:57:00'),
(228, 'IATF', 47, '2025-07-25', 3, 131, '', 'Aguardando Diagnostico', 1, '2025-07-25 18:00:55', '2025-07-25 18:00:55'),
(229, 'IATF', 16, '2025-07-25', 3, 131, '', 'Aguardando Diagnostico', 1, '2025-07-25 18:05:36', '2025-07-25 18:05:36'),
(230, 'IATF', 41, '2025-07-25', 3, 131, '', 'Aguardando Diagnostico', 1, '2025-07-25 18:11:13', '2025-07-25 18:11:13'),
(231, 'IATF', 4, '2025-07-25', 3, 131, '', 'Aguardando Diagnostico', 1, '2025-07-25 18:16:53', '2025-07-25 18:16:53'),
(232, 'IATF', 5, '2025-07-25', 3, 132, '', 'Aguardando Diagnostico', 1, '2025-07-25 18:20:38', '2025-07-25 18:20:38'),
(233, 'IATF', 10, '2025-07-25', 3, 132, '', 'Aguardando Diagnostico', 1, '2025-07-25 18:28:20', '2025-07-25 18:28:20'),
(234, 'IATF', 49, '2025-07-25', 3, 131, '', 'Aguardando Diagnostico', 1, '2025-07-25 18:34:45', '2025-07-25 18:34:45'),
(235, 'IATF', 17, '2025-07-28', 3, 133, '', 'Aguardando Diagnostico', 1, '2025-07-28 22:25:44', '2025-07-28 22:25:44'),
(236, 'IATF', 15, '2025-07-28', 3, 134, '', 'Aguardando Diagnostico', 1, '2025-07-28 22:26:06', '2025-07-28 22:26:06'),
(237, 'IATF', 36, '2025-07-28', 3, 134, '', 'Aguardando Diagnostico', 1, '2025-07-28 22:26:25', '2025-07-28 22:26:25'),
(238, 'IATF', 51, '2025-07-28', 3, 134, '', 'Aguardando Diagnostico', 1, '2025-07-28 22:26:44', '2025-07-28 22:26:44'),
(239, 'IATF', 46, '2025-07-28', 3, 134, '', 'Aguardando Diagnostico', 1, '2025-07-28 22:27:02', '2025-07-28 22:27:02'),
(240, 'IATF', 35, '2025-07-28', 3, 134, '', 'Aguardando Diagnostico', 1, '2025-07-28 22:27:19', '2025-07-28 22:27:19'),
(241, 'IATF', 11, '2025-07-28', 3, 133, '', 'Aguardando Diagnostico', 1, '2025-07-28 22:27:41', '2025-07-28 22:27:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `inseminadores`
--

CREATE TABLE `inseminadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `inseminadores`
--

INSERT INTO `inseminadores` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Michel', 1, '2025-06-06 21:05:15', '2025-06-06 21:13:37'),
(2, 'Marcelo', 1, '2025-06-06 21:05:15', '2025-06-06 21:13:45'),
(3, 'Jeferson', 1, '2025-06-06 21:05:15', '2025-06-06 21:13:54'),
(4, 'Dione', 1, '2025-06-17 12:01:27', '2025-06-17 12:53:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `manejos`
--

CREATE TABLE `manejos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo` enum('BST','Diagnóstico','Protocolo de Saúde','Vacinas','Secagem','Pré-Parto') NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `recorrencia_meses` int(11) DEFAULT NULL,
  `recorrencia_dias` int(11) DEFAULT NULL COMMENT 'Intervalo em dias para recorrência de BST',
  `evento_dias_1` int(11) DEFAULT NULL,
  `evento_titulo_1` varchar(255) DEFAULT NULL,
  `evento_dias_2` int(11) DEFAULT NULL,
  `evento_titulo_2` varchar(255) DEFAULT NULL,
  `evento_dias_3` int(11) DEFAULT NULL,
  `evento_titulo_3` varchar(255) DEFAULT NULL,
  `evento_dias_4` int(11) DEFAULT NULL,
  `evento_titulo_4` varchar(255) DEFAULT NULL,
  `evento_dias_5` int(11) DEFAULT NULL,
  `evento_titulo_5` varchar(255) DEFAULT NULL,
  `evento_dias_6` int(11) DEFAULT NULL,
  `evento_titulo_6` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `manejos`
--

INSERT INTO `manejos` (`id`, `nome`, `tipo`, `ativo`, `recorrencia_meses`, `recorrencia_dias`, `evento_dias_1`, `evento_titulo_1`, `evento_dias_2`, `evento_titulo_2`, `evento_dias_3`, `evento_titulo_3`, `evento_dias_4`, `evento_titulo_4`, `evento_dias_5`, `evento_titulo_5`, `evento_dias_6`, `evento_titulo_6`) VALUES
(1, 'BST', 'BST', 1, NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Mastite', 'Vacinas', 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'IATF', 'Protocolo de Saúde', 1, NULL, NULL, 0, 'Implante + 2,5ml ricbe', 8, '5ml lutalyse + 0.75 de ECP e retirar implante', 10, 'Inseminar', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Tristeza', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'teste', 'Secagem', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Cepravim + 1PPU', 'Secagem', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Aborto', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Casco', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'Cetose', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'Cisto', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'Dermatite', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'Dermatite Interdigital', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Deslocamento de Abomaso', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Diarreia', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'Endometrite', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Feto Mumificado', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Indigestão', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'Infecção de Umbigo', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Infecção Uterina', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Intoxicação', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'Laminite', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'Leptospirose', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'Lesão de Jarrete', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'Metrite', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'Otite', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'Pneumonia', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'Reabsorção Embrionária', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'Retenção de Placenta', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'Tumor', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Ph de Urina', 'Diagnóstico', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'Pour On', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'Clostridioses', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'IBR', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'Leptospirose', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'Revisao Ordenha', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'Teste Tuberculose', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'Raiva', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'Brucelose', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'Carbunculo', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'Vermífugo injetavel novilhas', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'Anti-inflamatório 3 dias', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'Antibiótico + Anti-inflamátorio 3 dias', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Anti-inflamatório 5 dias', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'Antibiótico 3 dias', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'Antibiótico 4 dias', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'Antibiótico 5 dias', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'Desmame', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Brucelose', 'Vacinas', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'Mochar', 'Protocolo de Saúde', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `partos`
--

CREATE TABLE `partos` (
  `id` int(11) NOT NULL,
  `id_vaca` int(11) NOT NULL,
  `id_touro` int(11) DEFAULT NULL,
  `sexo_cria` enum('Aborto','Indução','Macho','Fêmea','Gêmeos Machos','Gêmeos Fêmea','Gêmeos macho e fêmea') NOT NULL,
  `data_parto` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `partos`
--

INSERT INTO `partos` (`id`, `id_vaca`, `id_touro`, `sexo_cria`, `data_parto`, `observacoes`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 17, 106, 'Fêmea', '2024-04-23', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(2, 13, 112, 'Fêmea', '2024-08-03', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(3, 27, 102, 'Macho', '2024-08-07', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(4, 29, 106, 'Fêmea', '2024-06-05', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(5, 10, 106, 'Macho', '2024-06-05', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(6, 28, 106, 'Macho', '2024-06-05', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(7, 8, 102, 'Macho', '2024-06-11', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(8, 49, 102, 'Fêmea', '2024-08-19', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(9, 11, 113, 'Fêmea', '2022-07-05', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(10, 11, 112, 'Macho', '2024-06-13', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(11, 36, 102, 'Fêmea', '2024-06-13', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(12, 9, 102, 'Fêmea', '2024-06-17', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(13, 10, 107, 'Fêmea', '2022-07-09', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(14, 8, 107, 'Macho', '2022-06-11', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(15, 51, 102, 'Fêmea', '2024-08-21', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(16, 14, NULL, 'Fêmea', '2023-12-18', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(17, 3, 106, 'Fêmea', '2023-12-08', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(18, 12, NULL, 'Macho', '2022-10-06', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(19, 25, 102, 'Fêmea', '2024-06-21', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(20, 32, 102, 'Macho', '2024-06-22', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(21, 5, 103, 'Macho', '2018-11-19', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(22, 5, 103, 'Macho', '2019-12-14', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(23, 5, 103, 'Macho', '2022-02-01', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(24, 29, 103, 'Macho', '2019-02-20', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(25, 29, 103, 'Macho', '2020-03-16', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(26, 29, 103, 'Macho', '2021-04-10', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(27, 29, 103, 'Macho', '2022-05-05', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(28, 22, 103, 'Macho', '2021-12-25', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(29, 23, 103, 'Macho', '2021-12-11', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(30, 32, 103, 'Macho', '2022-03-23', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(31, 3, 103, 'Macho', '2018-12-25', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(32, 3, 103, 'Macho', '2020-01-19', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(33, 3, 103, 'Macho', '2021-02-12', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(34, 3, 103, 'Macho', '2022-03-09', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(35, 27, 103, 'Macho', '2022-07-27', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(36, 25, 103, 'Macho', '2022-08-15', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(37, 26, 103, 'Macho', '2022-05-13', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(38, 28, 103, 'Macho', '2022-01-12', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(39, 22, 102, 'Fêmea', '2024-07-12', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(40, 29, NULL, 'Macho', '2023-06-16', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(41, 23, NULL, 'Macho', '2022-12-24', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(42, 5, 114, 'Fêmea', '2022-12-31', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(43, 36, NULL, 'Fêmea', '2023-01-28', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(44, 6, 106, 'Macho', '2024-09-15', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(45, 3, NULL, 'Macho', '2023-02-09', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(46, 39, 102, 'Macho', '2021-01-14', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(47, 39, 102, 'Macho', '2022-02-28', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(48, 15, NULL, 'Fêmea', '2023-04-09', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(49, 22, NULL, 'Macho', '2023-05-02', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(50, 9, 104, 'Macho', '2023-05-15', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(51, 17, 104, 'Fêmea', '2023-05-16', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(52, 39, 102, 'Macho', '2025-01-22', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(53, 10, 104, 'Macho', '2023-06-21', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(54, 11, 113, 'Fêmea', '2023-07-13', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(55, 27, NULL, 'Macho', '2023-07-13', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(56, 25, NULL, 'Fêmea', '2023-07-17', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(57, 26, NULL, 'Macho', '2023-07-27', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(58, 8, 106, 'Fêmea', '2023-07-31', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(59, 6, 113, 'Macho', '2023-10-01', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(60, 23, 115, 'Macho', '2025-03-11', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(61, 3, 115, 'Macho', '2025-02-27', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(62, 17, 115, 'Macho', '2025-04-02', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(63, 34, 102, 'Fêmea', '2025-04-08', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(64, 35, 102, 'Fêmea', '2025-04-06', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(65, 19, 102, 'Fêmea', '2025-04-09', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(66, 12, 102, 'Macho', '2024-01-24', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(67, 16, 108, 'Fêmea', '2025-04-05', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(68, 39, NULL, 'Macho', '2024-02-23', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(69, 23, 102, 'Fêmea', '2024-03-06', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(70, 5, 102, 'Macho', '2024-04-12', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(71, 5, 115, 'Macho', '2025-03-13', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(72, 28, 108, '', '2025-05-18', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(73, 37, 102, 'Fêmea', '2025-04-20', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(74, 26, 106, 'Fêmea', '2025-03-24', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(75, 15, 108, 'Fêmea', '2025-05-24', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(76, 47, 109, 'Fêmea', '2025-05-24', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(77, 46, 110, 'Fêmea', '2025-05-25', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(78, 41, 109, 'Fêmea', '2025-05-28', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(79, 4, 109, 'Fêmea', '2025-05-28', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(80, 10, 111, 'Fêmea', '2025-05-29', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(81, 18, 102, 'Fêmea', '2025-05-30', 'Importado via CSV.', 1, '2025-06-17 11:57:07', '2025-06-17 11:57:07'),
(83, 9, 108, 'Macho', '2025-06-26', '', 1, '2025-06-29 11:49:51', '2025-06-29 11:49:51'),
(84, 32, 108, 'Fêmea', '2025-06-27', '', 1, '2025-07-02 14:06:30', '2025-07-02 14:06:30'),
(85, 22, 108, 'Fêmea', '2025-07-09', '', 1, '2025-07-11 15:26:13', '2025-07-11 15:26:13'),
(86, 40, 111, 'Fêmea', '2025-07-11', '', 1, '2025-07-11 15:29:42', '2025-07-11 15:29:42'),
(87, 29, 111, 'Fêmea', '2025-07-18', '', 1, '2025-07-18 08:14:43', '2025-07-18 08:14:43');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pesagens`
--

CREATE TABLE `pesagens` (
  `id` int(11) NOT NULL,
  `id_gado` int(11) NOT NULL,
  `peso` int(11) NOT NULL,
  `data_pesagem` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pesagens`
--

INSERT INTO `pesagens` (`id`, `id_gado`, `peso`, `data_pesagem`, `observacoes`, `created_at`, `updated_at`) VALUES
(1, 1, 84, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(2, 1, 93, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(3, 1, 173, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(4, 2, 84, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(5, 2, 108, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(6, 2, 188, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(7, 4, 144, '2023-09-18', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(8, 4, 355, '2024-06-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(9, 7, 90, '2023-09-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(10, 7, 355, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(11, 7, 462, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(12, 9, 179, '2021-11-25', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(13, 9, 183, '2021-12-28', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(14, 9, 192, '2022-01-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(15, 9, 198, '2022-02-23', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(16, 9, 249, '2022-04-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(17, 9, 299, '2022-05-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(18, 9, 321, '2022-06-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(19, 9, 379, '2022-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(20, 9, 385, '2022-08-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(21, 9, 434, '2022-09-29', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(22, 13, 181, '2021-11-25', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(23, 13, 188, '2021-12-28', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(24, 13, 192, '2022-01-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(25, 13, 208, '2022-02-23', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(26, 13, 243, '2022-04-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(27, 13, 252, '2022-05-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(28, 13, 275, '2022-06-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(29, 13, 306, '2022-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(30, 13, 321, '2022-08-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(31, 13, 349, '2022-09-29', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(32, 13, 403, '2022-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(33, 13, 483, '2023-03-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(34, 13, 504, '2023-06-12', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(35, 14, 35, '2021-11-23', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(36, 14, 76, '2022-01-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(37, 14, 79, '2022-02-23', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(38, 14, 102, '2022-03-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(39, 14, 124, '2022-04-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(40, 14, 158, '2022-05-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(41, 14, 188, '2022-06-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(42, 14, 217, '2022-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(43, 14, 221, '2022-08-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(44, 14, 257, '2022-09-29', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(45, 14, 285, '2022-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(46, 14, 397, '2023-03-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(47, 16, 82, '2023-05-04', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(48, 16, 95, '2023-05-26', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(49, 16, 144, '2023-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(50, 16, 170, '2023-09-18', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(51, 16, 189, '2023-10-26', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(52, 16, 385, '2024-06-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(53, 16, 490, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(54, 17, 252, '2021-11-25', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(55, 17, 261, '2021-12-28', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(56, 17, 273, '2022-01-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(57, 17, 287, '2022-02-23', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(58, 17, 338, '2022-04-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(59, 17, 385, '2022-05-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(60, 17, 424, '2022-08-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(61, 17, 465, '2022-09-29', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(62, 17, 555, '2023-03-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(63, 18, 58, '2023-05-05', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(64, 18, 62, '2023-05-20', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(65, 18, 141, '2023-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(66, 18, 155, '2023-09-18', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(67, 18, 391, '2024-06-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(68, 18, 476, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(69, 19, 385, '2022-01-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(70, 19, 504, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(71, 20, 76, '2023-07-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(72, 20, 217, '2023-10-03', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(73, 20, 385, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(74, 21, 130, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(75, 21, 166, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(76, 21, 229, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(77, 24, 62, '2023-05-05', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(78, 24, 151, '2023-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(79, 24, 188, '2023-09-18', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(80, 24, 373, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(81, 24, 469, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(82, 30, 265, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(83, 30, 266, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(84, 30, 349, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(85, 31, 208, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(86, 31, 301, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(87, 33, 243, '2022-07-19', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(88, 33, 275, '2023-10-26', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(89, 33, 449, '2024-06-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(90, 33, 577, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(91, 34, 585, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(92, 37, 50, '2022-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(93, 37, 64, '2022-08-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(94, 37, 90, '2022-09-29', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(95, 37, 124, '2022-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(96, 37, 166, '2023-03-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(97, 37, 204, '2023-04-06', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(98, 37, 210, '2023-05-05', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(99, 37, 192, '2023-06-12', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(100, 37, 271, '2023-09-18', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(101, 37, 511, '2024-06-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(102, 37, 630, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(103, 38, 410, '2023-01-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(104, 38, 600, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(105, 40, 360, '2022-09-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(106, 40, 442, '2023-03-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(107, 40, 592, '2023-09-19', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(108, 41, 688, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(109, 42, 104, '2023-10-03', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(110, 42, 349, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(111, 42, 366, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(112, 42, 483, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(113, 43, 80, '2023-07-31', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(114, 43, 321, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(115, 43, 355, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(116, 43, 416, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(117, 44, 130, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(118, 44, 151, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(119, 44, 271, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(120, 45, 130, '2024-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(121, 45, 155, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(122, 45, 257, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(123, 47, 424, '2022-01-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(124, 47, 511, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(125, 48, 373, '2022-01-11', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(126, 48, 504, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(127, 49, 311, '2023-07-10', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(128, 49, 410, '2023-09-18', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(129, 51, 39, '2022-03-23', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(130, 51, 43, '2022-03-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(131, 51, 58, '2022-04-07', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(132, 51, 65, '2022-04-23', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(133, 51, 62, '2022-04-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(134, 51, 90, '2022-05-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(135, 51, 102, '2022-06-27', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(136, 51, 117, '2022-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(137, 51, 148, '2022-08-30', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(138, 51, 173, '2022-09-29', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(139, 51, 204, '2022-11-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(140, 51, 290, '2023-03-08', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(141, 51, 379, '2023-06-12', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(142, 51, 469, '2023-09-18', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(143, 52, 344, '2024-12-17', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(144, 52, 435, '2025-03-24', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(145, 4, 66, '2023-05-05', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33'),
(146, 4, 141, '2023-08-02', 'Importado via CSV', '2025-06-17 18:57:33', '2025-06-17 18:57:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `producao_leite`
--

CREATE TABLE `producao_leite` (
  `id` int(11) NOT NULL,
  `id_gado` int(11) NOT NULL,
  `data_producao` date NOT NULL,
  `ordenha_1` decimal(5,2) NOT NULL DEFAULT 0.00,
  `ordenha_2` decimal(5,2) NOT NULL DEFAULT 0.00,
  `ordenha_3` decimal(5,2) NOT NULL DEFAULT 0.00,
  `producao_total` decimal(6,2) NOT NULL DEFAULT 0.00,
  `observacoes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `producao_leite`
--

INSERT INTO `producao_leite` (`id`, `id_gado`, `data_producao`, `ordenha_1`, `ordenha_2`, `ordenha_3`, `producao_total`, `observacoes`, `created_at`, `updated_at`) VALUES
(2, 11, '2025-06-22', 17.50, 0.00, 0.00, 17.50, '', '2025-06-22 11:07:44', '2025-06-22 11:07:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `registros_manejos`
--

CREATE TABLE `registros_manejos` (
  `id` int(11) NOT NULL,
  `id_manejo` int(11) NOT NULL,
  `id_gado` int(11) DEFAULT NULL,
  `aplicado_rebanho` tinyint(1) NOT NULL DEFAULT 0,
  `data_aplicacao` date NOT NULL,
  `observacoes` text DEFAULT NULL,
  `id_evento_calendario` varchar(255) DEFAULT NULL,
  `calendar_event_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `registros_manejos`
--

INSERT INTO `registros_manejos` (`id`, `id_manejo`, `id_gado`, `aplicado_rebanho`, `data_aplicacao`, `observacoes`, `id_evento_calendario`, `calendar_event_id`, `created_at`) VALUES
(1, 7, 6, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(2, 7, 7, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(3, 7, 9, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(4, 7, 12, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(5, 7, 13, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(6, 7, 22, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(7, 7, 24, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(8, 7, 25, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(9, 7, 29, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(10, 7, 32, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(11, 7, 33, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(12, 7, 38, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(13, 7, 39, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(14, 7, 40, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(15, 7, 43, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(16, 7, 48, 0, '2025-06-17', 'Eventos gerados automaticamente pelo script de workflow retroativo.', NULL, NULL, '2025-06-17 17:40:32'),
(17, 3, 19, 0, '2025-06-10', '', NULL, NULL, '2025-06-17 17:50:54'),
(18, 3, 37, 0, '2025-06-10', '', NULL, NULL, '2025-06-17 17:51:47'),
(19, 1, NULL, 1, '2025-06-18', '', NULL, NULL, '2025-06-18 12:01:47'),
(20, 2, NULL, 1, '2025-06-09', '', NULL, NULL, '2025-06-18 18:40:54'),
(21, 34, NULL, 1, '2025-05-20', '', NULL, NULL, '2025-06-18 18:41:25'),
(22, 42, NULL, 1, '2025-06-24', '', NULL, NULL, '2025-06-18 18:42:03'),
(23, 35, NULL, 1, '2025-09-24', '', NULL, NULL, '2025-06-18 18:42:42'),
(24, 33, NULL, 1, '2024-10-11', '', NULL, NULL, '2025-06-18 18:44:15'),
(28, 49, 53, 0, '2025-05-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(29, 50, 53, 0, '2025-06-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(30, 51, 53, 0, '2025-06-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(31, 49, 54, 0, '2025-06-06', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(32, 50, 54, 0, '2025-07-06', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(33, 51, 54, 0, '2025-07-06', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(34, 49, 55, 0, '2025-06-08', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(35, 50, 55, 0, '2025-07-08', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(36, 51, 55, 0, '2025-07-08', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(37, 49, 56, 0, '2025-06-08', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(38, 50, 56, 0, '2025-07-08', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(39, 51, 56, 0, '2025-07-08', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(40, 49, 57, 0, '2025-06-09', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(41, 50, 57, 0, '2025-07-09', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(42, 51, 57, 0, '2025-07-09', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(43, 49, 58, 0, '2025-06-05', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(44, 50, 58, 0, '2025-07-05', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(45, 51, 58, 0, '2025-07-05', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(46, 49, 59, 0, '2025-06-20', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(47, 50, 59, 0, '2025-07-20', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(48, 51, 59, 0, '2025-07-20', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(49, 49, 60, 0, '2025-07-18', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(50, 50, 60, 0, '2025-08-18', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(51, 51, 60, 0, '2025-08-18', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(52, 49, 61, 0, '2025-07-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(53, 50, 61, 0, '2025-08-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(54, 51, 61, 0, '2025-08-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(55, 49, 62, 0, '2025-07-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(56, 50, 62, 0, '2025-08-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(57, 51, 62, 0, '2025-08-24', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(58, 49, 63, 0, '2025-07-25', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(59, 50, 63, 0, '2025-08-25', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(60, 51, 63, 0, '2025-08-25', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(61, 49, 64, 0, '2025-07-28', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(62, 50, 64, 0, '2025-08-28', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(63, 51, 64, 0, '2025-08-28', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(64, 49, 65, 0, '2025-07-28', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(65, 50, 65, 0, '2025-08-28', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(66, 51, 65, 0, '2025-08-28', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(67, 49, 66, 0, '2025-07-29', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(68, 50, 66, 0, '2025-08-29', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(69, 51, 66, 0, '2025-08-29', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(70, 49, 67, 0, '2025-07-30', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(71, 50, 67, 0, '2025-08-30', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(72, 51, 67, 0, '2025-08-30', 'Agendamento automático via script retroativo.', NULL, NULL, '2025-06-19 02:16:49'),
(74, 7, 31, 0, '2025-06-30', 'Diagnóstico de gestação positivo. Eventos futuros gerados automaticamente.', NULL, NULL, '2025-06-30 17:37:09'),
(75, 49, 74, 0, '2025-08-27', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-08 19:33:15'),
(76, 50, 74, 0, '2025-09-27', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-08 19:33:15'),
(77, 51, 74, 0, '2025-09-27', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-08 19:33:15'),
(78, 49, 75, 0, '2025-09-09', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-11 18:26:34'),
(79, 50, 75, 0, '2025-10-09', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-11 18:26:34'),
(80, 51, 75, 0, '2025-10-09', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-11 18:26:34'),
(81, 49, 76, 0, '2025-09-11', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-11 18:29:55'),
(82, 50, 76, 0, '2025-10-11', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-11 18:29:55'),
(83, 51, 76, 0, '2025-10-11', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-11 18:29:55'),
(84, 49, 77, 0, '2025-09-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:03'),
(85, 50, 77, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:03'),
(86, 51, 77, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:03'),
(87, 49, 78, 0, '2025-09-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:14'),
(88, 50, 78, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:14'),
(89, 51, 78, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:14'),
(90, 49, 79, 0, '2025-09-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:26'),
(91, 50, 79, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:26'),
(92, 51, 79, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:26'),
(93, 49, 80, 0, '2025-09-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:36'),
(94, 50, 80, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:36'),
(95, 51, 80, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 00:57:36'),
(96, 49, 81, 0, '2025-09-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 13:13:45'),
(97, 50, 81, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 13:13:45'),
(98, 51, 81, 0, '2025-10-18', 'Agendamento automático na criação do bezerro.', NULL, NULL, '2025-07-18 13:13:45'),
(99, 3, 3, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:33:01'),
(100, 3, 10, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:33:20'),
(101, 3, 41, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:33:35'),
(102, 3, 16, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:33:56'),
(103, 3, 5, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:34:14'),
(104, 3, 47, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:34:29'),
(105, 3, 4, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:34:43'),
(106, 3, 49, 0, '2025-07-17', '', NULL, NULL, '2025-07-18 13:34:58'),
(107, 3, 30, 0, '2025-07-18', '', NULL, NULL, '2025-07-18 13:35:14'),
(108, 3, 20, 0, '2025-07-18', '', NULL, NULL, '2025-07-18 13:35:33'),
(109, 3, 42, 0, '2025-07-18', '', NULL, NULL, '2025-07-18 13:35:46'),
(110, 3, 52, 0, '2025-07-18', '', NULL, NULL, '2025-07-18 13:36:00'),
(111, 3, 15, 0, '2025-07-19', '', NULL, NULL, '2025-07-18 13:36:20'),
(112, 3, 11, 0, '2025-07-19', '', NULL, NULL, '2025-07-18 13:36:34'),
(113, 3, 46, 0, '2025-07-19', '', NULL, NULL, '2025-07-18 13:36:54'),
(114, 3, 36, 0, '2025-07-19', '', NULL, NULL, '2025-07-18 13:37:09'),
(115, 3, 17, 0, '2025-07-19', '', NULL, NULL, '2025-07-18 13:37:29'),
(116, 3, 35, 0, '2025-07-19', '', NULL, NULL, '2025-07-18 13:37:52'),
(117, 3, 51, 0, '2025-07-19', '', NULL, NULL, '2025-07-18 13:38:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `touros`
--

CREATE TABLE `touros` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `raca` varchar(50) DEFAULT NULL,
  `codigo_semem` varchar(50) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `doses_estoque` int(11) NOT NULL DEFAULT 0 COMMENT 'Quantidade de doses de sêmen em estoque',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `touros`
--

INSERT INTO `touros` (`id`, `nome`, `raca`, `codigo_semem`, `observacoes`, `ativo`, `doses_estoque`, `created_at`, `updated_at`) VALUES
(102, 'Thanos', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:04:47'),
(103, 'Sem Touro', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:06:43'),
(104, 'caleb', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:07:01'),
(105, 'Didi', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:07:15'),
(106, 'spot lite', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:06:13'),
(107, 'Microsoft', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:08:31'),
(108, 'Havit', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:05:12'),
(109, 'Sousauce', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:05:37'),
(110, 'Soldado', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:02:32'),
(111, 'macaroni', 'Holandês', NULL, 'Importado via CSV', 1, 0, '2025-06-17 10:00:45', '2025-06-18 16:02:19'),
(112, 'austin', 'Angus', NULL, 'Importado via CSV de Partos', 1, 0, '2025-06-17 11:28:05', '2025-06-18 16:06:30'),
(113, 'Angus', 'Angus', NULL, 'Importado via CSV de Partos', 1, 0, '2025-06-17 11:28:05', '2025-06-18 16:03:54'),
(114, 'Touro', NULL, NULL, 'Importado via CSV de Partos', 1, 0, '2025-06-17 11:28:05', '2025-06-17 11:28:05'),
(115, 'destaque', 'Angus', NULL, 'Importado via CSV de Partos', 1, 0, '2025-06-17 11:28:05', '2025-06-18 16:05:59'),
(116, 'Je feidurardo', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:07:56'),
(117, 'Red Angus', 'Angus', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:04:23'),
(118, 'Sly', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:08:53'),
(119, 'Macchiato', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:08:12'),
(120, 'gimme', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:07:44'),
(121, 'Alberto', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:02:03'),
(122, 'elixer', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:07:28'),
(123, 'hall e oats', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:05:01'),
(124, 'ringwood', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:05:24'),
(125, 'Redeye', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:04:36'),
(126, 'Altadoubloos', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 5, '2025-06-17 12:12:59', '2025-06-26 09:43:01'),
(127, 'Novo Holandês', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:04:06'),
(128, 'Crossfire', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:03:23'),
(129, 'Lancaster', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:01:55'),
(130, 'Early BIRD', 'Angus', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:03:04'),
(131, 'Zoo', 'Holandês', NULL, 'Importado via CSV de Inseminações', 1, 0, '2025-06-17 12:12:59', '2025-06-18 16:02:48'),
(132, 'Fritlan ', 'Holandês', NULL, '', 1, 0, '2025-07-25 17:56:40', '2025-07-25 17:56:40'),
(133, 'Fritzlan', 'Holandês', NULL, '', 1, 10, '2025-07-28 22:20:28', '2025-07-28 22:23:33'),
(134, 'Maxville', 'Holandês', NULL, '', 1, 10, '2025-07-28 22:20:48', '2025-07-28 22:23:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `role`) VALUES
(3, 'admin', '2795aa46eea4f9d21bdce82e694c44df', 'admin'),
(4, 'michelthiel', '$2y$10$P5JG/0w3aAMVMkyfioZPRO5txRQVuYPGGe06IWSC8OmdtuAmAsfcS', 'admin'),
(5, 'marcelothiel', '$2y$10$KoFUIM4ckW/VIqwG8ZmFG.eyb7hFlilNmOIvDTN3dBpuCim/tv6bG', 'admin'),
(6, 'vacas', '$2y$10$NgMC5DHyilrKwr9feQOkj.n9zdkJkMnHWq4FFUoOYK1om/tA50KSK', 'user');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `dieta`
--
ALTER TABLE `dieta`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `envios_leite`
--
ALTER TABLE `envios_leite`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `fk_estoque_categoria` (`id_categoria_financeira`);

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_data_evento` (`data_evento`),
  ADD KEY `idx_id_vaca_evento` (`id_vaca`);

--
-- Índices de tabela `financeiro_categorias`
--
ALTER TABLE `financeiro_categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria_pai` (`parent_id`);

--
-- Índices de tabela `financeiro_contatos`
--
ALTER TABLE `financeiro_contatos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `financeiro_lancamentos`
--
ALTER TABLE `financeiro_lancamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lancamento_contato` (`id_contato`),
  ADD KEY `fk_lancamento_categoria` (`id_categoria`);

--
-- Índices de tabela `financeiro_lancamento_itens`
--
ALTER TABLE `financeiro_lancamento_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_lancamento` (`id_lancamento`),
  ADD KEY `fk_item_produto_estoque` (`id_produto_estoque`),
  ADD KEY `fk_item_categoria` (`id_categoria`);

--
-- Índices de tabela `financeiro_parcelas`
--
ALTER TABLE `financeiro_parcelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_parcela_lancamento` (`id_lancamento`);

--
-- Índices de tabela `gado`
--
ALTER TABLE `gado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brinco` (`brinco`),
  ADD KEY `fk_gado_id_pai` (`id_pai`),
  ADD KEY `fk_gado_id_mae` (`id_mae`);

--
-- Índices de tabela `gado_fotos`
--
ALTER TABLE `gado_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_foto_id_gado` (`id_gado`);

--
-- Índices de tabela `inseminacoes`
--
ALTER TABLE `inseminacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_vaca` (`id_vaca`),
  ADD KEY `fk_id_inseminador` (`id_inseminador`),
  ADD KEY `fk_id_touro` (`id_touro`);

--
-- Índices de tabela `inseminadores`
--
ALTER TABLE `inseminadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `idx_nome_inseminador` (`nome`);

--
-- Índices de tabela `manejos`
--
ALTER TABLE `manejos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `partos`
--
ALTER TABLE `partos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_id_vaca` (`id_vaca`),
  ADD KEY `idx_id_touro_parto` (`id_touro`);

--
-- Índices de tabela `pesagens`
--
ALTER TABLE `pesagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_gado` (`id_gado`);

--
-- Índices de tabela `producao_leite`
--
ALTER TABLE `producao_leite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_gado` (`id_gado`);

--
-- Índices de tabela `registros_manejos`
--
ALTER TABLE `registros_manejos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_registro_id_manejo` (`id_manejo`),
  ADD KEY `fk_registro_id_gado` (`id_gado`);

--
-- Índices de tabela `touros`
--
ALTER TABLE `touros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `idx_nome_touro` (`nome`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `dieta`
--
ALTER TABLE `dieta`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `envios_leite`
--
ALTER TABLE `envios_leite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `estoque`
--
ALTER TABLE `estoque`
  MODIFY `Id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT de tabela `financeiro_categorias`
--
ALTER TABLE `financeiro_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de tabela `financeiro_contatos`
--
ALTER TABLE `financeiro_contatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `financeiro_lancamentos`
--
ALTER TABLE `financeiro_lancamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `financeiro_lancamento_itens`
--
ALTER TABLE `financeiro_lancamento_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `financeiro_parcelas`
--
ALTER TABLE `financeiro_parcelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT de tabela `gado`
--
ALTER TABLE `gado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de tabela `gado_fotos`
--
ALTER TABLE `gado_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `inseminacoes`
--
ALTER TABLE `inseminacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT de tabela `inseminadores`
--
ALTER TABLE `inseminadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `manejos`
--
ALTER TABLE `manejos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de tabela `partos`
--
ALTER TABLE `partos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT de tabela `pesagens`
--
ALTER TABLE `pesagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT de tabela `producao_leite`
--
ALTER TABLE `producao_leite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `registros_manejos`
--
ALTER TABLE `registros_manejos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT de tabela `touros`
--
ALTER TABLE `touros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `fk_estoque_categoria` FOREIGN KEY (`id_categoria_financeira`) REFERENCES `financeiro_categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `fk_evento_id_vaca` FOREIGN KEY (`id_vaca`) REFERENCES `gado` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `financeiro_categorias`
--
ALTER TABLE `financeiro_categorias`
  ADD CONSTRAINT `fk_categoria_pai` FOREIGN KEY (`parent_id`) REFERENCES `financeiro_categorias` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `financeiro_lancamentos`
--
ALTER TABLE `financeiro_lancamentos`
  ADD CONSTRAINT `fk_lancamento_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `financeiro_categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lancamento_contato` FOREIGN KEY (`id_contato`) REFERENCES `financeiro_contatos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `financeiro_lancamento_itens`
--
ALTER TABLE `financeiro_lancamento_itens`
  ADD CONSTRAINT `fk_item_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `financeiro_categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_lancamento` FOREIGN KEY (`id_lancamento`) REFERENCES `financeiro_lancamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_item_produto_estoque` FOREIGN KEY (`id_produto_estoque`) REFERENCES `estoque` (`Id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `financeiro_parcelas`
--
ALTER TABLE `financeiro_parcelas`
  ADD CONSTRAINT `fk_parcela_lancamento` FOREIGN KEY (`id_lancamento`) REFERENCES `financeiro_lancamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `gado`
--
ALTER TABLE `gado`
  ADD CONSTRAINT `fk_gado_id_mae` FOREIGN KEY (`id_mae`) REFERENCES `gado` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gado_id_pai` FOREIGN KEY (`id_pai`) REFERENCES `touros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `gado_fotos`
--
ALTER TABLE `gado_fotos`
  ADD CONSTRAINT `fk_foto_id_gado` FOREIGN KEY (`id_gado`) REFERENCES `gado` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `inseminacoes`
--
ALTER TABLE `inseminacoes`
  ADD CONSTRAINT `fk_id_inseminador` FOREIGN KEY (`id_inseminador`) REFERENCES `inseminadores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_id_touro` FOREIGN KEY (`id_touro`) REFERENCES `touros` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_id_vaca` FOREIGN KEY (`id_vaca`) REFERENCES `gado` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `partos`
--
ALTER TABLE `partos`
  ADD CONSTRAINT `fk_parto_id_touro` FOREIGN KEY (`id_touro`) REFERENCES `touros` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_parto_id_vaca` FOREIGN KEY (`id_vaca`) REFERENCES `gado` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `pesagens`
--
ALTER TABLE `pesagens`
  ADD CONSTRAINT `pesagens_ibfk_1` FOREIGN KEY (`id_gado`) REFERENCES `gado` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `producao_leite`
--
ALTER TABLE `producao_leite`
  ADD CONSTRAINT `producao_leite_ibfk_1` FOREIGN KEY (`id_gado`) REFERENCES `gado` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `registros_manejos`
--
ALTER TABLE `registros_manejos`
  ADD CONSTRAINT `fk_registro_id_gado` FOREIGN KEY (`id_gado`) REFERENCES `gado` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_registro_id_manejo` FOREIGN KEY (`id_manejo`) REFERENCES `manejos` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `10_ureia` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:13:40' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Ureia), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Ureia';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `11_silagem` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:16:02' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Silagem), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Silagem';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `12_caroco` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Caroco), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Caroco';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `4_casca` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:55:35' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Casca), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Casca';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `3_feno` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:53:07' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Feno), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Feno';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `2_soja` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:48:59' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Soja), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Soja';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `1_milho` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:18:20' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Milho), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Milho';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `limpar_monitoramento_cio_expirado` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-19 00:10:00' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE gado SET data_monitoramento_cio = NULL 
  WHERE data_monitoramento_cio IS NOT NULL AND CURDATE() > data_monitoramento_cio$$

CREATE DEFINER=`root`@`localhost` EVENT `5_polpa` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:00:34' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Polpa), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Polpa';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `6_ice` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:03:39' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Ice), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Ice';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `7_mineral` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:05:20' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Mineral), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Mineral';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `8_equalizer` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:08:05' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Equalizer), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Equalizer';
END$$

CREATE DEFINER=`root`@`localhost` EVENT `9_notox` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-01 00:11:12' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DECLARE total_consumo_diario DECIMAL(10,2);
    SET total_consumo_diario = (SELECT COALESCE(SUM(d.Vacas * d.Notox), 0) FROM dieta d WHERE d.Ativo = 1);
    UPDATE estoque e SET e.Quantidade = e.Quantidade - total_consumo_diario WHERE e.Produto = 'Notox';
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
