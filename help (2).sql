-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2024 at 08:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `help`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_station`
--

CREATE TABLE `tbl_station` (
  `id` int(11) NOT NULL,
  `station_id` varchar(255) DEFAULT NULL,
  `station_name` varchar(255) DEFAULT NULL,
  `station_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_station`
--

INSERT INTO `tbl_station` (`id`, `station_id`, `station_name`, `station_type`) VALUES
(13, 'BC168', 'station_name', 'CoCo'),
(18, '168', 'super', 'DoDo'),
(19, '777', 'ptt', 'CoCo');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ticket`
--

CREATE TABLE `tbl_ticket` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(255) DEFAULT NULL,
  `station_id` varchar(255) DEFAULT NULL,
  `station_name` varchar(255) DEFAULT NULL,
  `station_type` varchar(255) DEFAULT NULL,
  `issue_description` longtext DEFAULT NULL,
  `issue_image` varchar(255) DEFAULT NULL,
  `issue_type` varchar(255) DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `users_id` int(100) DEFAULT NULL,
  `ticket_open` datetime DEFAULT NULL,
  `ticket_on_hold` datetime DEFAULT NULL,
  `ticket_in_progress` datetime DEFAULT NULL,
  `ticket_pending_vender` datetime DEFAULT NULL,
  `ticket_close` datetime DEFAULT NULL,
  `comment` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ticket`
--

INSERT INTO `tbl_ticket` (`id`, `ticket_id`, `station_id`, `station_name`, `station_type`, `issue_description`, `issue_image`, `issue_type`, `priority`, `status`, `users_id`, `ticket_open`, `ticket_on_hold`, `ticket_in_progress`, `ticket_pending_vender`, `ticket_close`, `comment`) VALUES
(5, 'POS2406000001', 'BC168', 'station_name', 'CoCo', '123', '', 'Dispensor', 'CAT Hardware', 'Close', 37, '2024-06-30 23:57:40', NULL, NULL, NULL, '2024-07-01 13:32:11', ''),
(40, 'POS2407000009', 'BC168', 'station_name', 'CoCo', 'rgsfd', '', 'Hardware', '', NULL, NULL, '2024-07-01 15:55:29', NULL, NULL, NULL, NULL, NULL),
(41, 'POS2407000010', '777', 'ptt', 'CoCo', 'tyrhg', '', 'Dispensor', 'CAT Hardware', 'On Hold', 36, '2024-07-01 15:55:34', NULL, NULL, NULL, NULL, ''),
(43, 'POS2407000011', 'BC168', 'station_name', 'CoCo', 'dfgb', '', 'Dispensor', '', NULL, 39, '2024-07-01 15:58:55', NULL, NULL, NULL, NULL, NULL),
(44, 'POS2407000012', '777', 'ptt', 'CoCo', '7sd', '', 'Dispensor', '', NULL, NULL, '2024-07-01 15:59:15', NULL, NULL, NULL, NULL, NULL),
(45, 'POS2407000013', '777', 'ptt', 'CoCo', 'gsd', '', 'Dispensor', '', NULL, NULL, '2024-07-01 15:59:43', NULL, NULL, NULL, NULL, NULL),
(46, 'POS2407000014', '777', 'ptt', 'CoCo', 'ythg', '', 'Dispensor', '', NULL, NULL, '2024-07-01 15:59:49', NULL, NULL, NULL, NULL, NULL),
(47, 'POS2407000015', '777', 'ptt', 'CoCo', 'dfg', '', 'Dispensor', '', NULL, NULL, '2024-07-01 16:00:33', NULL, NULL, NULL, NULL, NULL),
(48, 'POS2407000016', '777', 'ptt', 'CoCo', 'tgfsd', '', 'Hardware', '', NULL, NULL, '2024-07-01 16:00:38', NULL, NULL, NULL, NULL, NULL),
(49, 'POS2407000017', '777', 'ptt', 'CoCo', 'rfdf', '', 'Hardware', '', NULL, NULL, '2024-07-01 16:00:44', NULL, NULL, NULL, NULL, NULL),
(50, 'POS2407000018', '777', 'ptt', 'CoCo', 'sfd', '', 'Dispensor', '', NULL, NULL, '2024-07-01 16:13:33', NULL, NULL, NULL, NULL, NULL),
(51, 'POS2407000019', '777', 'ptt', 'CoCo', 'sfdg', '', 'Dispensor', '', NULL, NULL, '2024-07-01 16:13:40', NULL, NULL, NULL, NULL, NULL),
(52, 'POS2407000020', '777', 'ptt', 'CoCo', 'asdfg', '', 'Dispensor', '', NULL, NULL, '2024-07-01 16:13:47', NULL, NULL, NULL, NULL, NULL),
(53, 'POS2407000021', '777', 'ptt', 'CoCo', 'asdfsd', '', 'Dispensor, Hardware', '', NULL, NULL, '2024-07-01 16:14:01', NULL, NULL, NULL, NULL, NULL),
(54, 'POS2407000022', '777', 'ptt', 'CoCo', 'asddf', '', 'Dispensor, Hardware', '', NULL, NULL, '2024-07-01 16:14:09', NULL, NULL, NULL, NULL, NULL),
(55, 'POS2407000023', '777', 'ptt', 'CoCo', 'asdf', '', 'Dispensor', '', NULL, NULL, '2024-07-01 16:14:17', NULL, NULL, NULL, NULL, NULL),
(56, 'POS2407000024', '777', 'ptt', 'CoCo', 'asdf', '', 'Hardware', 'CAT Hardware', 'On Hold', 40, '2024-07-01 16:14:24', NULL, NULL, NULL, NULL, ''),
(57, 'POS2407000025', '777', 'ptt', 'CoCo', 'adfs', '', 'Hardware', 'CAT Hardware', 'On Hold', 37, '2024-07-01 16:14:33', NULL, NULL, NULL, NULL, ''),
(58, 'POS2407000026', '777', 'ptt', 'CoCo', 'asdf', '', 'Dispensor', 'CAT Hardware', 'On Hold', 36, '2024-07-01 16:14:40', NULL, NULL, NULL, NULL, ''),
(59, 'POS2407000027', '777', 'ptt', 'CoCo', 'asdfrew', '', 'Dispensor', 'CAT Hardware', 'On Hold', 37, '2024-07-01 16:14:48', NULL, NULL, NULL, NULL, ''),
(61, 'POS2407000028', '168', 'super', 'DoDo', 'adafg', '', 'Hardware', '', NULL, NULL, '2024-07-02 08:34:31', NULL, NULL, NULL, NULL, NULL),
(62, 'POS2407000029', '168', 'super', 'DoDo', 'sdasd', '', 'Hardware', '', NULL, NULL, '2024-07-02 08:57:22', NULL, NULL, NULL, NULL, NULL),
(65, 'POS2407000030', '168', 'super', 'DoDo', 'eadf', '', 'Dispensor', 'CAT Hardware', 'On Hold', 36, '2024-07-02 11:23:20', NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `users_id` int(11) NOT NULL,
  `users_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` mediumint(50) NOT NULL,
  `status` text NOT NULL,
  `rules_id` int(11) DEFAULT NULL,
  `company` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`users_id`, `users_name`, `email`, `password`, `code`, `status`, `rules_id`, `company`) VALUES
(36, 'brilliance', 'broakzinll29@gmail.com', '$2y$10$U1/rBhe4.VnV3h1zwBhDVObVycBUwEjmRXe7RS8vdR9bzk45qR7o6', 0, '1', 1461, 'PTTCL'),
(37, 'brilliant', 'nun@ptt.com', '$2y$10$9e6KqmpVXo.eqHRSA1QveOW78cvc2YUpOtNnl/W6wh1liHkCto2gO', 0, '1', 1461, 'PTTDigital'),
(39, 'nun', 'ptt@cl.com', '$2y$10$Rpie4YQHdgY13YOIHoAfeeU.rC07FxZ4YBHhzI8rUhGIDkQe9sxUK', 0, '1', 1461, 'PTTCL'),
(40, 'bronun', 'nenzigaming@gmail.com', '$2y$10$WSk6LmPKLL.hxXxACTyCR.spXPPgcGovabvQWP9I.PyoM1080UDvG', 0, '1', 1461, 'PTTCL');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users_rules`
--

