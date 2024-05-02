-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2024 at 08:38 PM
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
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `floor_number` int(11) DEFAULT NULL,
  `room_number` int(11) DEFAULT NULL,
  `toilet_gender` varchar(4) DEFAULT NULL,
  `room_type` varchar(14) DEFAULT NULL,
  `toilet_clean` varchar(3) DEFAULT NULL,
  `room_clean` varchar(3) DEFAULT NULL,
  `toilet_paper_supply` varchar(50) DEFAULT NULL,
  `room_furniture` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`floor_number`, `room_number`, `toilet_gender`, `room_type`, `toilet_clean`, `room_clean`, `toilet_paper_supply`, `room_furniture`) VALUES
(3, 2, 'หญิง', 'ห้องเรียน', 'ํํY', 'N', '0', 'เก้าอี้'),
(4, 4, '-', 'ห้องประชุม', 'N', 'N', '0', 'โต๊ะ'),
(10, 6, 'ชาย', 'ห้องเรียน', 'ํํY', 'N', '0', 'โต๊ะ'),
(8, 3, 'หญิง', 'ห้องปฎิบัติการ', 'N', 'Y', '1', 'โต๊ะ'),
(7, 6, 'หญิง', 'ห้องเรียน', 'N', 'Y', '1', '-'),
(3, 1, 'ชาย', 'ห้องเรียน', 'ํํY', 'Y', '1', '-'),
(5, 3, 'ชาย', 'ห้องประชุม', 'ํํY', 'Y', '0', 'เก้าอี้'),
(4, 1, '-', 'ห้องประชุม', 'N', 'N', '0', 'โต๊ะ'),
(3, 1, 'ชาย', 'ห้องปฎิบัติการ', 'ํํY', 'N', '1', '-'),
(7, 5, 'หญิง', 'ห้องประชุม', 'N', 'Y', '1', '-'),
(2, 3, '-', 'ห้องเรียน', 'N', 'Y', '0', '-'),
(3, 4, '-', 'ห้องประชุม', 'ํํY', 'N', '0', '-'),
(3, 6, 'หญิง', 'ห้องประชุม', 'N', 'N', '0', 'โต๊ะ'),
(7, 5, 'หญิง', 'ห้องปฎิบัติการ', 'ํํY', 'N', '1', 'เก้าอี้'),
(4, 5, 'ชาย', 'ห้องประชุม', 'ํํY', 'N', '1', 'เก้าอี้'),
(4, 4, 'ชาย', 'ห้องปฎิบัติการ', 'N', 'Y', '0', 'เก้าอี้'),
(3, 3, 'หญิง', 'ห้องปฎิบัติการ', 'N', 'Y', '0', '-'),
(8, 2, '-', 'ห้องปฎิบัติการ', 'N', 'Y', '1', 'โต๊ะ'),
(5, 6, 'ชาย', 'ห้องปฎิบัติการ', 'N', 'N', '1', '-'),
(9, 6, '-', 'ห้องประชุม', 'N', 'N', '1', '-');

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
(1, 'admin', 'admin1', 'admin', 'admin', '2024-04-29 01:00:49', 'พร้อม'),
(2, 'แมว เหมียว', 'catcat', '1234', 'headmaid', '2024-04-29 17:40:11', 'ลาพัก');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
