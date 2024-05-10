-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2024 at 09:37 PM
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
  `room_type` enum('lecture','meeting','lab') DEFAULT NULL,
  `toilet_gender` enum('male','female') DEFAULT NULL,
  `toilet_status` enum('Ready','Waiting','Not Ready') NOT NULL,
  `image` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `task_title`, `task_description`, `start_date`, `user_id`, `floor_number`, `room_number`, `room_status`, `room_type`, `toilet_gender`, `toilet_status`, `image`) VALUES
(1, 'Big Clean', NULL, '2024-05-08', 2, '5', '1', 'Ready', 'lecture', NULL, 'Ready', 'wallpaperflare.com_wallpaper.jpg'),
(2, 'Small', NULL, '2024-05-08', 3, '8', '1', 'Ready', 'meeting', NULL, 'Ready', NULL),
(3, 'Task 70', NULL, '2024-05-13', 3, '8', '5', NULL, 'lab', 'female', 'Waiting', NULL),
(4, 'Task 2', NULL, '2024-05-13', 3, '5', '2', 'Waiting', 'meeting', 'male', 'Ready', NULL),
(5, 'Task 79', NULL, '2024-05-08', 3, '10', '5', 'Ready', 'meeting', 'male', 'Ready', NULL),
(6, 'Task 59', NULL, '2024-05-14', 3, '1', '2', 'Not Ready', 'lab', 'male', 'Ready', NULL),
(7, 'Task 65', NULL, '2024-05-11', 2, '4', '4', 'Ready', 'lab', 'female', 'Not Ready', NULL),
(8, 'Task 53', NULL, '2024-05-08', 2, '5', '3', 'Not Ready', 'lecture', 'female', 'Ready', NULL),
(9, 'Task 28', NULL, '2024-05-11', 3, '10', '6', 'Waiting', 'meeting', 'male', 'Waiting', NULL),
(10, 'Task 39', NULL, '2024-05-10', 2, '11', '2', 'Not Ready', NULL, 'male', 'Waiting', NULL),
(11, 'Task 11', NULL, '2024-05-08', 2, '8', '2', NULL, 'lecture', 'female', 'Waiting', NULL),
(12, 'Task 62', NULL, '2024-05-11', 3, '9', '1', 'Waiting', 'meeting', 'male', 'Not Ready', NULL),
(13, 'Task 54', NULL, '2024-05-06', 4, '3', '6', 'Waiting', 'meeting', 'male', 'Ready', NULL),
(14, 'Task 20', NULL, '2024-05-08', 3, '11', '5', 'Waiting', 'lab', 'female', 'Waiting', NULL),
(15, 'Task 0', NULL, '2024-05-09', 2, '10', '6', NULL, 'lecture', 'female', 'Waiting', NULL),
(16, 'Task 85', NULL, '2024-05-13', 4, '10', '3', 'Ready', 'lab', 'male', 'Waiting', NULL),
(17, 'Task 43', NULL, '2024-05-14', 3, '3', '1', 'Not Ready', 'lab', 'female', 'Waiting', NULL),
(18, 'Task 29', NULL, '2024-05-12', 3, '11', '1', 'Not Ready', NULL, 'male', 'Waiting', NULL),
(19, 'Task 6', NULL, '2024-05-09', 3, '7', '6', 'Not Ready', 'meeting', 'male', 'Not Ready', NULL),
(20, 'Task 12', NULL, '2024-05-11', 2, '6', '1', 'Ready', 'meeting', 'female', 'Not Ready', NULL),
(21, 'Task 35', NULL, '2024-05-09', 4, '4', '2', 'Waiting', 'lab', 'female', 'Waiting', NULL),
(22, 'Task 39', NULL, '2024-05-09', 4, '6', '2', 'Waiting', 'meeting', 'female', 'Not Ready', NULL),
(23, 'Task 66', NULL, '2024-05-14', 2, '6', '6', 'Waiting', 'lab', 'female', 'Ready', NULL),
(24, 'Task 62', NULL, '2024-05-10', 4, '9', '6', 'Waiting', 'lecture', 'female', 'Waiting', NULL),
(25, 'Task 11', NULL, '2024-05-08', 2, '4', '2', 'Waiting', 'meeting', 'female', 'Waiting', NULL),
(26, 'Task 92', NULL, '2024-05-14', 3, '6', '2', 'Waiting', 'lab', 'male', 'Waiting', NULL),
(27, 'Task 29', NULL, '2024-05-06', 4, '1', '5', 'Not Ready', 'lecture', 'male', 'Waiting', NULL),
(28, 'Task 71', NULL, '2024-05-15', 4, '10', NULL, 'Not Ready', 'meeting', 'male', 'Ready', NULL),
(29, 'Task 70', NULL, '2024-05-07', 2, '10', '3', 'Not Ready', 'meeting', 'female', 'Waiting', NULL),
(30, 'Task 84', NULL, '2024-05-11', 3, '8', '4', 'Waiting', 'lab', 'male', 'Ready', NULL),
(31, 'Task 4', NULL, '2024-05-09', 2, '1', '1', NULL, 'lab', 'male', 'Waiting', NULL),
(32, 'Task 68', NULL, '2024-05-08', 2, '5', '4', 'Waiting', 'meeting', 'male', 'Waiting', NULL),
(33, 'Task 44', NULL, '2024-05-08', 4, '6', '1', 'Ready', 'lecture', 'male', 'Not Ready', NULL),
(34, 'Task 84', NULL, '2024-05-13', 3, '11', '3', 'Ready', 'lecture', 'male', 'Ready', NULL),
(35, 'Task 40', NULL, '2024-05-09', 2, '11', '1', 'Not Ready', 'lecture', 'female', 'Ready', NULL),
(36, 'Task 59', NULL, '2024-05-09', 4, '8', '6', NULL, NULL, 'female', 'Not Ready', NULL),
(37, 'Task 93', NULL, '2024-05-15', 2, '9', '2', 'Waiting', 'meeting', 'male', 'Waiting', NULL),
(38, 'Task 16', NULL, '2024-05-12', 4, '4', '6', 'Ready', 'lecture', 'male', 'Ready', NULL),
(39, 'Task 88', NULL, '2024-05-08', 4, '3', '4', 'Not Ready', 'lecture', 'female', 'Ready', NULL),
(40, 'Task 98', NULL, '2024-05-09', 4, '9', '4', 'Waiting', 'meeting', 'male', 'Waiting', NULL),
(41, 'Task 11', NULL, '2024-05-06', 3, '11', '1', 'Ready', 'lab', 'female', 'Waiting', NULL),
(42, 'Task 82', NULL, '2024-05-12', 4, '10', '4', 'Not Ready', 'meeting', 'male', 'Not Ready', NULL),
(43, 'Task 27', NULL, '2024-05-07', 2, '9', '2', NULL, 'lecture', 'female', 'Ready', NULL),
(44, 'Task 86', NULL, '2024-05-13', 3, '8', '3', 'Not Ready', 'lecture', 'male', 'Not Ready', NULL),
(45, 'Task 85', NULL, '2024-05-09', 2, '3', NULL, 'Waiting', 'lecture', 'male', 'Not Ready', NULL),
(46, 'Task 17', NULL, '2024-05-15', 3, '11', '4', 'Not Ready', 'lecture', 'male', 'Waiting', NULL),
(47, 'Task 89', NULL, '2024-05-09', 4, '2', '3', 'Not Ready', NULL, 'male', 'Ready', NULL),
(48, 'Task 73', NULL, '2024-05-06', 4, '5', '6', 'Ready', 'lab', 'male', 'Not Ready', NULL),
(49, 'Task 9', NULL, '2024-05-08', 4, '1', '6', 'Waiting', 'lab', 'female', 'Not Ready', NULL),
(50, '1', '', '2024-05-09', 4, '4', '3', 'Waiting', 'lecture', 'male', 'Waiting', NULL);

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
(1, 'admin', 'admin1', 'admin', 'admin', '2024-05-04 21:15:46', 'พร้อม'),
(2, 'แมว เหมียว', 'catcat', '1234', 'headmaid', '2024-05-09 02:36:43', 'พร้อม'),
(3, 'eakkachai', '123123', '1212312121', 'headmaid', '2024-05-06 17:17:50', 'ไม่พร้อม'),
(4, 'น้องปอน', 'อยากมีเมียน้อย', '20', 'maid', '', 'พร้อม');

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
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

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
