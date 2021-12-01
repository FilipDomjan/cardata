-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2021 at 04:24 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `manufacturer` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `chassis` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `vinyl` varchar(100) DEFAULT NULL,
  `model_year` varchar(4) DEFAULT NULL,
  `engine_config` varchar(30) DEFAULT NULL,
  `engine_capacity` int(11) DEFAULT NULL,
  `horsepower` int(11) DEFAULT NULL,
  `fuel_type` varchar(30) DEFAULT NULL,
  `transmission` varchar(30) DEFAULT NULL,
  `drivetrain` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `manufacturer`, `model`, `chassis`, `color`, `vinyl`, `model_year`, `engine_config`, `engine_capacity`, `horsepower`, `fuel_type`, `transmission`, `drivetrain`) VALUES
(83319, 'Alfa Romeo', 'Giulia Quadrifoglio', 'Sedan', 'Red Metallic', '', '2018', 'V6', 3200, 410, 'Petrol', 'semi-auto', 'awd'),
(415547, 'Audi', 'A6', 'Sedan', 'Santorino Blue Pearl', '', '1999', 'V6', 2800, 192, 'Petrol + LPG', 'semi-auto', 'awd'),
(484342, 'Audi', 'RS5', 'Coupe', 'Nardo Gray', '', '2013', 'V8', 4200, 450, 'Petrol', 'semi-auto', 'awd');

-- --------------------------------------------------------

--
-- Table structure for table `mods`
--

CREATE TABLE `mods` (
  `id` int(11) NOT NULL,
  `carid` int(11) NOT NULL,
  `repair_type` varchar(10) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `milage` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `unit` varchar(5) DEFAULT NULL,
  `done_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mods`
--

INSERT INTO `mods` (`id`, `carid`, `repair_type`, `description`, `milage`, `price`, `unit`, `done_at`) VALUES
(1, 484342, 'Mod', 'New 20\" ADV10 M2.CS Two-piece wheels', 150000, 2500, 'EUR', '2021-11-28 23:34:38'),
(2, 415547, 'Repair', 'All-out service, pulleys, timing belt, front belt, water pump and some piping', 275000, 7000, 'HRK', '2021-11-28 23:36:51'),
(3, 484342, 'Other', 'Oil change', 151000, 1200, 'HRK', '2021-11-28 23:37:09'),
(4, 415547, 'Mod', 'New exhaust, removed center resonator and rear mufflers, added new pass-through rear mufflers to eliminate resonance,  also added two oval tips.', 278000, 3000, 'HRK', '2021-11-28 23:38:57'),
(5, 415547, 'Tune', 'New sport intake, making the car Stage 1 Tuned at 230HP', 280000, 500, 'EUR', '2021-11-28 23:40:22'),
(6, 83319, 'Mod', 'Custom Quad tip exhaust', 14000, 2430, 'EUR', '2021-12-01 01:58:05'),
(7, 415547, 'Repair', 'Timing belt replacement', 291000, 500, 'EUR', '2021-12-01 01:58:28'),
(8, 484342, 'Tune', 'Stage 3 AWE Tune', 60000, 2600, 'EUR', '2021-12-01 01:58:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mods`
--
ALTER TABLE `mods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carid` (`carid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=484343;

--
-- AUTO_INCREMENT for table `mods`
--
ALTER TABLE `mods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mods`
--
ALTER TABLE `mods`
  ADD CONSTRAINT `mods_ibfk_1` FOREIGN KEY (`carid`) REFERENCES `cars` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
