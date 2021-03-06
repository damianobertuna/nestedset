-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2021 at 10:35 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nestedset`
--

--
-- Dumping data for table `node_tree`
--

INSERT INTO `node_tree` (`idNode`, `level`, `iLeft`, `iRight`) VALUES
(1, 2, 2, 3),
(2, 2, 4, 5),
(3, 2, 6, 7),
(4, 2, 8, 9),
(5, 1, 1, 24),
(6, 2, 10, 11),
(7, 2, 12, 19),
(8, 3, 15, 16),
(9, 3, 17, 18),
(10, 2, 20, 21),
(11, 3, 13, 14),
(12, 2, 22, 23);

--
-- Dumping data for table `node_tree_names`
--

INSERT INTO `node_tree_names` (`idNode`, `language`, `nodeName`) VALUES
(1, 'english', 'Marketing'),
(1, 'italian', 'Marketing'),
(2, 'english', 'Helpdesk'),
(2, 'italian', 'Supporto Tecnico'),
(3, 'english', 'Managers'),
(3, 'italian', 'Managers'),
(4, 'english', 'Customer Account'),
(4, 'italian', 'Assistenza Cliente'),
(5, 'english', 'Docebo'),
(5, 'italian', 'Docebo'),
(6, 'english', 'Accounting'),
(6, 'italian', 'Amministrazione'),
(7, 'english', 'Sales'),
(7, 'italian', 'Supporto Vendite'),
(8, 'english', 'Italy'),
(8, 'italian', 'Italia'),
(9, 'english', 'Europe'),
(9, 'italian', 'Europa'),
(10, 'english', 'Developers'),
(10, 'italian', 'Sviluppatori'),
(11, 'english', 'North America'),
(11, 'italian', 'Nord America'),
(12, 'english', 'Quality Assurance'),
(12, 'italian', 'Controllo Qualità');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
