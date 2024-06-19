-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Jun 19, 2024 at 06:19 AM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ims_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `color`) VALUES
(14, 'AC', 'PURPLE'),
(15, 'N', 'BLUE'),
(17, 'SL', 'BLUE');

-- --------------------------------------------------------

--
-- Table structure for table `issue`
--

CREATE TABLE `issue` (
  `id` int(11) NOT NULL,
  `bus_number` varchar(50) DEFAULT NULL,
  `permit_number` varchar(50) DEFAULT NULL,
  `issue_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `issue`
--

INSERT INTO `issue` (`id`, `bus_number`, `permit_number`, `issue_date`) VALUES
(6, NULL, NULL, '2024-06-16 04:18:57'),
(7, NULL, NULL, '2024-06-16 04:58:14'),
(8, '1111', '1112', '2024-06-16 05:10:43'),
(9, 'ND-5656', '12375764', '2024-06-16 08:11:24'),
(10, 'ND-4545', '2355246', '2024-06-19 03:32:07');

-- --------------------------------------------------------

--
-- Table structure for table `issue_product`
--

CREATE TABLE `issue_product` (
  `issue_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `issue_product`
--

INSERT INTO `issue_product` (`issue_id`, `product_id`, `quantity`) VALUES
(9, 12, 11),
(10, 10, 2),
(10, 11, 3);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `name` varchar(300) NOT NULL,
  `category_id` int(11) NOT NULL,
  `min_units` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `serial_number`, `name`, `category_id`, `min_units`) VALUES
(9, '1', 'COLOMBO-AMPARA', 14, 50),
(10, '1', 'COLOMBO-KANDY', 15, 28),
(11, '4', 'COLOMBO-MATARA', 14, 40),
(12, '2', 'COLOMBO-JAFFNA', 17, 20),
(13, '34', 'BADULLA-TRINCOMALEE', 15, 20);

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `purchase_price` decimal(10,2) NOT NULL,
  `supplier_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`id`, `product_id`, `quantity`, `purchase_date`, `purchase_price`, `supplier_name`) VALUES
(4, 9, 21, '2024-06-14 18:30:00', '12000.00', 'GK printers'),
(6, 11, 56, '2024-06-14 18:30:00', '56000.00', 'Marv'),
(7, 10, 90, '2024-06-15 18:30:00', '20000.00', 'Anemanda'),
(8, 12, 30, '2024-06-15 18:30:00', '12000.00', 'ashagan export'),
(9, 10, 45, '2024-05-14 18:30:00', '4000.00', 'sup'),
(10, 13, 20, '2024-06-18 18:30:00', '3000.00', 'sup');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issue`
--
ALTER TABLE `issue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issue_product`
--
ALTER TABLE `issue_product`
  ADD PRIMARY KEY (`issue_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `issue`
--
ALTER TABLE `issue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issue_product`
--
ALTER TABLE `issue_product`
  ADD CONSTRAINT `issue_product_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issue` (`id`),
  ADD CONSTRAINT `issue_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
