-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2025 at 04:52 PM
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
-- Database: `celestia_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `credentialId` int(11) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `appPassword` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`credentialId`, `emailAddress`, `appPassword`) VALUES
(1, 'celestiaevents312025@gmail.com', 'ytggrowxduakkgzv');

-- --------------------------------------------------------

--
-- Table structure for table `eventquestions`
--

CREATE TABLE `eventquestions` (
  `questionID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL,
  `questionText` varchar(255) NOT NULL,
  `helpText` varchar(255) DEFAULT NULL,
  `isRequired` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventquestions`
--

INSERT INTO `eventquestions` (`questionID`, `eventID`, `questionText`, `helpText`, `isRequired`) VALUES
(1, 1, 'How satisfied were you with the event?', 'Rate from 1-5', 1),
(2, 1, 'What did you enjoy most about the event?', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(13,8) NOT NULL,
  `longitude` decimal(13,8) NOT NULL,
  `startDateTime` date NOT NULL,
  `endDateTime` date NOT NULL,
  `posterURL` varchar(500) DEFAULT NULL,
  `bannerImage` varchar(255) NOT NULL,
  `seatPlanImage` varchar(255) NOT NULL,
  `organizerInfoID` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `ticketingOutletURL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `title`, `description`, `location`, `latitude`, `longitude`, `startDateTime`, `endDateTime`, `posterURL`, `bannerImage`, `seatPlanImage`, `organizerInfoID`, `createdAt`, `ticketingOutletURL`) VALUES
