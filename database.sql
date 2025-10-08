-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 01, 2025 at 07:34 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cell`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `vacancy` int(11) DEFAULT NULL,
  `applicant` int(11) DEFAULT NULL,
  `stat` text DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cells`
--

CREATE TABLE `cells` (
  `id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stat` text DEFAULT 'NO ACTION'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text DEFAULT NULL,
  `pass` text DEFAULT NULL,
  `role` text DEFAULT 'USER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pass`, `role`) VALUES
(1, 'u', '123', 'CANDIDATE'),
(2, 'a', '123', 'ADMIN'),
(3, 'c', '123', 'CANDIDATE'),
(4, 'b', '123', 'USER'),
(5, 'rahul_sharma', 'pass123', 'USER'),
(6, 'priya_kapoor', 'pass123', 'USER'),
(7, 'amit_verma', 'pass123', 'USER'),
(8, 'neha_singh', 'pass123', 'USER'),
(9, 'arjun_mehta', 'pass123', 'USER'),
(10, 'kavya_rathi', 'pass123', 'USER'),
(11, 'rohan_patel', 'pass123', 'USER'),
(12, 'ananya_iyer', 'pass123', 'USER'),
(13, 'vikas_joshi', 'pass123', 'USER'),
(14, 'sneha_nair', 'pass123', 'USER'),
(15, 'deepak_malhotra', 'pass123', 'USER'),
(16, 'isha_agrawal', 'pass123', 'USER'),
(17, 'sanjay_kumar', 'pass123', 'USER'),
(18, 'meera_chopra', 'pass123', 'USER'),
(19, 'manish_gupta', 'pass123', 'USER'),
(20, 'rhea_banerjee', 'pass123', 'USER'),
(21, 'tarun_saxena', 'pass123', 'USER'),
(22, 'simran_kohli', 'pass123', 'USER'),
(23, 'gopal_reddy', 'pass123', 'USER'),
(24, 'aisha_pandey', 'pass123', 'USER'),
(25, 'vivek_mishra', 'pass123', 'USER'),
(26, 'nisha_rajput', 'pass123', 'USER'),
(27, 'karthik_iyer', 'pass123', 'USER'),
(28, 'pallavi_menon', 'pass123', 'USER'),
(29, 'abhishek_tandon', 'pass123', 'USER'),
(30, 'tanya_jain', 'pass123', 'USER'),
(31, 'john_smith', 'pass123', 'USER'),
(32, 'emily_jones', 'pass123', 'USER'),
(33, 'michael_brown', 'pass123', 'USER'),
(34, 'sarah_davis', 'pass123', 'USER'),
(35, 'david_miller', 'pass123', 'USER'),
(36, 'jessica_wilson', 'pass123', 'USER'),
(37, 'james_moore', 'pass123', 'USER'),
(38, 'ashley_taylor', 'pass123', 'USER'),
(39, 'william_anderson', 'pass123', 'USER'),
(40, 'olivia_thomas', 'pass123', 'USER'),
(41, 'daniel_jackson', 'pass123', 'USER'),
(42, 'madison_white', 'pass123', 'USER'),
(43, 'robert_harris', 'pass123', 'USER'),
(44, 'sophia_martin', 'pass123', 'USER'),
(45, 'charles_thompson', 'pass123', 'USER'),
(46, 'ava_garcia', 'pass123', 'USER'),
(47, 'henry_clark', 'pass123', 'USER'),
(48, 'mia_lewis', 'pass123', 'USER'),
(49, 'andrew_lee', 'pass123', 'USER'),
(50, 'chloe_walker', 'pass123', 'USER'),
(51, 'jack_hall', 'pass123', 'USER'),
(52, 'ella_allen', 'pass123', 'USER'),
(53, 'benjamin_young', 'pass123', 'USER'),
(54, 'grace_hernandez', 'pass123', 'USER'),
(55, 'alexander_king', 'pass123', 'USER'),
(56, 'arvind.kumar@gmail.com', 'pass123', 'USER'),
(57, 'shruti.mehra@yahoo.com', 'pass123', 'USER'),
(58, 'mohit.singhal@gmail.com', 'pass123', 'USER'),
(59, 'puneet.kapoor@outlook.com', 'pass123', 'USER'),
(60, 'anita.nair@gmail.com', 'pass123', 'USER'),
(61, 'suresh.menon@yahoo.com', 'pass123', 'USER'),
(62, 'sonali.jain@gmail.com', 'pass123', 'USER'),
(63, 'varun.shah@outlook.com', 'pass123', 'USER'),
(64, 'poonam.mittal@gmail.com', 'pass123', 'USER'),
(65, 'gaurav.agarwal@yahoo.com', 'pass123', 'USER'),
(66, 'rekha.iyer@gmail.com', 'pass123', 'USER'),
(67, 'ajay.rathi@gmail.com', 'pass123', 'USER'),
(68, 'divya.verma@yahoo.com', 'pass123', 'USER'),
(69, 'lokesh.patel@outlook.com', 'pass123', 'USER'),
(70, 'megha.saxena@gmail.com', 'pass123', 'USER'),
(71, 'yash.kohli@gmail.com', 'pass123', 'USER'),
(72, 'kanika.chopra@yahoo.com', 'pass123', 'USER'),
(73, 'siddharth.gupta@gmail.com', 'pass123', 'USER'),
(74, 'nikita.banerjee@outlook.com', 'pass123', 'USER'),
(75, 'rahul.reddy@gmail.com', 'pass123', 'USER'),
(76, 'pragya.agrawal@yahoo.com', 'pass123', 'USER'),
(77, 'chetan.sharma@gmail.com', 'pass123', 'USER'),
(78, 'monika.naidu@outlook.com', 'pass123', 'USER'),
(79, 'alok.mishra@gmail.com', 'pass123', 'USER'),
(80, 'swati.kulkarni@yahoo.com', 'pass123', 'USER'),
(81, 'parag.tandon@gmail.com', 'pass123', 'USER'),
(82, 'ritu.bhatt@outlook.com', 'pass123', 'USER'),
(83, 'aditya.singh@gmail.com', 'pass123', 'USER'),
(84, 'sonam.rajput@yahoo.com', 'pass123', 'USER'),
(85, 'harshit.kumar@gmail.com', 'pass123', 'USER'),
(86, 'aarti.rana@outlook.com', 'pass123', 'USER'),
(87, 'jatin.malhotra@gmail.com', 'pass123', 'USER'),
(88, 'shreya.bose@yahoo.com', 'pass123', 'USER'),
(89, 'manoj.khatri@gmail.com', 'pass123', 'USER'),
(90, 'kiran.pandey@outlook.com', 'pass123', 'USER'),
(91, 'sumit.desai@gmail.com', 'pass123', 'USER'),
(92, 'pallavi.mukherjee@yahoo.com', 'pass123', 'USER'),
(93, 'arjun.rao@gmail.com', 'pass123', 'USER'),
(94, 'isha.shukla@outlook.com', 'pass123', 'USER'),
(95, 'devendra.mani@gmail.com', 'pass123', 'USER'),
(96, 'vandana.seth@yahoo.com', 'pass123', 'USER'),
(97, 'sahil.agarwal@gmail.com', 'pass123', 'USER'),
(98, 'jyoti.tripathi@outlook.com', 'pass123', 'USER'),
(99, 'rohit.bajaj@gmail.com', 'pass123', 'USER'),
(100, 'neeraj.chaudhary@yahoo.com', 'pass123', 'USER'),
(101, 'mansi.khatri@gmail.com', 'pass123', 'USER'),
(102, 'lalit.iyengar@outlook.com', 'pass123', 'USER'),
(103, 'geeta.kapoor@gmail.com', 'pass123', 'USER'),
(104, 'vivek.rastogi@yahoo.com', 'pass123', 'USER'),
(105, 'ankita.bhalla@gmail.com', 'pass123', 'USER'),
(106, 'ethan.johnson@gmail.com', 'pass123', 'USER'),
(107, 'harper.williams@yahoo.com', 'pass123', 'USER'),
(108, 'liam.brown@outlook.com', 'pass123', 'USER'),
(109, 'isabella.jones@gmail.com', 'pass123', 'USER'),
(110, 'mason.miller@yahoo.com', 'pass123', 'USER'),
(111, 'sophia.davis@gmail.com', 'pass123', 'USER'),
(112, 'logan.moore@outlook.com', 'pass123', 'USER'),
(113, 'charlotte.taylor@gmail.com', 'pass123', 'USER'),
(114, 'lucas.anderson@yahoo.com', 'pass123', 'USER'),
(115, 'amelia.thomas@gmail.com', 'pass123', 'USER'),
(116, 'jackson.jackson@outlook.com', 'pass123', 'USER'),
(117, 'mia.white@gmail.com', 'pass123', 'USER'),
(118, 'carter.harris@yahoo.com', 'pass123', 'USER'),
(119, 'ella.martin@gmail.com', 'pass123', 'USER'),
(120, 'grayson.thompson@outlook.com', 'pass123', 'USER'),
(121, 'aria.garcia@gmail.com', 'pass123', 'USER'),
(122, 'levi.clark@yahoo.com', 'pass123', 'USER'),
(123, 'zoey.lewis@gmail.com', 'pass123', 'USER'),
(124, 'sebastian.lee@outlook.com', 'pass123', 'USER'),
(125, 'nora.walker@gmail.com', 'pass123', 'USER'),
(126, 'oliver.hall@yahoo.com', 'pass123', 'USER'),
(127, 'lily.allen@gmail.com', 'pass123', 'USER'),
(128, 'elijah.young@outlook.com', 'pass123', 'USER'),
(129, 'scarlett.hernandez@gmail.com', 'pass123', 'USER'),
(130, 'aiden.king@yahoo.com', 'pass123', 'USER'),
(131, 'zoe.scott@gmail.com', 'pass123', 'USER'),
(132, 'matthew.green@outlook.com', 'pass123', 'USER'),
(133, 'hannah.adams@gmail.com', 'pass123', 'USER'),
(134, 'samuel.baker@yahoo.com', 'pass123', 'USER'),
(135, 'victoria.gonzalez@gmail.com', 'pass123', 'USER'),
(136, 'jack.parker@outlook.com', 'pass123', 'USER'),
(137, 'ella.evans@gmail.com', 'pass123', 'USER'),
(138, 'joseph.turner@yahoo.com', 'pass123', 'USER'),
(139, 'layla.campbell@gmail.com', 'pass123', 'USER'),
(140, 'christopher.mitchell@outlook.com', 'pass123', 'USER'),
(141, 'samantha.roberts@gmail.com', 'pass123', 'USER'),
(142, 'owen.carter@yahoo.com', 'pass123', 'USER'),
(143, 'penelope.phillips@gmail.com', 'pass123', 'USER'),
(144, 'gabriel.ward@outlook.com', 'pass123', 'USER'),
(145, 'brooklyn.morgan@gmail.com', 'pass123', 'USER'),
(146, 'dylan.rivera@yahoo.com', 'pass123', 'USER'),
(147, 'stella.cooper@gmail.com', 'pass123', 'USER'),
(148, 'isaac.richardson@outlook.com', 'pass123', 'USER'),
(149, 'natalie.wood@gmail.com', 'pass123', 'USER'),
(150, 'ryan.bailey@yahoo.com', 'pass123', 'USER'),
(151, 'madelyn.flores@gmail.com', 'pass123', 'USER'),
(152, 'julian.morris@outlook.com', 'pass123', 'USER'),
(153, 'hazel.rogers@gmail.com', 'pass123', 'USER'),
(154, 'nathan.reed@yahoo.com', 'pass123', 'USER'),
(155, 'savannah.cook@gmail.com', 'pass123', 'USER');

-- --------------------------------------------------------

--
-- Table structure for table `vacancies`
--

CREATE TABLE `vacancies` (
  `cells` int(11) DEFAULT NULL,
  `role` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `openings` int(11) DEFAULT 1,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `voter` int(11) DEFAULT NULL,
  `applicant` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cells`
--
ALTER TABLE `cells`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vacancies`
--
ALTER TABLE `vacancies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cells`
--
ALTER TABLE `cells`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `vacancies`
--
ALTER TABLE `vacancies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
