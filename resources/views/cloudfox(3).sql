-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 28-Set-2018 às 00:49
-- Versão do servidor: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloudfox`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `boletos`
--

CREATE TABLE `boletos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `linha_digitavel` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `venda` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`, `created_at`, `updated_at`) VALUES
(1, 'shampoos', '', '2018-08-28 03:00:00', '2018-08-28 03:00:00'),
(2, 'condicionadores', '', '2018-08-29 06:00:00', '2018-08-29 06:00:00'),
(3, 'máscaras', '', '2018-08-29 06:00:00', '2018-08-29 06:00:00'),
(4, 'outros', '', '2018-08-28 03:00:00', '2018-08-29 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `comissoes`
--

CREATE TABLE `comissoes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referencia_comissionado` int(11) NOT NULL,
  `tipo_comissao` tinyint(1) DEFAULT NULL,
  `valor` decimal(8,2) NOT NULL,
  `venda` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `compradores`
--

CREATE TABLE `compradores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf_cnpj` varchar(21) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_kapsula_cliente` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `compradores`
--

INSERT INTO `compradores` (`id`, `nome`, `cpf_cnpj`, `data_nascimento`, `email`, `telefone`, `password`, `remember_token`, `created_at`, `updated_at`, `id_kapsula_cliente`) VALUES
(65, 'julio cesar leichtweis', '878.835.060-64', '1998-10-10', 'julio@mail.com', '(55) 9 9693-1098', '$2y$10$./MpNIfe8dBBzEayecbeCuLFyeYmnqcUoLVTQfGrT3CvJqPNV3PGW', NULL, '2018-09-17 23:28:14', '2018-09-17 23:28:14', NULL),
(66, 'julio cesar leichtweis', '87883506064', '1998-10-10', 'julio@mail.com', '(55) 9 9693-1098', '$2y$10$Pt/3wwL8YNoLNYnDpKhDeu/SwrcqQmBDR2MfcqzFRkrPli4lC.2oW', NULL, '2018-09-17 23:40:38', '2018-09-17 23:42:13', 1248687),
(67, 'henrique brites', '03795084008', '1998-10-10', 'henriquebrites@live.com', '(53) 9 9936-4177', '$2y$10$MXQe988wIWyHa3UDkkBDHejC6lRBue7b8qWA8qlowefsyeUZeHQWS', NULL, '2018-09-18 01:35:24', '2018-09-18 01:35:26', 977569),
(68, 'Maria leite', '18070520019', '1998-10-10', 'laite@mail.com', '(78) 7 9489-9895', '$2y$10$fST09F9DIr1NQHmihuFLi.7n/AARz96SVxOwP3pmDNbYlozHcnM2W', NULL, '2018-09-18 02:16:04', '2018-09-18 02:16:06', 1249149),
(69, 'Julio leichtweis', '02901053076', '1998-10-10', 'julio@mail.com', '(55) 9 9696-1098', '$2y$10$PCl66SRL6J/fuiYb9Vq3fu5Y9ORiAxtS4.g.UVwpkoFCaR0jwuZmC', NULL, '2018-09-18 05:11:08', '2018-09-18 05:11:10', 1229559),
(70, 'Jabsb kdjdk', '57312518001', '1998-10-10', 'djdjd@mail.com', '(55) 9 9467-9466', '$2y$10$VDcSb0i3dLrlswRcgjkUQOVEBVl0uwewgYFiIc9aCzqMs1bRVDt4.', NULL, '2018-09-18 05:18:39', '2018-09-18 05:18:39', NULL),
(71, 'Bruna Motta', '25400228046', '1998-10-10', 'motta@mail.com', '(98) 7 9879-8747', '$2y$10$bdgWI7IRstLCCkNOg40KEOEodqADZlCe7A2kBKDLqd8cr9tpxmuGG', NULL, '2018-09-18 15:54:17', '2018-09-18 16:36:09', 1250213),
(72, 'maria silva', '89195018077', '1998-10-10', 'silva@mail.com', '(52) 6 5465-6656', '$2y$10$uQCv.Sjbd/iaYV.dMDrNI.RoElnLwdaoeOj97zwVYAyrHmw7pDYHS', NULL, '2018-09-18 17:26:27', '2018-09-18 17:26:31', 1250390),
(73, 'maria silva', '48488328028', '1998-10-10', 'silva@mail.com', '(66) 5 4656-5656', '$2y$10$zo1kBgMpw80fW8BkwTydgeOyjUaeN98fjGCcyEcYI2a29zAaAAhka', NULL, '2018-09-18 17:28:16', '2018-09-18 17:28:20', 1250396),
(74, 'Lucia Alves Silva', '87851896002', '1998-10-10', 'lucia@live.com', '(54) 4 3543-4534', '$2y$10$3pChfdFYDER4Z3ZwQQNufe33.CY.YpVMPd3TkRq0n0N7QEYsvyXzW', NULL, '2018-09-18 18:28:51', '2018-09-18 18:28:51', NULL),
(75, 'Lorram Carlos Felix', '11420051784', '1998-10-10', 'felixlorram@gmail.com', '(22) 9 8107-1202', '$2y$10$8rOF6dZdWlLKgtbB07ffd.3gZ8fHRiZ/3TnQEBTLQbST2X0Dm14dW', NULL, '2018-09-18 18:57:32', '2018-09-18 23:17:07', 959164),
(76, 'Mariana da silva', '91056874031', '1998-10-10', 'mariana@maul.com', '(55) 9 6834-9665', '$2y$10$vwNkBRG.eKLCV5g8dNpqUuRt75pm6cvI12n90NvFYEwlObXBsstjK', 'rcgYR0ZOQWxIiGNfV8ueB9YjSC1RAyvAfGSIYKka05Xk6N1dAN7wfVVoybDp', '2018-09-18 20:47:18', '2018-09-18 20:47:18', NULL),
(77, 'Julio leichtweis', '80559950012', '1998-10-10', 'julior@mail.com', '(55) 9 4646-4854', '$2y$10$vMz6FlH3v9IK//tsQRJH6uFGlkcLAtsmXXLL.XhpJEPdGpm5VSGNC', NULL, '2018-09-18 21:07:49', '2018-09-18 21:07:49', NULL),
(78, 'julio cesar leichtweis', '03565813067', '1998-10-10', 'jklfdsdjl@lfsdk.com', '(55) 9 9693-1098', '$2y$10$b9eVPRifimN2SVHxSTVVDuG3eu28EAeLQyJtDSewp0hl/bCC4mSf2', NULL, '2018-09-18 21:19:03', '2018-09-18 21:19:07', 1251110),
(79, 'julio lei jsj', '56115994004', '1998-10-10', 'jsjs@jsjs.com', '(95) 6 5698-6494', '$2y$10$Obrw7AWTaAggF0oRlKNhqOYdji9wjbutee0x7zoXA5JiAUtl6KcQm', NULL, '2018-09-18 21:25:01', '2018-09-18 21:25:04', 1251118),
(80, 'Julho bdjsja', '08112560064', '1998-10-10', 'juliojfjd@mail.com', '(98) 6 5656-4664', '$2y$10$cr83oBOEmrH4SISgfDzDD.TxrQ.jm9KzOcCMlzFTlX1vRLFUKcgm2', NULL, '2018-09-18 21:55:40', '2018-09-18 21:55:40', NULL),
(81, 'Testando pagamento', '30679371044', '1998-10-10', 'testw@mail.com', '(55) 9 4646-8986', '$2y$10$Ch9mzDsQO4Lt8RiCBKINr.LUzKdmAe9.6ohIhCIF9WQgw.cfZSjJ2', NULL, '2018-09-18 22:03:46', '2018-09-18 22:03:48', 1251210),
(82, 'teste firefox', '66127396069', '1998-10-10', 'firefox@mail.com', '(55) 6 4976-7946', '$2y$10$QbTpJrWZMyZ7/zQXz6AxlOuQW1Zf1avSCAovV6jCRzCwVqTOoYplO', NULL, '2018-09-18 22:10:16', '2018-09-18 22:10:16', NULL),
(83, 'Navegador proprio', '03373950040', '1998-10-10', 'nav@mail.com', '(55) 9 4646-4994', '$2y$10$hpugkC99zj4N6Veg06ERBeP45UJ0kLH7anrCv3jik4yZebGVGkrb.', NULL, '2018-09-18 22:22:06', '2018-09-18 22:22:06', NULL),
(84, 'Giovani Teles Tier', '03599030022', '1998-10-10', 'giovanitier@gmail.com', '(53) 9 9971-8418', '$2y$10$RGcM/7yBqZH1vrEaeioBcusLWySMd4sB3pjZXy/lmWhCIISws1bOu', NULL, '2018-09-18 22:32:16', '2018-09-18 22:32:16', NULL),
(85, 'julio Cesar', '28828256095', '1998-10-10', 'hsjja@mail.com', '(55) 5 9467-9646', '$2y$10$bfWawMGfy4MrnEi87jKE4esSD4BqcCIV3754aW1fD5EB5CqsTUeuC', NULL, '2018-09-18 23:16:13', '2018-09-18 23:16:13', NULL),
(86, 'Hsjsj jajsj', '03516132000', '1998-10-10', 'bdjdjd@jdjd.com', '(55) 9 8646-4949', '$2y$10$5UUzEyZGR/YB9kd78zxGxe0OnTvUECFVJ8g7zNF2iG1C0m74DY9jO', NULL, '2018-09-18 23:30:07', '2018-09-18 23:30:07', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `cupons`
--

CREATE TABLE `cupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrica` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` tinyint(1) NOT NULL,
  `valor` int(11) NOT NULL,
  `cod_cupom` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `cupons`
--

INSERT INTO `cupons` (`id`, `nome`, `descrica`, `tipo`, `valor`, `cod_cupom`, `status`, `created_at`, `updated_at`) VALUES
(1, 'cupom', 'Descrição Cupom', 1, 50, 'Cod', 1, '2018-08-28 06:00:00', '2018-08-28 06:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(10) UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emaill` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `municipio` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cod_atividade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_situacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `situacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `abertura` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ultima_atualizacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fantasia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capital_social` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atividade_principal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `entregas`
--

CREATE TABLE `entregas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cep` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pais` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bairro` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rua` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ponto_referencia` text COLLATE utf8mb4_unicode_ci,
  `id_kapsula_pedido` bigint(20) DEFAULT NULL,
  `status_kapsula` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resposta_kapsula` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- Extraindo dados da tabela `entregas`
--

INSERT INTO `entregas` (`id`, `cep`, `pais`, `estado`, `cidade`, `bairro`, `rua`, `numero`, `ponto_referencia`, `id_kapsula_pedido`, `status_kapsula`, `resposta_kapsula`, `created_at`, `updated_at`) VALUES
(6, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '8943', NULL, NULL, NULL, NULL, '2018-09-17 23:28:14', '2018-09-17 23:28:14'),
(7, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '8943', NULL, NULL, NULL, NULL, '2018-09-17 23:40:38', '2018-09-17 23:40:38'),
(8, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '8943', NULL, 1741612, '200', 'Pedido Criado com Sucesso', '2018-09-17 23:42:11', '2018-09-17 23:42:13'),
(9, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '801', 'ap 402', 1741939, '200', 'Pedido Criado com Sucesso', '2018-09-18 01:35:24', '2018-09-18 01:35:26'),
(10, '26286-190', 'BR', 'RJ', 'Nova Iguaçu', 'Santa Eugênia', 'Rua Bahia', '854', 'Ap 2', 1742075, '200', 'Pedido Criado com Sucesso', '2018-09-18 02:16:04', '2018-09-18 02:16:07'),
(11, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '758', 'AP 214', 1742504, '200', 'Pedido Criado com Sucesso', '2018-09-18 05:11:08', '2018-09-18 05:11:10'),
(12, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '4783', 'casa', NULL, NULL, NULL, '2018-09-18 05:18:39', '2018-09-18 05:18:39'),
(13, '53170-520', 'BR', 'PE', 'Olinda', 'Passarinho', 'Rua Renascer', '84', 'casa', NULL, NULL, NULL, '2018-09-18 15:54:18', '2018-09-18 15:54:18'),
(14, '53170-520', 'BR', 'PE', 'Olinda', 'Passarinho', 'Rua Renascer', '84', 'casa', NULL, NULL, NULL, '2018-09-18 15:57:24', '2018-09-18 15:57:24'),
(15, '53170-520', 'BR', 'PE', 'Olinda', 'Passarinho', 'Rua Renascer', '56', 'casa', 1743141, '200', 'Pedido Criado com Sucesso', '2018-09-18 16:36:06', '2018-09-18 16:36:09'),
(16, '90820-094', 'BR', 'RS', 'Porto Alegre', 'Cristal', 'Beco B', '6656', 'ap 4', NULL, NULL, NULL, '2018-09-18 17:26:27', '2018-09-18 17:26:27'),
(17, '90820-094', 'BR', 'RS', 'Porto Alegre', 'Cristal', 'Beco B', '6656', 'ap 4', 1743318, '200', 'Pedido Criado com Sucesso', '2018-09-18 17:26:29', '2018-09-18 17:26:32'),
(18, '90820-094', 'BR', 'RS', 'Porto Alegre', 'Cristal', 'Beco B', '656', 'ap 4', NULL, NULL, NULL, '2018-09-18 17:28:16', '2018-09-18 17:28:16'),
(19, '90820-094', 'BR', 'RS', 'Porto Alegre', 'Cristal', 'Beco B', '656', 'ap 4', 1743324, '200', 'Pedido Criado com Sucesso', '2018-09-18 17:28:17', '2018-09-18 17:28:20'),
(20, '53170-520', 'BR', 'PE', 'Olinda', 'Passarinho', 'Rua Renascer', '84', 'casa', NULL, NULL, NULL, '2018-09-18 17:29:45', '2018-09-18 17:29:45'),
(21, '59115-591', 'BR', 'RN', 'Natal', 'Nossa Senhora da Apresentação', 'Travessa Valdir Peres', '254', NULL, NULL, NULL, NULL, '2018-09-18 18:28:52', '2018-09-18 18:28:52'),
(22, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '801', 'Ap 03', NULL, NULL, NULL, '2018-09-18 18:55:22', '2018-09-18 18:55:22'),
(23, '96400-003', 'BR', 'RS', 'Bagé', 'Centro', 'Avenida Sete de Setembro', '1012', 'Casa 01', NULL, NULL, NULL, '2018-09-18 18:57:32', '2018-09-18 18:57:32'),
(24, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '801', 'Ap 402', NULL, NULL, NULL, '2018-09-18 19:19:26', '2018-09-18 19:19:26'),
(25, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '63', 'AP 107', NULL, NULL, NULL, '2018-09-18 20:47:18', '2018-09-18 20:47:18'),
(26, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '6594', NULL, NULL, NULL, NULL, '2018-09-18 21:07:49', '2018-09-18 21:07:49'),
(27, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '9843', NULL, 1744038, '200', 'Pedido Criado com Sucesso', '2018-09-18 21:19:03', '2018-09-18 21:19:07'),
(28, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '4554', 'Ap 402', 1744039, '200', 'Pedido Criado com Sucesso', '2018-09-18 21:19:12', '2018-09-18 21:19:14'),
(29, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '6565', NULL, 1744047, '200', 'Pedido Criado com Sucesso', '2018-09-18 21:25:01', '2018-09-18 21:25:04'),
(30, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '65976', NULL, NULL, NULL, NULL, '2018-09-18 21:55:40', '2018-09-18 21:55:40'),
(31, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '6895', NULL, 1744139, '200', 'Pedido Criado com Sucesso', '2018-09-18 22:03:46', '2018-09-18 22:03:48'),
(32, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '6794', NULL, NULL, NULL, NULL, '2018-09-18 22:10:16', '2018-09-18 22:10:16'),
(33, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '6594', NULL, NULL, NULL, NULL, '2018-09-18 22:22:06', '2018-09-18 22:22:06'),
(34, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '801', 'Ap 402', NULL, NULL, NULL, '2018-09-18 22:27:08', '2018-09-18 22:27:08'),
(35, '96420-040', 'BR', 'RS', 'Bagé', 'Castro Alves', 'Rua Professor José Frois', '250', NULL, NULL, NULL, NULL, '2018-09-18 22:32:16', '2018-09-18 22:32:16'),
(36, '96400-101', 'BR', 'RS', 'Bagé', 'Centro', 'Avenida General Osório', '524', 'Ap 038', NULL, NULL, NULL, '2018-09-18 22:43:40', '2018-09-18 22:43:40'),
(37, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '64978', NULL, NULL, NULL, NULL, '2018-09-18 23:16:13', '2018-09-18 23:16:13'),
(38, '96400-003', 'BR', 'RS', 'Bagé', 'Centro', 'Avenida Sete de Setembro', '1012', 'Casa 1', 1744397, '200', 'Pedido Criado com Sucesso', '2018-09-18 23:17:06', '2018-09-18 23:17:08'),
(39, '97572-490', 'BR', 'RS', 'Santana do Livramento', 'Planalto', 'Rua Gentil Araújo', '6764', NULL, NULL, NULL, NULL, '2018-09-18 23:30:07', '2018-09-18 23:30:07'),
(40, '96400-600', 'BR', 'RS', 'Bagé', 'Centro', 'Rua Senador Salgado Filho', '801', 'ap 4002', NULL, NULL, NULL, '2018-09-21 22:10:21', '2018-09-21 22:10:21');

-- --------------------------------------------------------

--
-- Estrutura da tabela `fotos`
--

CREATE TABLE `fotos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `caminho_imagem` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plano` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- Extraindo dados da tabela `fotos`
--

INSERT INTO `fotos` (`id`, `caminho_imagem`, `plano`, `created_at`, `updated_at`) VALUES
(1, 'storage/upload/plano/s_nutri_cachos.png', 1, '2018-08-28 03:00:00', '2018-08-28 03:00:00'),
(2, 'storage/upload/plano/s_fortalecedor.png', 2, '2018-09-01 03:00:00', '2018-09-01 03:00:00'),
(3, 'storage/upload/plano/s_anti_poluicao.png', 3, '2018-09-01 03:00:00', '2018-09-01 03:00:00'),
(4, 'storage/upload/plano/c_nutri_cachos.png', 4, '2018-09-01 03:00:00', '2018-09-01 03:00:00'),
(5, 'storage/upload/plano/c_fortalecedor.png', 5, '2018-09-12 03:00:00', '2018-09-27 03:00:00'),
(6, 'storage/upload/plano/o_leave_nutri_cachos.png', 6, NULL, NULL),
(7, 'storage/upload/plano/m_mask_explosao_aminoacidos.png', 7, NULL, NULL),
(8, 'storage/upload/plano/m_mask_explosao_brilho.png', 8, NULL, NULL),
(9, 'storage/upload/plano/m_nutri_cachos.png', 9, NULL, NULL),
(10, 'storage/upload/plano/o_fluido_brilho.png', 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_sessao_log` text COLLATE utf8mb4_unicode_ci,
  `plano` text COLLATE utf8mb4_unicode_ci,
  `evento` text COLLATE utf8mb4_unicode_ci,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `hora_acesso` text COLLATE utf8mb4_unicode_ci,
  `hora_encerramento` text COLLATE utf8mb4_unicode_ci,
  `hora_submit` text COLLATE utf8mb4_unicode_ci,
  `forward` text COLLATE utf8mb4_unicode_ci,
  `referencia` text COLLATE utf8mb4_unicode_ci,
  `nome` text COLLATE utf8mb4_unicode_ci,
  `email` text COLLATE utf8mb4_unicode_ci,
  `cpf` text COLLATE utf8mb4_unicode_ci,
  `celular` text COLLATE utf8mb4_unicode_ci,
  `entrega` text COLLATE utf8mb4_unicode_ci,
  `cep` text COLLATE utf8mb4_unicode_ci,
  `endereco` text COLLATE utf8mb4_unicode_ci,
  `numero` text COLLATE utf8mb4_unicode_ci,
  `bairro` text COLLATE utf8mb4_unicode_ci,
  `cidade` text COLLATE utf8mb4_unicode_ci,
  `estado` text COLLATE utf8mb4_unicode_ci,
  `valor_frete` text COLLATE utf8mb4_unicode_ci,
  `valor_cupom` text COLLATE utf8mb4_unicode_ci,
  `valor_total` text COLLATE utf8mb4_unicode_ci,
  `numero_cartao` tinyint(1) DEFAULT NULL,
  `nome_cartao` tinyint(1) DEFAULT NULL,
  `cpf_cartao` tinyint(1) DEFAULT NULL,
  `mes_cartao` tinyint(1) DEFAULT NULL,
  `ano_cartao` tinyint(1) DEFAULT NULL,
  `codigo_cartao` tinyint(1) DEFAULT NULL,
  `parcelamento` tinyint(1) DEFAULT NULL,
  `erro` text COLLATE utf8mb4_unicode_ci,
  `coockies` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `logs`
--

INSERT INTO `logs` (`id`, `id_sessao_log`, `plano`, `evento`, `user_agent`, `hora_acesso`, `hora_encerramento`, `hora_submit`, `forward`, `referencia`, `nome`, `email`, `cpf`, `celular`, `entrega`, `cep`, `endereco`, `numero`, `bairro`, `cidade`, `estado`, `valor_frete`, `valor_cupom`, `valor_total`, `numero_cartao`, `nome_cartao`, `cpf_cartao`, `mes_cartao`, `ano_cartao`, `codigo_cartao`, `parcelamento`, `erro`, `coockies`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2018_08_22_190257_criar_tabela_categoria', 1),
(2, '2018_08_22_191126_criar_tabela_produto', 1),
(3, '2018_08_22_192721_criar_tabela_plano', 1),
(4, '2018_08_22_194942_criar_tabela_foto', 1),
(5, '2018_08_22_201552_criar_tabela_produto_plano', 1),
(6, '2018_08_22_201641_criar_tabela_comprador', 1),
(7, '2018_08_22_202223_criar_tabela_venda', 1),
(8, '2018_08_22_203620_criar_tabela_boleto', 1),
(9, '2018_08_22_204928_criar_tabela_plano_venda', 1),
(10, '2018_08_22_224036_criar_tabela_comissao', 1),
(11, '2018_08_25_214144_criar_tabele_cupom', 1),
(12, '2018_08_25_214257_criar_tabele_planos_cupons', 1),
(13, '2018_08_29_013056_alterar_tabela_compradores', 2),
(14, '2018_08_29_153826_alterar_tabala_planos_produtos', 3),
(15, '2018_08_29_154432_alterar_tabala_planos', 4),
(16, '2018_08_29_223328_altera_tabela_plano_vendas', 5),
(19, '2018_09_03_012848_altera_tabela_vendas_frete', 8),
(20, '2018_09_06_210703_altera_tabela_venda', 9),
(21, '2018_09_06_215442_altera_tabela_venda', 10),
(23, '2018_08_22_201819_criar_tabela_entrega', 11),
(24, '2018_09_08_153726_altera_tabela_venda', 12),
(25, '2018_09_08_213922_altera_tabela_comprador', 13),
(26, '2018_09_09_061253_alterar_tabela_venda', 14),
(27, '2018_09_09_222530_alterar_tabela_entrega', 15),
(29, '2018_09_10_173932_altera_tabela_plano', 16),
(30, '2018_09_14_192641_altera_tabela_vendas', 17),
(32, '2018_09_19_174726_criar_tabela_logs', 18),
(34, '2018_09_19_154105_criar_tabela_pixel', 19),
(35, '2018_09_19_193152_criar_tabela_plano_pixel', 19),
(36, '2014_10_12_000000_create_users_table', 20),
(37, '2014_10_12_100000_create_password_resets_table', 21),
(38, '2018_09_27_002948_alterar_tabela_users', 22),
(39, '2018_09_27_010253_criar_tabela_empresa', 22),
(40, '2018_09_27_011445_criar_tabela_user_empresa', 23),
(43, '2018_09_27_182125_aterar_tabela_planos', 24);

-- --------------------------------------------------------

--
-- Estrutura da tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pixels`
--

CREATE TABLE `pixels` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cod_pixel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plataforma` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `pixels`
--

INSERT INTO `pixels` (`id`, `nome`, `cod_pixel`, `plataforma`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Facebook', '2032486283729541', 'facebook', 1, NULL, NULL, NULL),
(2, 'Google', '851088806 ', 'google', 1, NULL, NULL, NULL),
(3, 'Outbrian', '00501a263406b1224c160160f17fbdcd0d', 'outbrain', 1, NULL, NULL, NULL),
(4, 'Taboola', '1152332', 'taboola', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `planos`
--

CREATE TABLE `planos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quntidade` int(11) NOT NULL,
  `status_cupom` tinyint(1) NOT NULL DEFAULT '0',
  `preco` decimal(8,2) NOT NULL,
  `cod_identificador` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `id_pacote_kapsula` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frete_gratis` tinyint(1) NOT NULL DEFAULT '0',
  `valor_frete` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `descricao`, `quntidade`, `status_cupom`, `preco`, `cod_identificador`, `created_at`, `updated_at`, `status`, `id_pacote_kapsula`, `frete_gratis`, `valor_frete`) VALUES
(1, 'Shampoo Nutri Cachos', 'Shampoo Nutri Cachos - 250ml', 0, 0, '1.00', 'ASD456', '2018-08-28 03:00:00', '2018-08-28 03:00:00', 1, '-', 0, NULL),
(2, 'Shampoo Fortalecedor', 'Shampoo Fortalecedor - 250ml', 0, 0, '1.00', 'QWE789', '2018-08-28 06:00:00', '2018-08-28 06:00:00', 1, '-', 0, NULL),
(3, 'Shampoo 2x1 Anti Poluição', 'Shampoo 2x1 Anti Poluição - 250ml', 0, 0, '1.00', 'HJK789', '2018-08-28 06:00:00', '2018-08-29 06:00:00', 1, '-', 0, NULL),
(4, 'Condicionador Nutri Cachos', 'Condicionador Nutri Cachos - 250ml', 0, 0, '1.00', 'ZXC153', '2018-08-28 06:00:00', '2018-08-29 06:00:00', 1, '-', 0, NULL),
(5, 'Condicionador Fortalecedor', 'Condicionador Fortalecedor - 250ml', 0, 0, '1.00', 'POI223', '2018-08-28 03:00:00', '2018-08-29 03:00:00', 1, '-', 0, NULL),
(6, 'Leave in Nutri Cachos', 'Leave in Nutri Cachos - 250ml', 0, 0, '1.00', 'SHW482', '2018-08-28 03:00:00', NULL, 1, '-', 0, NULL),
(7, 'Máscara Explosão de Aminoácidos', 'Máscara Explosão de Aminoácidos - 250ML', 0, 0, '1.00', 'UYT764', '2018-09-27 03:00:00', NULL, 1, '-', 0, NULL),
(8, 'Máscara Explosão de Brilho', 'Máscara Explosão de Brilho - 250ML', 0, 0, '1.00', 'DJT964', '2018-09-27 03:00:00', NULL, 1, '-', 0, NULL),
(9, 'Máscara Nutri Cachos', 'Máscara Nutri Cachos - 250ML', 0, 0, '1.00', 'WUR964', '2018-09-27 03:00:00', NULL, 1, '-', 0, NULL),
(10, 'Fluído de Brilho', 'Fluído de Brilho - 120ml', 0, 0, '1.00', 'KRT624', '2018-09-27 03:00:00', NULL, 1, '-', 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `planos_cupons`
--

CREATE TABLE `planos_cupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cupom` bigint(20) UNSIGNED NOT NULL,
  `plano` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `planos_cupons`
--

INSERT INTO `planos_cupons` (`id`, `cupom`, `plano`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2018-08-28 06:00:00', '2018-08-28 06:00:00'),
(2, 1, 1, '2018-09-08 03:00:00', '2018-09-08 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `planos_pixels`
--

CREATE TABLE `planos_pixels` (
  `id` int(10) UNSIGNED NOT NULL,
  `checkout` tinyint(1) DEFAULT '1',
  `cartao` tinyint(1) DEFAULT '1',
  `boleto` tinyint(1) DEFAULT '1',
  `plano` bigint(20) UNSIGNED NOT NULL,
  `pixel` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `planos_pixels`
--

INSERT INTO `planos_pixels` (`id`, `checkout`, `cartao`, `boleto`, `plano`, `pixel`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 2, 1, NULL, NULL, NULL),
(2, 1, 1, 1, 2, 2, NULL, NULL, NULL),
(3, 1, 1, 1, 2, 3, NULL, NULL, NULL),
(4, 1, 1, 1, 2, 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `planos_vendas`
--

CREATE TABLE `planos_vendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plano` bigint(20) UNSIGNED NOT NULL,
  `venda` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `planos_vendas`
--

INSERT INTO `planos_vendas` (`id`, `plano`, `venda`, `created_at`, `updated_at`) VALUES
(1, 2, 270, '2018-09-17 23:28:15', '2018-09-17 23:28:15'),
(2, 2, 271, '2018-09-17 23:40:39', '2018-09-17 23:40:39'),
(3, 2, 272, '2018-09-17 23:42:11', '2018-09-17 23:42:11'),
(4, 2, 273, '2018-09-18 01:35:24', '2018-09-18 01:35:24'),
(5, 1, 274, '2018-09-18 02:16:04', '2018-09-18 02:16:04'),
(6, 3, 275, '2018-09-18 05:11:08', '2018-09-18 05:11:08'),
(7, 3, 276, '2018-09-18 05:18:41', '2018-09-18 05:18:41'),
(8, 3, 277, '2018-09-18 15:54:19', '2018-09-18 15:54:19'),
(9, 3, 278, '2018-09-18 15:57:28', '2018-09-18 15:57:28'),
(10, 2, 279, '2018-09-18 16:36:06', '2018-09-18 16:36:06'),
(11, 1, 280, '2018-09-18 17:26:28', '2018-09-18 17:26:28'),
(12, 1, 281, '2018-09-18 17:26:30', '2018-09-18 17:26:30'),
(13, 1, 282, '2018-09-18 17:28:17', '2018-09-18 17:28:17'),
(14, 1, 283, '2018-09-18 17:28:17', '2018-09-18 17:28:17'),
(15, 3, 284, '2018-09-18 17:29:48', '2018-09-18 17:29:48'),
(16, 2, 286, '2018-09-18 18:55:22', '2018-09-18 18:55:22'),
(17, 2, 287, '2018-09-18 18:57:33', '2018-09-18 18:57:33'),
(18, 1, 288, '2018-09-18 19:19:27', '2018-09-18 19:19:27'),
(19, 3, 289, '2018-09-18 20:47:19', '2018-09-18 20:47:19'),
(20, 2, 290, '2018-09-18 21:07:50', '2018-09-18 21:07:50'),
(21, 2, 291, '2018-09-18 21:19:03', '2018-09-18 21:19:03'),
(22, 1, 292, '2018-09-18 21:19:12', '2018-09-18 21:19:12'),
(23, 2, 293, '2018-09-18 21:25:01', '2018-09-18 21:25:01'),
(24, 2, 294, '2018-09-18 21:55:40', '2018-09-18 21:55:40'),
(25, 2, 295, '2018-09-18 22:03:46', '2018-09-18 22:03:46'),
(26, 2, 296, '2018-09-18 22:10:16', '2018-09-18 22:10:16'),
(27, 2, 297, '2018-09-18 22:22:06', '2018-09-18 22:22:06'),
(28, 1, 298, '2018-09-18 22:27:08', '2018-09-18 22:27:08'),
(29, 2, 299, '2018-09-18 22:32:16', '2018-09-18 22:32:16'),
(30, 2, 300, '2018-09-18 22:43:40', '2018-09-18 22:43:40'),
(31, 2, 301, '2018-09-18 23:16:15', '2018-09-18 23:16:15'),
(32, 2, 302, '2018-09-18 23:17:06', '2018-09-18 23:17:06'),
(33, 2, 303, '2018-09-18 23:30:07', '2018-09-18 23:30:07'),
(34, 2, 304, '2018-09-21 22:10:24', '2018-09-21 22:10:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `garantia` int(11) DEFAULT NULL,
  `quntidade` int(11) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT NULL,
  `formato` tinyint(1) DEFAULT NULL,
  `categoria` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `custo_produto` decimal(6,2) DEFAULT '0.00',
  `foto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `garantia`, `quntidade`, `disponivel`, `formato`, `categoria`, `created_at`, `updated_at`, `custo_produto`, `foto`) VALUES
(1, 'Shampoo Nutri Cachos', 'Shampoo nutri cachos - 250ml', 7, 0, 1, 1, 1, '2018-08-28 03:00:00', '2018-08-28 03:00:00', '9.00', 'storage/upload/produto/s_nutri_cachos.png'),
(2, 'Shampoo Fortalecedor', 'Shampoo fortalecedor - 250ml', 7, 0, 1, 1, 1, '2018-08-28 06:00:00', '2018-08-28 06:00:00', '9.00', 'storage/upload/produto/s_fortalecedor.png'),
(3, 'Shampoo 2x1 Anti Poluição', 'Shampoo 2x1 anti poluição - 250ml', 7, 0, 1, 1, 1, '2018-08-28 06:00:00', '2018-08-29 06:00:00', '9.00', 'storage/upload/produto/s_anti_poluicao.png'),
(4, 'Condicionador Nutri Cachos', 'Condicionador nutri cachos - 250ml', 7, 0, 1, 2, 1, '2018-08-28 03:00:00', '2018-08-29 03:00:00', '9.00', 'storage/upload/produto/c_nutri_cachos.png'),
(5, 'Condicionador fortalecedor', 'Condicionador fortalecedor - 250ml', 7, 0, 1, 2, 1, '2018-09-26 03:00:00', '2018-09-26 03:00:00', '9.00', 'storage/upload/produto/c_fortalecedor.png'),
(6, 'Leave in nutri cachos', 'Leave in nutri cachos - 250ML', 7, 0, 1, 1, 4, '2018-09-27 03:00:00', NULL, '9.00', 'storage/upload/produto/o_leave_nutri_cachos.png'),
(7, 'Fluído de Brilho', 'Fluído de brilho - 120ml', 7, 0, 1, 1, 4, '2018-09-27 03:00:00', NULL, '9.00', 'storage/upload/produto/o_fluido_brilho.png'),
(8, 'Máscara nutri cachos', 'MÁSCARA nutri cachos - 250M', 7, 0, 1, 1, 3, '2018-09-12 03:00:00', NULL, '9.00', 'storage/upload/produto/m_nutri_cachos.png'),
(9, 'Máscara Explosão de Brilho', 'MÁSCARA EXPLOSÃO DE brilho - 250ML', 7, 0, 1, 1, 3, '2018-09-27 03:00:00', NULL, '9.00', 'storage/upload/produto/m_mask_explosao_brilho.png'),
(10, 'MÁSCARA EXPLOSÃO DE AMINOÁCDIDOS', 'MÁSCARA EXPLOSÃO DE AMINOÁCIDOS - 250ML', 7, 0, 1, 1, 3, '2018-08-28 03:00:00', NULL, '9.00', 'storage/upload/produto/m_mask_explosao_aminoacidos.png');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos_planos`
--

CREATE TABLE `produtos_planos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `produto` bigint(20) UNSIGNED NOT NULL,
  `plano` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `quantidade_produto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `produtos_planos`
--

INSERT INTO `produtos_planos` (`id`, `produto`, `plano`, `created_at`, `updated_at`, `quantidade_produto`) VALUES
(1, 1, 1, '2018-08-28 03:00:00', '2018-08-28 03:00:00', 1),
(2, 2, 2, '2018-08-29 06:00:00', '2018-08-29 06:00:00', 1),
(3, 3, 3, '2018-08-29 06:00:00', '2018-08-29 06:00:00', 1),
(4, 4, 4, '2018-09-19 03:00:00', '2018-09-26 03:00:00', 1),
(5, 5, 5, '2018-09-12 03:00:00', '2018-09-19 03:00:00', 1),
(6, 6, 5, '2018-09-12 03:00:00', '2018-09-27 03:00:00', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `celular` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `data_nascimento`, `celular`, `cpf`, `cep`, `pais`, `estado`, `cidade`, `bairro`, `logradouro`, `numero`, `complemento`, `telefone2`, `telefone1`, `referencia`, `foto`, `deleted_at`) VALUES
(1, 'Henrique Brites', 'henriquebrites@live.com', '$2y$10$H8F/hcr/zG8ZZ4YC0b1a..hRejC8K1GcuQgnLOaeVG3/mUZRW0tkm', 'oIHLm5b1O4PKzP3lqVahpmN8VP5JVUYlqoMUfCYblDm95Az2OpPToV4aKeLr', '2018-09-25 22:00:01', '2018-09-25 22:00:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Pedro Pina', 'pedro@live.com', '$2y$10$nd4Ey9PMW1ZOm1vpj92aE.TwqbzAJxmuaNBjK/JVV419u7E/IkPAS', '', '2018-09-26 16:56:54', '2018-09-26 16:56:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users_empresas`
--

CREATE TABLE `users_empresas` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED NOT NULL,
  `empresa` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL,
  `forma_pagamento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_total_pago` decimal(8,2) DEFAULT NULL,
  `valor_recebido_mercado_pago` decimal(8,2) DEFAULT NULL,
  `valor_plano` decimal(6,2) DEFAULT NULL,
  `valor_frete` decimal(6,2) NOT NULL,
  `cod_cupom` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT 'NULL',
  `meio_pagamento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_finalizada` datetime NOT NULL,
  `comprador` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mercado_pago_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mercado_pago_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qtd_parcela` int(11) DEFAULT NULL,
  `valor_parcela` decimal(6,2) DEFAULT NULL,
  `bandeira` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entrega` bigint(20) UNSIGNED DEFAULT NULL,
  `valor_cupom` decimal(6,2) DEFAULT NULL,
  `tipo_cupom` int(11) DEFAULT NULL,
  `link_boleto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- Extraindo dados da tabela `vendas`
--

INSERT INTO `vendas` (`id`, `status`, `forma_pagamento`, `valor_total_pago`, `valor_recebido_mercado_pago`, `valor_plano`, `valor_frete`, `cod_cupom`, `meio_pagamento`, `data_inicio`, `data_finalizada`, `comprador`, `created_at`, `updated_at`, `mercado_pago_id`, `mercado_pago_status`, `qtd_parcela`, `valor_parcela`, `bandeira`, `entrega`, `valor_cupom`, `tipo_cupom`, `link_boleto`) VALUES
(270, 1, 'boleto', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-17 20:28:14', '2018-09-17 20:28:14', 65, '2018-09-17 23:28:14', '2018-09-17 23:28:14', '4171702415', 'pending', NULL, NULL, NULL, 6, '50.00', 1, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4171702415&payment_method_reference_id=3426242640&caller_id=1641887&hash=c61f0999-c651-4051-b79b-2cfafaa01c68'),
(271, 1, 'boleto', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-17 20:40:38', '2018-09-17 20:40:38', 66, '2018-09-17 23:40:38', '2018-09-17 23:40:39', '4171709638', 'pending', NULL, NULL, NULL, 7, '50.00', 1, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4171709638&payment_method_reference_id=3426252122&caller_id=1641887&hash=795e0b7e-32fd-47c9-8005-a3629078a067'),
(272, 3, 'cartao_credito', '4.90', '4.66', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-17 20:42:11', '2018-09-17 20:42:11', 66, '2018-09-17 23:42:11', '2018-09-17 23:42:11', '15927224', 'approved', 1, '4.90', 'visa', 8, '50.00', 1, NULL),
(273, 3, 'cartao_credito', '4.90', '4.66', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-17 22:35:24', '2018-09-17 22:35:24', 67, '2018-09-18 01:35:24', '2018-09-18 01:35:25', '15928632', 'approved', 1, '4.90', 'amex', 9, '50.00', 1, NULL),
(274, 3, 'cartao_credito', '31.80', '30.21', '1.00', '30.80', '', 'mercado_pago', '2018-09-17 23:16:04', '2018-09-17 23:16:04', 68, '2018-09-18 02:16:04', '2018-09-18 02:16:05', '15928752', 'approved', 4, '8.52', 'visa', 10, NULL, NULL, NULL),
(275, 3, 'cartao_credito', '174.15', '165.46', '120.25', '53.90', '', 'mercado_pago', '2018-09-18 02:11:08', '2018-09-18 02:11:08', 69, '2018-09-18 05:11:08', '2018-09-18 05:11:09', '15929542', 'approved', 6, '32.07', 'visa', 11, NULL, NULL, NULL),
(276, 1, 'boleto', '174.15', '0.00', '120.25', '53.90', '', 'mercado_pago', '2018-09-18 02:18:39', '2018-09-18 02:18:39', 70, '2018-09-18 05:18:39', '2018-09-18 05:18:41', '15929560', 'pending', NULL, NULL, NULL, 12, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/sandbox/ticket/helper?payment_id=15929560&payment_method_reference_id=15929559&caller_id=355350189&hash=5b97553f-10be-4d1f-86e0-ce515fafbaae'),
(277, 1, 'cartao_credito', '195.05', '185.32', '120.25', '74.80', '', 'mercado_pago', '2018-09-18 12:54:18', '2018-09-18 12:54:18', 71, '2018-09-18 15:54:18', '2018-09-18 15:54:18', '15932583', 'approved', 2, '99.86', 'diners', 13, NULL, NULL, NULL),
(278, 1, 'boleto', '195.05', '0.00', '120.25', '74.80', '', 'mercado_pago', '2018-09-18 12:57:25', '2018-09-18 12:57:25', 71, '2018-09-18 15:57:25', '2018-09-18 15:57:27', '15932607', 'pending', NULL, NULL, NULL, 14, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/sandbox/ticket/helper?payment_id=15932607&payment_method_reference_id=15932606&caller_id=151157538&hash=8b670a83-904f-4ac2-8d65-8d88826572aa'),
(279, 3, 'cartao_credito', '25.80', '24.51', '1.00', '74.80', 'Cod', 'mercado_pago', '2018-09-18 13:36:06', '2018-09-18 13:36:06', 71, '2018-09-18 16:36:06', '2018-09-18 16:36:07', '15932789', 'approved', 2, '13.21', 'elo', 15, '50.00', 1, NULL),
(280, 1, 'boleto', '54.90', '0.00', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 14:26:27', '2018-09-18 14:26:27', 72, '2018-09-18 17:26:27', '2018-09-18 17:26:28', '15933221', 'pending', NULL, NULL, NULL, 16, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/sandbox/ticket/helper?payment_id=15933221&payment_method_reference_id=15933220&caller_id=189797918&hash=b9615553-5628-4598-bd56-dd0aaeb500f7'),
(281, 3, 'cartao_credito', '54.90', '52.16', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 14:26:29', '2018-09-18 14:26:29', 72, '2018-09-18 17:26:30', '2018-09-18 17:26:30', '15933218', 'approved', 3, '19.17', 'diners', 17, NULL, NULL, NULL),
(282, 1, 'boleto', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 14:28:16', '2018-09-18 14:28:16', 73, '2018-09-18 17:28:16', '2018-09-18 17:28:17', '15933240', 'pending', NULL, NULL, NULL, 18, '50.00', 1, 'https://www.mercadopago.com/mlb/payments/sandbox/ticket/helper?payment_id=15933240&payment_method_reference_id=15933239&caller_id=189797918&hash=b7f1bb15-88ed-442b-8c26-b9b78c26be88'),
(283, 3, 'cartao_credito', '4.90', '4.66', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 14:28:17', '2018-09-18 14:28:17', 73, '2018-09-18 17:28:17', '2018-09-18 17:28:18', '15933237', 'approved', 1, '4.90', 'amex', 19, '50.00', 1, NULL),
(284, 1, 'boleto', '145.05', '0.00', '120.25', '74.80', 'Cod', 'mercado_pago', '2018-09-18 14:29:45', '2018-09-18 14:29:45', 71, '2018-09-18 17:29:45', '2018-09-18 17:29:47', '15933253', 'pending', NULL, NULL, NULL, 20, '50.00', 1, 'https://www.mercadopago.com/mlb/payments/sandbox/ticket/helper?payment_id=15933253&payment_method_reference_id=15933254&caller_id=151157538&hash=4b6ee58b-cb72-4ee2-9911-f6c1cd1fc303'),
(285, 1, 'boleto', '208.25', NULL, '120.25', '88.00', '', 'mercado_pago', '2018-09-18 15:28:52', '2018-09-18 15:28:52', 74, '2018-09-18 18:28:52', '2018-09-18 18:28:52', NULL, NULL, NULL, NULL, NULL, 21, NULL, NULL, NULL),
(286, 1, 'boleto', '54.90', '0.00', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 15:55:22', '2018-09-18 15:55:22', 67, '2018-09-18 18:55:22', '2018-09-18 18:55:22', '4173238411', 'pending', NULL, NULL, NULL, 22, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4173238411&payment_method_reference_id=3428995140&caller_id=159428273&hash=2eeb3f0d-0ff9-4d79-a6c1-3cde9430ebb4'),
(287, 1, 'boleto', '54.90', '0.00', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 15:57:32', '2018-09-18 15:57:32', 75, '2018-09-18 18:57:32', '2018-09-18 18:57:33', '4172941578', 'pending', NULL, NULL, NULL, 23, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4172941578&payment_method_reference_id=3428909512&caller_id=249922967&hash=0fd07f23-2c1a-41dd-91ff-1cc671a6dfe5'),
(288, 1, 'boleto', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 16:19:26', '2018-09-18 16:19:26', 67, '2018-09-18 19:19:26', '2018-09-18 19:19:27', '4173088647', 'pending', NULL, NULL, NULL, 24, '50.00', 1, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4173088647&payment_method_reference_id=3429205534&caller_id=159428273&hash=ea03d904-7359-4f48-942f-5fdf7e74432f'),
(289, 1, 'boleto', '174.15', '0.00', '120.25', '53.90', '', 'mercado_pago', '2018-09-18 17:47:18', '2018-09-18 17:47:18', 76, '2018-09-18 20:47:18', '2018-09-18 20:47:19', '4173029913', 'pending', NULL, NULL, NULL, 25, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4173029913&payment_method_reference_id=3429286794&caller_id=355498989&hash=5019d62e-9dd4-4fbb-b175-313b01d3fe64'),
(290, 1, 'boleto', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 18:07:49', '2018-09-18 18:07:49', 77, '2018-09-18 21:07:49', '2018-09-18 21:07:50', '4173430644', 'pending', NULL, NULL, NULL, 26, '50.00', 1, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4173430644&payment_method_reference_id=3429508170&caller_id=355505104&hash=6331f4c2-9c5d-41bd-ab3e-faefb80e086e'),
(291, 3, 'cartao_credito', '54.90', '52.16', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 18:19:03', '2018-09-18 18:19:03', 78, '2018-09-18 21:19:03', '2018-09-18 21:19:04', '15934758', 'approved', 8, '7.72', 'visa', 27, NULL, NULL, NULL),
(292, 3, 'cartao_credito', '54.90', '52.16', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 18:19:12', '2018-09-18 18:19:12', 67, '2018-09-18 21:19:12', '2018-09-18 21:19:13', '15934763', 'approved', 5, '11.97', 'amex', 28, NULL, NULL, NULL),
(293, 3, 'cartao_credito', '54.90', '52.16', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 18:25:01', '2018-09-18 18:25:01', 79, '2018-09-18 21:25:01', '2018-09-18 21:25:02', '15934820', 'approved', 4, '14.71', 'master', 29, NULL, NULL, NULL),
(294, 7, 'cartao_credito', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 18:55:40', '2018-09-18 18:55:40', 80, '2018-09-18 21:55:40', '2018-09-18 22:56:26', '4173178219', 'rejected', 1, '4.90', 'master', 30, '50.00', 1, NULL),
(295, 3, 'cartao_credito', '4.90', '4.66', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 19:03:46', '2018-09-18 19:03:46', 81, '2018-09-18 22:03:46', '2018-09-18 22:03:47', '4173182880', 'approved', 1, '4.90', 'master', 31, '50.00', 1, NULL),
(296, 7, 'cartao_credito', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 19:10:16', '2018-09-18 19:10:16', 82, '2018-09-18 22:10:16', '2018-09-18 23:01:03', '4173629113', 'rejected', 1, '4.90', 'master', 32, '50.00', 1, NULL),
(297, 7, 'cartao_credito', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 19:22:06', '2018-09-18 19:22:06', 83, '2018-09-18 22:22:06', '2018-09-18 23:00:45', '4173358856', 'rejected', 1, '4.90', 'master', 33, '50.00', 1, NULL),
(298, 7, 'cartao_credito', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 19:27:08', '2018-09-18 19:27:08', 67, '2018-09-18 22:27:08', '2018-09-18 23:00:40', '4173361714', 'rejected', 1, '4.90', 'master', 34, '50.00', 1, NULL),
(299, 7, 'cartao_credito', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 19:32:16', '2018-09-18 19:32:16', 84, '2018-09-18 22:32:16', '2018-09-18 23:00:57', '4173636663', 'rejected', 1, '4.90', 'master', 35, '50.00', 1, NULL),
(300, 7, 'cartao_credito', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 19:43:40', '2018-09-18 19:43:40', 67, '2018-09-18 22:43:40', '2018-09-18 23:00:40', '4173503989', 'rejected', 1, '4.90', 'master', 36, '50.00', 1, NULL),
(301, 1, 'boleto', '54.90', '0.00', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 20:16:13', '2018-09-18 20:16:13', 85, '2018-09-18 23:16:13', '2018-09-18 23:16:15', '4173527828', 'pending', NULL, NULL, NULL, 37, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/ticket/helper?payment_id=4173527828&payment_method_reference_id=3430014844&caller_id=355544491&hash=882b9e3b-e03d-453a-ae94-ca54336834f6'),
(302, 3, 'cartao_credito', '54.90', '52.16', '1.00', '53.90', '', 'mercado_pago', '2018-09-18 20:17:06', '2018-09-18 20:17:06', 75, '2018-09-18 23:17:06', '2018-09-18 23:17:06', '4173528393', 'approved', 1, '54.90', 'master', 38, NULL, NULL, NULL),
(303, 7, 'cartao_credito', '4.90', '0.00', '1.00', '53.90', 'Cod', 'mercado_pago', '2018-09-18 20:30:07', '2018-09-18 20:30:07', 86, '2018-09-18 23:30:07', '2018-09-19 06:00:25', '4173808676', 'rejected', 1, '4.90', 'master', 39, '50.00', 1, NULL),
(304, 1, 'boleto', '54.90', '0.00', '1.00', '53.90', '', 'mercado_pago', '2018-09-21 19:10:21', '2018-09-21 19:10:21', 67, '2018-09-21 22:10:21', '2018-09-21 22:10:24', '15967154', 'pending', NULL, NULL, NULL, 40, NULL, NULL, 'https://www.mercadopago.com/mlb/payments/sandbox/ticket/helper?payment_id=15967154&payment_method_reference_id=15967155&caller_id=159428273&hash=3fa62231-be13-44a1-842f-6e451d29d560');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boletos`
--
ALTER TABLE `boletos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boletos_venda_foreign` (`venda`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comissoes`
--
ALTER TABLE `comissoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comissoes_venda_foreign` (`venda`);

--
-- Indexes for table `compradores`
--
ALTER TABLE `compradores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `entregas`
--
ALTER TABLE `entregas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fotos_plano_foreign` (`plano`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `pixels`
--
ALTER TABLE `pixels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `planos_cupons`
--
ALTER TABLE `planos_cupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `planos_cupons_cupom_foreign` (`cupom`),
  ADD KEY `planos_cupons_plano_foreign` (`plano`);

--
-- Indexes for table `planos_pixels`
--
ALTER TABLE `planos_pixels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `planos_pixels_plano_foreign` (`plano`),
  ADD KEY `planos_pixels_pixel_foreign` (`pixel`);

--
-- Indexes for table `planos_vendas`
--
ALTER TABLE `planos_vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `planos_vendas_plano_foreign` (`plano`),
  ADD KEY `planos_vendas_venda_foreign` (`venda`);

--
-- Indexes for table `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produtos_categoria_foreign` (`categoria`);

--
-- Indexes for table `produtos_planos`
--
ALTER TABLE `produtos_planos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produtos_planos_produto_foreign` (`produto`),
  ADD KEY `produtos_planos_plano_foreign` (`plano`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `users_empresas`
--
ALTER TABLE `users_empresas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_empresas_user_foreign` (`user`),
  ADD KEY `users_empresas_empresa_foreign` (`empresa`);

--
-- Indexes for table `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendas_comprador_foreign` (`comprador`),
  ADD KEY `vendas_entrega_foreign` (`entrega`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boletos`
--
ALTER TABLE `boletos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comissoes`
--
ALTER TABLE `comissoes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `compradores`
--
ALTER TABLE `compradores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entregas`
--
ALTER TABLE `entregas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `fotos`
--
ALTER TABLE `fotos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `pixels`
--
ALTER TABLE `pixels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `planos`
--
ALTER TABLE `planos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `planos_cupons`
--
ALTER TABLE `planos_cupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `planos_pixels`
--
ALTER TABLE `planos_pixels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `planos_vendas`
--
ALTER TABLE `planos_vendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `produtos_planos`
--
ALTER TABLE `produtos_planos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_empresas`
--
ALTER TABLE `users_empresas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `boletos`
--
ALTER TABLE `boletos`
  ADD CONSTRAINT `boletos_venda_foreign` FOREIGN KEY (`venda`) REFERENCES `vendas` (`id`);

--
-- Limitadores para a tabela `comissoes`
--
ALTER TABLE `comissoes`
  ADD CONSTRAINT `comissoes_venda_foreign` FOREIGN KEY (`venda`) REFERENCES `vendas` (`id`);

--
-- Limitadores para a tabela `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fotos_plano_foreign` FOREIGN KEY (`plano`) REFERENCES `planos` (`id`);

--
-- Limitadores para a tabela `planos_cupons`
--
ALTER TABLE `planos_cupons`
  ADD CONSTRAINT `planos_cupons_cupom_foreign` FOREIGN KEY (`cupom`) REFERENCES `cupons` (`id`),
  ADD CONSTRAINT `planos_cupons_plano_foreign` FOREIGN KEY (`plano`) REFERENCES `planos` (`id`);

--
-- Limitadores para a tabela `planos_pixels`
--
ALTER TABLE `planos_pixels`
  ADD CONSTRAINT `planos_pixels_pixel_foreign` FOREIGN KEY (`pixel`) REFERENCES `pixels` (`id`),
  ADD CONSTRAINT `planos_pixels_plano_foreign` FOREIGN KEY (`plano`) REFERENCES `planos` (`id`);

--
-- Limitadores para a tabela `planos_vendas`
--
ALTER TABLE `planos_vendas`
  ADD CONSTRAINT `planos_vendas_plano_foreign` FOREIGN KEY (`plano`) REFERENCES `planos` (`id`),
  ADD CONSTRAINT `planos_vendas_venda_foreign` FOREIGN KEY (`venda`) REFERENCES `vendas` (`id`);

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_categoria_foreign` FOREIGN KEY (`categoria`) REFERENCES `categorias` (`id`);

--
-- Limitadores para a tabela `produtos_planos`
--
ALTER TABLE `produtos_planos`
  ADD CONSTRAINT `produtos_planos_plano_foreign` FOREIGN KEY (`plano`) REFERENCES `planos` (`id`),
  ADD CONSTRAINT `produtos_planos_produto_foreign` FOREIGN KEY (`produto`) REFERENCES `produtos` (`id`);

--
-- Limitadores para a tabela `users_empresas`
--
ALTER TABLE `users_empresas`
  ADD CONSTRAINT `users_empresas_empresa_foreign` FOREIGN KEY (`empresa`) REFERENCES `empresas` (`id`),
  ADD CONSTRAINT `users_empresas_user_foreign` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Limitadores para a tabela `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_comprador_foreign` FOREIGN KEY (`comprador`) REFERENCES `compradores` (`id`),
  ADD CONSTRAINT `vendas_entrega_foreign` FOREIGN KEY (`entrega`) REFERENCES `entregas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
