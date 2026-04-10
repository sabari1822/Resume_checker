-- phpMyAdmin SQL Dump
-- Database: `resume_analyzer`

CREATE DATABASE IF NOT EXISTS `resume_analyzer` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `resume_analyzer`;

-- --------------------------------------------------------

--
-- Table structure for table `resumes`
--

CREATE TABLE `resumes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `job_role` varchar(255) NOT NULL,
  `extracted_text` longtext DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `missing_keywords` text DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
