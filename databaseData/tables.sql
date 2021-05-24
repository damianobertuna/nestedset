-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2021 at 10:33 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `node_tree`
--

CREATE TABLE `node_tree` (
  `idNode` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `iLeft` int(11) NOT NULL,
  `iRight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `node_tree_names`
--

CREATE TABLE `node_tree_names` (
  `idNode` int(11) NOT NULL,
  `language` varchar(50) COLLATE utf8_bin NOT NULL,
  `nodeName` varchar(150) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `node_tree`
--
ALTER TABLE `node_tree`
  ADD PRIMARY KEY (`idNode`),
  ADD KEY `idNode` (`idNode`);

--
-- Indexes for table `node_tree_names`
--
ALTER TABLE `node_tree_names`
  ADD KEY `idNode` (`idNode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `node_tree`
--
ALTER TABLE `node_tree`
  MODIFY `idNode` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `node_tree_names`
--
ALTER TABLE `node_tree_names`
  ADD CONSTRAINT `node_tree_names_ibfk_1` FOREIGN KEY (`idNode`) REFERENCES `node_tree` (`idNode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