CREATE TABLE `tbl_users_rules` (
  `rules_id` int(11) NOT NULL,
  `rules_name` varchar(255) DEFAULT NULL,
  `add_user_status` tinyint(4) DEFAULT NULL,
  `edit_user_status` tinyint(4) DEFAULT NULL,
  `delete_user_status` tinyint(4) DEFAULT NULL,
  `list_user_status` tinyint(4) DEFAULT NULL,
  `add_ticket_status` tinyint(4) DEFAULT NULL,
  `edit_ticket_status` tinyint(4) DEFAULT NULL,
  `delete_ticket_status` tinyint(4) DEFAULT NULL,
  `list_ticket_status` tinyint(4) DEFAULT NULL,
  `add_user_rules` tinyint(4) DEFAULT NULL,
  `edit_user_rules` tinyint(4) DEFAULT NULL,
  `delete_user_rules` tinyint(4) DEFAULT NULL,
  `list_user_rules` tinyint(4) DEFAULT NULL,
  `add_station` tinyint(4) DEFAULT NULL,
  `edit_station` tinyint(4) DEFAULT NULL,
  `delete_station` tinyint(4) DEFAULT NULL,
  `list_station` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users_rules`
--

INSERT INTO `tbl_users_rules` (`rules_id`, `rules_name`, `add_user_status`, `edit_user_status`, `delete_user_status`, `list_user_status`, `add_ticket_status`, `edit_ticket_status`, `delete_ticket_status`, `list_ticket_status`, `add_user_rules`, `edit_user_rules`, `delete_user_rules`, `list_user_rules`, `add_station`, `edit_station`, `delete_station`, `list_station`) VALUES
(1461, 'Admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(1466, 'user', 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_station`
--
ALTER TABLE `tbl_station`
  ADD PRIMARY KEY (`id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`users_id`),
  ADD KEY `rules_id` (`rules_id`);

--
-- Indexes for table `tbl_users_rules`
--
ALTER TABLE `tbl_users_rules`
  ADD PRIMARY KEY (`rules_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_station`
--
ALTER TABLE `tbl_station`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tbl_users_rules`
--
ALTER TABLE `tbl_users_rules`
  MODIFY `rules_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1467;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  ADD CONSTRAINT `tbl_ticket_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `tbl_station` (`station_id`);

--
-- Constraints for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD CONSTRAINT `tbl_users_ibfk_1` FOREIGN KEY (`rules_id`) REFERENCES `tbl_users_rules` (`rules_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
