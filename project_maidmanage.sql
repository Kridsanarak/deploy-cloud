-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2024 at 04:37 PM
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
-- Database: `project_maidmanage`
--

-- --------------------------------------------------------

--
-- Table structure for table `floor`
--

CREATE TABLE `floor` (
  `floor_id` int(11) NOT NULL,
  `floor_no` enum('ชั้น 1','ชั้น 2','ชั้น 3','ชั้น 4','ชั้น 5','ชั้น 6','ชั้น 7','ชั้น 8','ชั้น 9','ชั้น 10','ชั้น 11') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `floor`
--

INSERT INTO `floor` (`floor_id`, `floor_no`) VALUES
(1, 'ชั้น 1'),
(2, 'ชั้น 2'),
(3, 'ชั้น 3'),
(4, 'ชั้น 4'),
(5, 'ชั้น 5'),
(6, 'ชั้น 6'),
(7, 'ชั้น 7'),
(8, 'ชั้น 8'),
(9, 'ชั้น 9'),
(10, 'ชั้น 10'),
(11, 'ชั้น 11');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(3) NOT NULL,
  `role_name` enum('admin','headmaid','maid') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'headmaid'),
(3, 'maid');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(255) NOT NULL,
  `room_name` enum('IF-11M280','IF-10M60','IF-10M56','IF-10M32','IF-8R07','IF-7T01','IF-7T02','IF-7T03','IF-7T04','IF-7T05','IF-710','IF-6T01','IF-6T02','IF-6T03','IF-6T04','IF-6T05','IF-610','IF-5T01','IF-5T02','IF-5T03','IF-5T04','IF-5T05','IF-510','IF-5M210','IF-4C01','IF-4C02','IF-4C03','IF-4C04','IF-4M210','IF-3C01','IF-3C02','IF-3C03','IF-3C04','IF-3M210','IF-308','IF-212','-') NOT NULL,
  `floor_id` int(11) NOT NULL,
  `room_type_id` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `room_name`, `floor_id`, `room_type_id`) VALUES
(1, 'IF-212', 2, 2),
(2, 'IF-3C01', 3, 3),
(3, 'IF-3C02', 3, 3),
(4, 'IF-3C03', 3, 3),
(5, 'IF-3C04', 3, 3),
(6, 'IF-3M210', 3, 2),
(7, 'IF-308', 3, 2),
(8, 'IF-4C01', 4, 3),
(9, 'IF-4C02', 4, 3),
(10, 'IF-4C03', 4, 3),
(11, 'IF-4C04', 4, 3),
(12, 'IF-4M210', 4, 2),
(13, 'IF-5T01', 5, 1),
(14, 'IF-5T02', 5, 1),
(15, 'IF-5T03', 5, 1),
(16, 'IF-5T04', 5, 1),
(17, 'IF-5T05', 5, 1),
(18, 'IF-510', 5, 2),
(19, 'IF-5M210', 5, 2),
(20, 'IF-6T01', 6, 1),
(21, 'IF-6T02', 6, 1),
(22, 'IF-6T03', 6, 1),
(23, 'IF-6T04', 6, 1),
(24, 'IF-6T05', 6, 1),
(25, 'IF-610', 6, 2),
(26, 'IF-7T01', 7, 1),
(27, 'IF-7T02', 7, 1),
(28, 'IF-7T03', 7, 1),
(29, 'IF-7T04', 7, 1),
(30, 'IF-7T05', 7, 1),
(31, 'IF-710', 7, 2),
(32, 'IF-8R07', 8, 2),
(33, 'IF-10M32', 10, 2),
(34, 'IF-10M56', 10, 2),
(35, 'IF-10M60', 10, 2),
(36, 'IF-11M280', 11, 2);

-- --------------------------------------------------------

--
-- Table structure for table `room_type`
--

CREATE TABLE `room_type` (
  `room_type_id` int(100) NOT NULL,
  `room_type_name` enum('Lecture','Meeting','Lab','-') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type`
--

INSERT INTO `room_type` (`room_type_id`, `room_type_name`) VALUES
(1, 'Lecture'),
(2, 'Meeting'),
(3, 'Lab'),
(4, '-');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int(3) NOT NULL,
  `status_name` enum('Ready','Waiting','Not Ready') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `status_name`) VALUES
(1, 'Ready'),
(2, 'Waiting'),
(3, 'Not Ready');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` int(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `user_id` int(255) NOT NULL,
  `floor_id` int(255) NOT NULL,
  `room_id` int(255) DEFAULT NULL,
  `status_id` int(255) DEFAULT NULL,
  `toilet_status_id` int(255) DEFAULT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `start_date`, `end_date`, `user_id`, `floor_id`, `room_id`, `status_id`, `toilet_status_id`, `image`) VALUES
(1, '2024-07-19', '2024-07-19', 3, 1, NULL, NULL, 3, 'image.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `toilet_status`
--

CREATE TABLE `toilet_status` (
  `toilet_status_id` int(11) NOT NULL,
  `toilet_status_name` enum('Ready','Waiting','Not Ready') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toilet_status`
--

INSERT INTO `toilet_status` (`toilet_status_id`, `toilet_status_name`) VALUES
(1, 'Ready'),
(2, 'Waiting'),
(3, 'Not Ready');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(100) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role_id` int(255) NOT NULL,
  `timestamp` varchar(255) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `username`, `password`, `role_id`, `timestamp`, `status_id`) VALUES
(1, 'admin', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, '2024-07-18 01:04:50', 1),
(2, 'head', 'head', '356a192b7913b04c54574d18c28d46e6395428ab', 2, '2024-07-17 12:22:59', 1),
(3, 'maid', 'maid', 'da4b9237bacccdf19c0760cab7aec4a8359010b0', 3, '2024-07-17 12:21:42', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `floor`
--
ALTER TABLE `floor`
  ADD PRIMARY KEY (`floor_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `floor_id` (`floor_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `room_type`
--
ALTER TABLE `room_type`
  ADD PRIMARY KEY (`room_type_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `floor_id` (`floor_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `toilet_status_id` (`toilet_status_id`);

--
-- Indexes for table `toilet_status`
--
ALTER TABLE `toilet_status`
  ADD PRIMARY KEY (`toilet_status_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `status_id` (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `floor`
--
ALTER TABLE `floor`
  MODIFY `floor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `room_type_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--


--
-- AUTO_INCREMENT for table `toilet_status`
--
ALTER TABLE `toilet_status`
  MODIFY `toilet_status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`floor_id`) REFERENCES `floor` (`floor_id`),
  ADD CONSTRAINT `room_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_type` (`room_type_id`);

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `task_ibfk_2` FOREIGN KEY (`floor_id`) REFERENCES `floor` (`floor_id`),
  ADD CONSTRAINT `task_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`),
  ADD CONSTRAINT `task_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`),
  ADD CONSTRAINT `task_ibfk_6` FOREIGN KEY (`toilet_status_id`) REFERENCES `toilet_status` (`toilet_status_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
