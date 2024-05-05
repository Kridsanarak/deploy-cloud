-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2024 at 11:58 AM
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
  `room_number` int(11) NOT NULL,
  `room_status` enum('Ready','Waiting','Not Ready') NOT NULL,
  `room_type` enum('ห้องเรียน','ห้องประชุม','ห้องปฎิบัติการ') NOT NULL,
  `toilet_gender` enum('male','female') DEFAULT NULL,
  `toilet_status` enum('Ready','Waiting','Not Ready') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `task_title`, `task_description`, `start_date`, `user_id`, `floor_number`, `room_number`, `room_status`, `room_type`, `toilet_gender`, `toilet_status`) VALUES
(1, 'Big Clean', NULL, '2024-05-06', 2, '5', 2, 'Waiting', 'ห้องเรียน', NULL, 'Ready'),
(2, 'Small', NULL, '2024-05-08', 3, '8', 2, 'Ready', 'ห้องประชุม', NULL, 'Ready');

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
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `username`, `password`, `role`, `lasttime_login`, `status`) VALUES
(1, 'admin', 'admin1', 'admin', 'admin', '2024-05-04 21:15:46', 'พร้อม'),
(2, 'แมว เหมียว', 'catcat', '1234', 'headmaid', '2024-05-04 21:46:25', 'ลาพัก'),
(3, 'Kirdsaanarak', '64160080', '1234', 'maid', '2024-05-05 02:34:33', '');

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
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
