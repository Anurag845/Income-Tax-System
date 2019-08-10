-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2019 at 03:35 PM
-- Server version: 5.7.20-0ubuntu0.17.04.1
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Income_Tax`
--

-- --------------------------------------------------------

--
-- Table structure for table `Declarations`
--

CREATE TABLE `Declarations` (
  `emp_id` varchar(11) NOT NULL,
  `dec_type` varchar(35) NOT NULL,
  `amount_declared` int(11) NOT NULL,
  `amount_proved` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `dec_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `Declarations`
--

INSERT INTO `Declarations` (`emp_id`, `dec_type`, `amount_declared`, `amount_proved`, `status`, `dec_id`) VALUES
('EMP002', 'Home Interest', 36000, 0, 'Pending', 'home'),
('EMP002', 'CPF', 21600, 0, 'Pending', 'sub_cpf');

-- --------------------------------------------------------

--
-- Table structure for table `Dec_sub_fields`
--

CREATE TABLE `Dec_sub_fields` (
  `field_id` varchar(50) NOT NULL,
  `sub_field` varchar(50) NOT NULL,
  `sub_id` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Dec_sub_fields`
--

INSERT INTO `Dec_sub_fields` (`field_id`, `sub_field`, `sub_id`) VALUES
('inve', 'CPF', 'sub_cpf'),
('inve', 'PPF', 'sub_ppf'),
('inve', 'NSC', 'sub_nsc'),
('inve', 'ULIP/Mutual Fund', 'sub_ulip'),
('inve', 'Annual Insurance', 'sub_annu'),
('inve', 'Housing Loan Principal', 'sub_hous'),
('inve', 'Tuition Fee', 'sub_tuit'),
('inve', 'Bank Deposit', 'sub_bank'),
('inve', 'Registration Fee', 'sub_regi');

-- --------------------------------------------------------

--
-- Table structure for table `Employee`
--

CREATE TABLE `Employee` (
  `emp_name` varchar(35) DEFAULT NULL,
  `emp_id` varchar(6) NOT NULL,
  `gross_sal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `Employee`
--

INSERT INTO `Employee` (`emp_name`, `emp_id`, `gross_sal`) VALUES
('Name3', 'EMP001', 600000),
('Name1', 'EMP002', 1000000),
('Name5', 'EMP003', 1200000),
('Name4', 'EMP004', 1400000);

-- --------------------------------------------------------

--
-- Table structure for table `Limits`
--

CREATE TABLE `Limits` (
  `entry` varchar(35) NOT NULL,
  `tax_limit` int(11) NOT NULL,
  `sub_field` varchar(5) NOT NULL,
  `dec_id` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `Limits`
--

INSERT INTO `Limits` (`entry`, `tax_limit`, `sub_field`, `dec_id`) VALUES
('Annual Rent', 150000, 'no', 'annu'),
('Home Interest', 200000, 'no', 'home'),
('National Pension', 50000, 'no', 'nati'),
('Physically Handicap', 40000, 'no', 'phys'),
('Investments', 150000, 'yes', 'inve'),
('Education Interest', 50000, 'no', 'educ'),
('Mediclaim', 30000, 'no', 'medi');

-- --------------------------------------------------------

--
-- Table structure for table `Standard_Deduc`
--

CREATE TABLE `Standard_Deduc` (
  `field` varchar(50) NOT NULL,
  `value` int(11) NOT NULL,
  `ded_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Standard_Deduc`
--

INSERT INTO `Standard_Deduc` (`field`, `value`, `ded_id`) VALUES
('Standard Deduction', 50000, 'stan'),
('Profession Tax', 2500, 'prof');

-- --------------------------------------------------------

--
-- Table structure for table `Taxable_monthly`
--

CREATE TABLE `Taxable_monthly` (
  `emp_id` varchar(11) NOT NULL,
  `April` int(11) NOT NULL,
  `May` int(11) NOT NULL,
  `June` int(11) NOT NULL,
  `July` int(11) NOT NULL,
  `August` int(11) NOT NULL,
  `September` int(11) NOT NULL,
  `October` int(11) NOT NULL,
  `November` int(11) NOT NULL,
  `December` int(11) NOT NULL,
  `January` int(11) NOT NULL,
  `February` int(11) NOT NULL,
  `March` int(11) NOT NULL,
  `Annual` int(11) NOT NULL,
  `Adjusted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `Taxable_monthly`
--

INSERT INTO `Taxable_monthly` (`emp_id`, `April`, `May`, `June`, `July`, `August`, `September`, `October`, `November`, `December`, `January`, `February`, `March`, `Annual`, `Adjusted`) VALUES
('EMP002', 0, 0, 88990, 88990, 88990, 88990, 88990, 88990, 88990, 88990, 88990, 88990, 889900, 889900);

-- --------------------------------------------------------

--
-- Table structure for table `Tax_monthly`
--

CREATE TABLE `Tax_monthly` (
  `emp_id` varchar(11) NOT NULL,
  `April` int(11) NOT NULL,
  `May` int(11) NOT NULL,
  `June` int(11) NOT NULL,
  `July` int(11) NOT NULL,
  `August` int(11) NOT NULL,
  `September` int(11) NOT NULL,
  `October` int(11) NOT NULL,
  `November` int(11) NOT NULL,
  `December` int(11) NOT NULL,
  `January` int(11) NOT NULL,
  `February` int(11) NOT NULL,
  `March` int(11) NOT NULL,
  `Annual` int(11) NOT NULL,
  `Adjusted` int(11) NOT NULL,
  `Edu_Cess` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `Tax_monthly`
--

INSERT INTO `Tax_monthly` (`emp_id`, `April`, `May`, `June`, `July`, `August`, `September`, `October`, `November`, `December`, `January`, `February`, `March`, `Annual`, `Adjusted`, `Edu_Cess`) VALUES
('EMP002', 0, 0, 9048, 9048, 9048, 9048, 9048, 9048, 9048, 9048, 9048, 9048, 90480, 90480, 3620);

-- --------------------------------------------------------

--
-- Table structure for table `Tax_slabs`
--

CREATE TABLE `Tax_slabs` (
  `lower_boundary` int(11) NOT NULL,
  `upper_boundary` int(11) NOT NULL,
  `percent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `Tax_slabs`
--

INSERT INTO `Tax_slabs` (`lower_boundary`, `upper_boundary`, `percent`) VALUES
(0, 250000, 0),
(250000, 500000, 5),
(500000, 1000000, 20),
(1000000, 5000000, 30),
(5000000, 10000000, 30);

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`username`, `password`) VALUES
('admin', 'admin_123'),
('user', 'user_123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Declarations`
--
ALTER TABLE `Declarations`
  ADD PRIMARY KEY (`emp_id`,`dec_id`);

--
-- Indexes for table `Employee`
--
ALTER TABLE `Employee`
  ADD PRIMARY KEY (`emp_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
