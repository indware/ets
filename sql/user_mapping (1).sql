-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2018 at 07:25 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `brring`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_mapping`
--

CREATE TABLE `user_mapping` (
  `mapping_id` bigint(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('0','1','2','3','4') NOT NULL DEFAULT '1' COMMENT '0->Normal User,1-> Zonal,2->State,3->City,4->Supervisor',
  `type_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_mapping`
--

INSERT INTO `user_mapping` (`mapping_id`, `user_id`, `user_type`, `type_id`, `created_by`, `created_date`) VALUES
(4, 2, '', 1, 201, '1000-01-01 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user_mapping`
--
ALTER TABLE `user_mapping`
  ADD PRIMARY KEY (`mapping_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`user_type`,`type_id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user_mapping`
--
ALTER TABLE `user_mapping`
  MODIFY `mapping_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_mapping`
--
ALTER TABLE `user_mapping`
  ADD CONSTRAINT `user_mapping_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `employee_master` (`emp_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
