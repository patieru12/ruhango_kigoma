-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 13, 2020 at 11:38 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `care_full_v1`
--

-- --------------------------------------------------------

--
-- Table structure for table `msg_data`
--

CREATE TABLE `msg_data` (
  `id` int(11) NOT NULL,
  `sendId` int(11) NOT NULL,
  `receiverId` int(11) NOT NULL,
  `msgText` text NOT NULL,
  `sentAt` int(11) NOT NULL,
  `readAt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `msg_data`
--
ALTER TABLE `msg_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sendId` (`sendId`),
  ADD KEY `receiverId` (`receiverId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `msg_data`
--
ALTER TABLE `msg_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `msg_data`
--
ALTER TABLE `msg_data`
  ADD CONSTRAINT `msg_data_receiver_foreign_key` FOREIGN KEY (`receiverId`) REFERENCES `sy_users` (`UserID`),
  ADD CONSTRAINT `msg_data_sender_foreign_key` FOREIGN KEY (`sendId`) REFERENCES `sy_users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
