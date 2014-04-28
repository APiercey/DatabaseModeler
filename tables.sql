-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 28, 2014 at 11:03 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `modeler`
--

-- --------------------------------------------------------

--
-- Table structure for table `bike`
--

CREATE TABLE IF NOT EXISTS `bike` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `colour` varchar(30) NOT NULL,
  `num_of_wheels` int(11) NOT NULL,
  `num_of_seats` int(11) NOT NULL,
  `num_of_gears` int(11) NOT NULL,
  `factory_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `bike`
--

INSERT INTO `bike` (`id`, `colour`, `num_of_wheels`, `num_of_seats`, `num_of_gears`, `factory_id`) VALUES
(100, 'Blue', 5, 3, 13, 27);

-- --------------------------------------------------------

--
-- Table structure for table `factory`
--

CREATE TABLE IF NOT EXISTS `factory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `factory`
--

INSERT INTO `factory` (`id`, `name`) VALUES
(1, 'Isreal'),
(2, 'Canada'),
(3, 'Mexico'),
(27, 'Bikes Ltd.');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
