-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18-Jun-2020 às 23:23
-- Versão do servidor: 10.4.11-MariaDB
-- versão do PHP: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `exemplo_login`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_example_logs`
--

CREATE TABLE `login_example_logs` (
  `logs_id` int(11) NOT NULL,
  `logs_user_id` int(11) NOT NULL,
  `logs_process` varchar(255) NOT NULL,
  `logs_message` text NOT NULL,
  `logs_json` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_example_sessions`
--

CREATE TABLE `login_example_sessions` (
  `session_id` int(11) NOT NULL,
  `session_token` varchar(100) NOT NULL,
  `session_expiration` timestamp NOT NULL DEFAULT current_timestamp(),
  `session_user_id` int(11) NOT NULL,
  `session_console_name` text NOT NULL,
  `session_console_type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_example_users`
--

CREATE TABLE `login_example_users` (
  `user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(20) NOT NULL,
  `user_subscription_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_last_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_clearance` int(11) NOT NULL DEFAULT 1,
  `user_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `login_example_users`
--

INSERT INTO `login_example_users` (`user_id`, `user_email`, `user_password`, `user_subscription_date`, `user_last_login`, `user_clearance`, `user_status`) VALUES
(1, 'a@a', '123123', '2020-06-18 19:55:23', '2020-06-18 19:55:23', 1, 1);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `login_example_logs`
--
ALTER TABLE `login_example_logs`
  ADD PRIMARY KEY (`logs_id`);

--
-- Índices para tabela `login_example_sessions`
--
ALTER TABLE `login_example_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Índices para tabela `login_example_users`
--
ALTER TABLE `login_example_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `login_example_logs`
--
ALTER TABLE `login_example_logs`
  MODIFY `logs_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `login_example_sessions`
--
ALTER TABLE `login_example_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `login_example_users`
--
ALTER TABLE `login_example_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
