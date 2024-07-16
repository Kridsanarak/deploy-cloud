-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2024 at 01:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `user_id` int(100) NOT NULL,
  `floor_number` enum('1','2','3','4','5','6','7','8','9','10','11') NOT NULL,
  `room_number` enum('1','2','3','4','5','6') DEFAULT NULL,
  `room_status` enum('Ready','Waiting','Not Ready') DEFAULT NULL,
  `room_type` enum('ห้องเรียน','ห้องประชุม','ห้องปฎิบัติการ') DEFAULT NULL,
  `toilet_gender` enum('male','female') DEFAULT NULL,
  `toilet_status` enum('Ready','Waiting','Not Ready') NOT NULL,
  `image` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `task_title`, `task_description`, `start_date`, `user_id`, `floor_number`, `room_number`, `room_status`, `room_type`, `toilet_gender`, `toilet_status`, `image`) VALUES
(1, '3', '2', '2024-07-16', 2, '4', '2', 'Waiting', '', 'female', 'Ready', NULL),
(2, '1', '1', '2024-07-16', 3, '8', '2', 'Ready', '', 'male', 'Waiting', NULL),
(3, '2', 'กกก', '2024-07-16', 3, '6', '3', 'Ready', '', 'female', 'Ready', NULL),
(4, '4', 'qwe', '2024-07-16', 3, '8', '3', 'Waiting', '', 'male', 'Waiting', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(100) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `lasttime_login` varchar(255) NOT NULL,
  `status` enum('พร้อม','ลา','ไม่พร้อม','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `username`, `password`, `role`, `lasttime_login`, `status`) VALUES
(1, 'admin', 'admin1', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', '2024-07-02 12:55:05', 'พร้อม'),
(2, 'head', 'head', '356a192b7913b04c54574d18c28d46e6395428ab', 'headmaid', '2024-07-02 12:47:19', 'พร้อม'),
(3, 'maid', 'maid', 'da4b9237bacccdf19c0760cab7aec4a8359010b0', 'maid', '2024-07-02 13:00:12', 'พร้อม'),
(4, 'Pond', 'pond', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'admin', '2024-07-02 11:37:38', 'พร้อม');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
