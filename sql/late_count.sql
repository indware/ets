-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2018 at 04:46 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.7

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
-- Table structure for table `late_count`
--

CREATE TABLE `late_count` (
  `id` bigint(20) NOT NULL,
  `emp_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time NOT NULL,
  `time_to_lat` float(12,8) DEFAULT NULL,
  `time_to_lng` float(12,8) DEFAULT NULL,
  `late_reason` longtext NOT NULL,
  `late_status` enum('0','1','2') NOT NULL DEFAULT '2' COMMENT '0=''denied'',1=''approved'',2=''pending''',
  `approved_by` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `late_count`
--
ALTER TABLE `late_count`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `late_count`
--
ALTER TABLE `late_count`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `late_count`
--
ALTER TABLE `late_count`
  ADD CONSTRAINT `late_count_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employee_master` (`emp_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
