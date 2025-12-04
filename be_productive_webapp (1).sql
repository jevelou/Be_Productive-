-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 12:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `be_productive_webapp`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetGoalDetails` (IN `goalId` INT, IN `userId` INT)   BEGIN
    DECLARE goalName VARCHAR(255);
    DECLARE goalDescription TEXT;
    DECLARE goalDeadline DATETIME;
    DECLARE goalProgress INT;
    DECLARE goalCategory VARCHAR(100);

    -- Fetch goal details
    SELECT g.goal_name, g.goal_description, g.goal_deadline, g.progress, c.cat_name 
    INTO goalName, goalDescription, goalDeadline, goalProgress, goalCategory
    FROM goal g 
    INNER JOIN category c ON g.cat_id = c.cat_id 
    WHERE g.goal_id = goalId AND g.user_id = userId;

    -- Return goal details
    SELECT goalName, goalDescription, goalDeadline, goalProgress, goalCategory;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTaskDetails` (IN `taskId` INT, IN `userId` INT)   BEGIN
    DECLARE taskName VARCHAR(255);
    DECLARE taskDescription TEXT;
    DECLARE taskPriority VARCHAR(10);
    DECLARE taskDeadline DATETIME;
    DECLARE taskStatus VARCHAR(50);
    DECLARE taskCategory VARCHAR(100);

    -- Fetch task details
    SELECT t.task_name, t.task_description, 
           CASE t.priority 
               WHEN 1 THEN 'High' 
               WHEN 2 THEN 'Medium' 
               WHEN 3 THEN 'Low' 
               ELSE 'Unknown' 
           END AS priority, 
           t.deadline, t.status, c.cat_name 
    INTO taskName, taskDescription, taskPriority, taskDeadline, taskStatus, taskCategory
    FROM task t 
    INNER JOIN category c ON t.cat_id = c.cat_id 
    WHERE t.task_id = taskId AND t.user_id = userId;

    -- Return task details
    SELECT taskName, taskDescription, taskPriority, taskDeadline, taskStatus, taskCategory;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserTasksAndGoals` (IN `userId` INT)   BEGIN
    -- Declare variables to hold user, tasks, and goals information
    DECLARE user_name VARCHAR(255);
    DECLARE task_name VARCHAR(255);
    DECLARE goal_name VARCHAR(255);
    
    -- Select user information based on user ID
    SELECT user_name INTO user_name FROM users WHERE user_id = userId;
    
    -- Select tasks based on user ID
    SELECT task_name INTO task_name FROM tasks WHERE user_id = userId;
    
    -- Select goals based on user ID
    SELECT goal_name INTO goal_name FROM goals WHERE user_id = userId;
    
    -- Return the user information, tasks, and goals
    SELECT user_name AS user, task_name AS task, goal_name AS goal;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cat_id`, `cat_name`) VALUES
(1, 'Study'),
(2, 'Work'),
(3, 'Self');

-- --------------------------------------------------------

--
-- Table structure for table `goal`
--