(1, 'Blackpink Deadline World Tour', 'The BLACKPINK Deadline World Tour 2025 brings JISOO, JENNIE, ROSÉ, and LISA back to the Philippines for a powerful night of music at the Philippine Arena. Fans can expect their biggest hits, stunning visuals, and high-energy performances that celebrate BLACKPINK’s global impact in K-pop.', 'Philippine Arena, Bulacan', 14.79380000, 120.95360000, '2025-11-22', '2025-11-23', 'assets/img/posters/poster1.jpg', 'assets/img/banners/banner-1.jpg', 'assets/img/seatplans/seat-plan-1.jpg', 1, '2024-05-01 06:30:00', 'https://smtickets.com/events/view/14905'),
(2, 'Jason Derulo in Manila', 'Jason Derulo is set to bring his high-energy performance to the SM Mall of Asia Arena on November 22, 2025, at 8:00 PM. Fans can expect a night of chart-topping hits like \"Savage Love,\" \"Wiggle,\" and new tracks from his latest album Nu King. Known for his dynamic stage presence, powerful vocals, and explosive dance routines, Derulo’s Manila comeback promises an unforgettable concert experience. Tickets will be available via SM Tickets starting May 30, 2025.', 'SM Mall of Asia Arena', 14.53160000, 120.98400000, '2025-11-22', '2025-11-23', 'assets/img/posters/poster2.jpg', 'assets/img/banners/banner-2.jpg', 'assets/img/seatplans/seat-plan-2.jpg', 2, '2025-06-17 14:01:43', 'https://smtickets.com/events/view/14834'),
(3, 'Wanderland Music & Arts Festival', 'A weekend of indie music and art installations featuring both local and international artists.', 'Muntinlupa', 14.53160000, 120.98400000, '2025-03-08', '2025-03-09', 'assets/img/posters/poster3.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 1, '2024-12-20 02:15:00', 'https://smtickets.com/events/view/14979'),
(4, 'SB19 PAGTATAG! Finale Concert', 'The highly-anticipated finale concert of SB19\'s PAGTATAG! tour in their home country.', 'Quezon City', 14.53160000, 120.98400000, '2025-05-18', '2025-05-19', 'assets/img/posters/poster4.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 2, '2025-01-05 08:45:00', 'https://smtickets.com/events/view/14979'),
(5, 'Ben&Ben Homecoming Concert', 'Ben&Ben returns with a special homecoming concert for their fans.', 'Pasay', 14.53160000, 120.98400000, '2024-11-15', '2024-11-16', 'assets/img/posters/poster5.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 4, '2024-08-12 01:20:00', 'https://smtickets.com/events/view/14979'),
(6, 'Taylor Swift: The Eras Tour', 'Taylor Swift brings her historic Eras Tour to the Philippine stage.', 'Taguig', 14.53160000, 120.98400000, '2024-08-25', '2024-08-26', 'assets/img/posters/poster6.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 5, '2024-05-10 04:05:00', 'https://smtickets.com/events/view/14979'),
(7, 'Fête de la Musique Philippines', 'Annual celebration of music with simultaneous performances across different venues in the city.', 'Makati', 14.53160000, 120.98400000, '2024-06-21', '2024-06-22', 'assets/img/posters/poster7.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 2, '2024-04-22 10:00:00', 'https://smtickets.com/events/view/14979'),
(8, 'BTS Festa Viewing Party', 'A fan-led celebration of BTS anniversary with live performances on screen and fan activities.', 'Cebu City', 14.53160000, 120.98400000, '2024-06-13', '2024-06-14', 'assets/img/posters/poster8.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 1, '2024-03-19 03:45:00', 'https://smtickets.com/events/view/14979'),
(9, 'Coke Studio Homecoming', 'An exciting music event featuring collaborations between various Filipino artists.', 'Manila', 14.53160000, 120.98400000, '2024-10-10', '2024-10-11', 'assets/img/posters/poster9.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 3, '2024-07-14 05:10:00', 'https://smtickets.com/events/view/14979'),
(10, 'RakraKAN Festival', 'The biggest gathering of Filipino rock bands for two nights of nonstop music.', 'San Juan', 14.53160000, 120.98400000, '2025-01-25', '2025-01-26', 'assets/img/posters/poster10.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 4, '2024-10-30 07:35:00', 'https://smtickets.com/events/view/14979'),
(11, 'Sinulog Grand Festival Concert', 'The culminating concert of the Sinulog Festival featuring top local acts.\n\n', 'Cebu City', 14.53160000, 120.98400000, '2025-01-19', '2025-01-20', 'assets/img/posters/poster11.jpg', 'assets/img/banners/banner-3.jpg', 'assets/img/seatplans/seat-plan-3.jpg', 5, '2024-11-02 09:50:00', 'https://smtickets.com/events/view/14979');

-- --------------------------------------------------------

--
-- Table structure for table `guestanswers`
--

CREATE TABLE `guestanswers` (
  `guestID` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `answer1` text DEFAULT NULL,
  `answer2` text DEFAULT NULL,
  `sendReminder` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `guestID` int(4) NOT NULL,
  `eventID` int(11) NOT NULL,
  `guestFirstName` varchar(50) NOT NULL,
  `guestLastName` varchar(50) NOT NULL,
  `guestContactNumber` varchar(15) NOT NULL,
  `guestEmail` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`guestID`, `eventID`, `guestFirstName`, `guestLastName`, `guestContactNumber`, `guestEmail`) VALUES
(1, 1, 'Keroppi', 'Sanrio', '09123456789', 'keroppisanrio8@gmail.com'),
(2, 3, 'Liam', 'Garcia', '09181234567', 'liamgarcia@gmail.com'),
(3, 5, 'Maya', 'Reyes', '09987654321', 'mayareyes99@gmail.com'),
(4, 2, 'Ethan', 'Lopez', '09271231234', 'ethanlopez12@gmail.com'),
(5, 8, 'Sofia', 'Castro', '09391239876', 'sofiacastro23@gmail.com'),
(6, 6, 'Jacob', 'Torres', '09481231235', 'jacobtorres88@gmail.com'),
(7, 9, 'Isabella', 'Villanueva', '09171239876', 'isabellavilla@gmail.com'),
(8, 4, 'Noah', 'Cruz', '09991234567', 'noahcruz77@gmail.com'),
(9, 10, 'Amelia', 'Santos', '09291237654', 'ameliasantos22@gmail.com'),
(10, 7, 'Lucas', 'Fernandez', '09191234567', 'lucasfernandez03@gmail.com'),
(11, 1, 'Mia', 'Navarro', '09391231234', 'mianavarro11@gmail.com'),
(12, 5, 'Caleb', 'Mendoza', '09185671234', 'calebmendoza45@gmail.com'),
(13, 3, 'Elena', 'Gutierrez', '09491238976', 'elenagutierrez09@gmail.com'),
(14, 9, 'Nathan', 'Soriano', '09371234512', 'nathansoriano14@gmail.com'),
(15, 4, 'Hannah', 'Ramirez', '09175678923', 'hannahramirez33@gmail.com'),
(16, 2, 'Oliver', 'Delos Reyes', '09081236543', 'oliverdreyes56@gmail.com'),
(17, 8, 'Chloe', 'Pineda', '09992345678', 'chloepineda74@gmail.com'),
(18, 6, 'Daniel', 'Velasquez', '09273456712', 'danielvelasquez21@gmail.com'),
(19, 10, 'Sophia', 'Agustin', '09481238976', 'sophiaagustin17@gmail.com'),
(20, 7, 'Gabriel', 'Domingo', '09381231235', 'gabrieldomingo13@gmail.com'),
(21, 1, 'joe', 'joe', '00000000000', 'josephmaliza55@gmail.com'),
(22, 1, 'joe', 'joe', '00000000000', 'josephmaliza55@gmail.com'),
(23, 1, 'joe', 'joe', '00000000000', 'josephmaliza55@gmail.com'),
(24, 1, 'joe', 'joe', '00000000000', 'josephmaliza55@gmail.com'),
(25, 1, 'joe', 'joe', '00000000000', 'josephmaliza55@gmail.com'),
(26, 1, 'jane', 'doe', '00000000000', 'josephmaliza55@gmail.com'),
(27, 1, 'jake', 'sim', '00000000000', 'josephmaliza55@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `organizerinfo`
--

CREATE TABLE `organizerinfo` (
  `organizerInfoID` int(4) NOT NULL,
  `organizerID` int(4) NOT NULL,
  `organizerFirstName` varchar(30) NOT NULL,
  `organizerLastName` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizerinfo`
--

INSERT INTO `organizerinfo` (`organizerInfoID`, `organizerID`, `organizerFirstName`, `organizerLastName`) VALUES
(1, 1, 'John', 'Doe'),
(2, 2, 'Jane', 'Air'),
(3, 3, 'Andrea', 'Villanueva'),
(4, 4, 'Miguel', 'Reyes'),
(5, 5, 'Samantha', 'Cruz'),
(6, 6, 'Uno', 'Bronze');

-- --------------------------------------------------------

--
-- Table structure for table `organizers`
--

CREATE TABLE `organizers` (
  `organizerID` int(4) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `isActive` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizers`
--

INSERT INTO `organizers` (`organizerID`, `username`, `password`, `email`, `dateCreated`, `isActive`) VALUES
(1, 'andreavilla', 'pass1234', 'andrea.villa@gmail.com', '2024-06-01 02:25:00', '1'),
(2, 'miguelreyes', 'm1gu3l2024', 'miguel.reyes@gmail.com', '2024-05-15 06:50:00', '0'),
(3, 'user1', 'user00', 'user@gmail.com', '2025-06-17 19:13:57', 'TRUE'),
(4, 'samanthacruz', 'samC!2025', 'samantha.cruz@gmail.com', '2024-06-10 01:00:00', '1'),
(5, 'carlogomez', 'g0m3zpass', 'carlo.gomez@gmail.com', '2024-06-18 11:30:00', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`credentialId`);

--
-- Indexes for table `eventquestions`
--
ALTER TABLE `eventquestions`
  ADD PRIMARY KEY (`questionID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `OrganizerID` (`organizerInfoID`);

--
-- Indexes for table `guestanswers`
--
ALTER TABLE `guestanswers`
  ADD PRIMARY KEY (`guestID`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`guestID`);

--
-- Indexes for table `organizerinfo`
--
ALTER TABLE `organizerinfo`
  ADD PRIMARY KEY (`organizerInfoID`);

--
-- Indexes for table `organizers`
--
ALTER TABLE `organizers`
  ADD PRIMARY KEY (`organizerID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `credentialId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eventquestions`
--
ALTER TABLE `eventquestions`
  MODIFY `questionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `guestanswers`
--
ALTER TABLE `guestanswers`
  MODIFY `guestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `guestID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `organizerinfo`
--
ALTER TABLE `organizerinfo`
  MODIFY `organizerInfoID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `organizers`
--
ALTER TABLE `organizers`
  MODIFY `organizerID` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
