-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2018 at 02:36 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.0.27

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
-- Table structure for table `leave_apply`
--

CREATE TABLE `leave_apply` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_emp_id` bigint(20) UNSIGNED NOT NULL,
  `user_type` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `financial_year` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `applied_by` int(11) UNSIGNED NOT NULL,
  `leave_reason` longtext,
  `leave_status` enum('0','1','2') NOT NULL DEFAULT '2' COMMENT '0=''denied'',1=''approved'',2=''pending''',
  `approved_by` int(11) UNSIGNED DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `leave_type` int(11) NOT NULL DEFAULT '1' COMMENT '1=paid, 2=casual, 3=sick, 4=other'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `leave_apply`
--

INSERT INTO `leave_apply` (`id`, `user_emp_id`, `user_type`, `financial_year`, `start_date`, `end_date`, `applied_by`, `leave_reason`, `leave_status`, `approved_by`, `created_date`, `leave_type`) VALUES
(1, 3, '0', '2018-07-31', '0000-00-00', '0000-00-00', 3, 'Doctor\'s appointment', '1', 5, '2018-07-28 10:32:19', 3),
(2, 2, '2', '2018-08-02', '0000-00-00', '0000-00-00', 2, 'oggo', '2', NULL, '2018-07-30 08:51:25', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `leave_apply`
--
ALTER TABLE `leave_apply`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_emp_id` (`user_emp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leave_apply`
--
ALTER TABLE `leave_apply`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