CREATE TABLE `goal` (
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goal_name` varchar(255) NOT NULL,
  `goal_description` text DEFAULT NULL,
  `goal_deadline` datetime DEFAULT NULL,
  `progress` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goal`
--

INSERT INTO `goal` (`goal_id`, `user_id`, `goal_name`, `goal_description`, `goal_deadline`, `progress`, `cat_id`) VALUES
(1, 1, 'Gain Height', 'Do exercise', '2024-05-22 17:39:00', 30, 3),
(2, 2, 'Gain Weight', 'skahdfhasjkdc', '2024-05-20 17:57:00', 40, 1),
(5, 1, 'Glow Up', 'Dapat mugwapa nko HAHAHA', '2025-01-01 07:00:00', 5, 3),
(7, 1, 'sdbjd', 'adjakhgjka', '2024-06-07 15:19:00', 100, 2),
(8, 7, 'Employeed in 2026', 'Dapat naa nay trabaho sa 2026', '2026-01-31 00:00:00', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_description` text DEFAULT NULL,
  `priority` enum('High','Medium','Low') NOT NULL,
  `deadline` datetime DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed') NOT NULL,
  `cat_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`task_id`, `user_id`, `task_name`, `task_description`, `priority`, `deadline`, `status`, `cat_id`) VALUES
(1, 1, 'CCC151 Web App', 'Web App Case Study', 'High', '2024-05-20 13:00:00', 'In Progress', 1),
(3, 2, 'School Activities', 'shjasgdjkas', 'High', '2024-05-22 17:56:00', 'In Progress', 2),
(6, 1, 'ITE112 Code', 'Code Defense', 'Medium', '2024-05-15 07:47:00', 'Completed', 1),
(8, 1, 'ITE Exam', 'Done na', 'High', '2024-05-18 13:00:00', 'Completed', 1),
(9, 6, 'CCC151', 'Web App Case Study', 'High', '2024-05-22 13:50:00', 'Completed', 1),
(10, 6, 'gjqiwhd', 'dajsdghjqw', 'High', '2024-05-30 14:27:00', 'In Progress', 2),
(11, 6, 'sdkqwehjdf', 'dhgwedghq', 'High', '2024-06-08 14:28:00', 'Pending', 3),
(12, 8, 'ITE185', 'Final Project', 'High', '2025-12-12 00:00:00', 'In Progress', 1),
(13, 9, 'Final_Proj in ITE185', 'Website Final Project', 'High', '2025-12-08 13:00:00', 'In Progress', 1),
(14, 9, 'ENTREP Pitching', 'Viagraph startup pitching with panelist.', 'High', '2025-12-16 09:00:00', 'In Progress', 1);

-- --------------------------------------------------------

--
-- Table structure for table `timelog`
--

CREATE TABLE `timelog` (
  `log_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timelog`
--

INSERT INTO `timelog` (`log_id`, `task_id`, `start_time`, `end_time`, `duration`) VALUES
(1, 1, '0000-00-00 00:00:00', '2024-05-14 03:51:58', 13),
(2, 6, '0000-00-00 00:00:00', '2024-05-14 03:53:41', 15),
(10, 9, '0000-00-00 00:00:00', '2024-05-22 05:51:18', 8),
(11, 13, '0000-00-00 00:00:00', '2025-12-03 08:16:57', 1741);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `password`) VALUES
(1, 'Jevelou', 'jevelou.suba-an@g.msuiit.edu.ph', '$2y$10$LsqERMcWDtB6jOdcY0zxoOlWZEEI69sM4nAICe/RVmaWTb/ixnGHK'),
(2, 'baday', 'jeonjevelou@gmail.com', '$2y$10$HPFPSZQ9kgby78zn6h8f7eU8Gepi6RT48o2jrlOXTe/EhS/e41u1.'),
(3, 'cristine', 'cristine.obinsa@g.msuiit.edu.ph', '$2y$10$w6MusGKX9E7wrhYBwQvul.r83hTsJ3sriodvHcrqCiLv1sPLaEDhO'),
(4, 'ethan', 'ethanmaghilum@gmail.com', '$2y$10$e3DseH8U/ynwn0ceGM.Uuunda.E1lZrHaEkn.H2uCY54aNLk9iI5a'),
(5, 'kapoy', 'kapoy.51@gmail.com', '$2y$10$6j2l.RVMZFLeKVtA1jIEw.LBN39yi7LmN1Vt8cD3D7DhU5N2KHxQy'),
(6, 'Paul King', 'paulking@gmail.com', '$2y$10$98NAAaos9Wz6il.x0SDSBuICEDoxpenldv6T1I/jWks8m5CZiyaFS'),
(7, 'alvinjay', 'alvinjay.andilab@gmail.com', '$2y$10$/.mWutatXp9fti9TYffdOuDCnDM1M1qVou4VvWJ9qnUbtSpXjBxeq'),
(8, 'Miamore', 'miaamore@gmail.com', '$2y$10$QeBirrAXR4pJDOwj6jItOeKmBq6BUNqcYbG4oYA4Vj.I.VMDRrPZ6'),
(9, 'amore', 'amore.mia@gmail.com', '$2y$10$fe5XHPIyP9.G0iW2gOQNK.D/Fmcrglqvs6bMf4/g4Qd7gq8aEhMlG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `goal`
--
ALTER TABLE `goal`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cat_id` (`cat_id`);

--
-- Indexes for table `timelog`
--
ALTER TABLE `timelog`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `goal`
--
ALTER TABLE `goal`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `timelog`
--
ALTER TABLE `timelog`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `goal`
--
ALTER TABLE `goal`
  ADD CONSTRAINT `goal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `goal_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `category` (`cat_id`);

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `task_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `task_ibfk_2` FOREIGN KEY (`cat_id`) REFERENCES `category` (`cat_id`);

--
-- Constraints for table `timelog`
--
ALTER TABLE `timelog`
  ADD CONSTRAINT `timelog_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
