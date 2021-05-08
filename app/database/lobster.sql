-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2021 at 05:59 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lobster`
--

-- --------------------------------------------------------

--
-- Table structure for table `catagories`
--

CREATE TABLE `catagories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `catagories`
--

INSERT INTO `catagories` (`cat_id`, `cat_name`) VALUES
(1, 'PHP'),
(2, 'Web design'),
(3, 'Web development'),
(4, 'WordPress');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `comment_content` longtext NOT NULL,
  `comment_date` varchar(100) NOT NULL,
  `comment_author` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `comment_replies`
--

CREATE TABLE `comment_replies` (
  `cr_id` int(11) NOT NULL,
  `cr_content` longtext NOT NULL,
  `cr_date` varchar(100) DEFAULT NULL,
  `cr_author` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `nf_id` int(11) NOT NULL,
  `nf_title` text NOT NULL,
  `nf_date` varchar(100) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `nf_status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `post_title` varchar(100) NOT NULL,
  `post_content` longtext NOT NULL,
  `post_author` int(11) NOT NULL,
  `post_date` varchar(100) NOT NULL,
  `post_cat` int(11) NOT NULL,
  `post_link` varchar(50) NOT NULL,
  `post_status` varchar(100) NOT NULL,
  `post_read` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `post_files`
--

CREATE TABLE `post_files` (
  `pflile_id` int(11) NOT NULL,
  `pfile_name` varchar(255) NOT NULL,
  `pfile_ext` varchar(10) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `post_ratings`
--

CREATE TABLE `post_ratings` (
  `pr_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `pr_action` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `saved_posts`
--

CREATE TABLE `saved_posts` (
  `sp_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `sub_id` int(11) NOT NULL,
  `sub_owner` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sub_date` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `token_id` int(11) NOT NULL,
  `token_selector` varchar(255) NOT NULL,
  `token_validator` longtext NOT NULL,
  `token_expires` varchar(255) NOT NULL,
  `token_usage` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_fname` varchar(50) NOT NULL,
  `user_lname` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_email_status` varchar(50) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_joining_date` varchar(50) NOT NULL,
  `user_country` varchar(50) NOT NULL,
  `user_account_status` int(11) NOT NULL,
  `user_role` varchar(100) NOT NULL,
  `user_desc` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_fname`, `user_lname`, `user_name`, `user_email`, `user_email_status`, `user_password`, `user_joining_date`, `user_country`, `user_account_status`, `user_role`, `user_desc`) VALUES
(41, 'afrad', 'bhuiyan', 'afradbhuiyan', 'abspiker7070@gmail.com', 'verified', '$2y$10$1/Vgffb/l3mKuEMz1EWi..TAid5TQJUwjcCQtqjuKRifzOmEpW5yS', '07 May, 2021', 'Bangladesh', 1, 'admin', 'false'),
(42, 'ranveer', 'singh', 'ranveersingh', 'afradbhuiyan2021@gmail.com', 'verified', '$2y$10$FHfLHMLPpWaSUJg3HlmxBupkJ/BWeuumsQOcgP3QOfQ5p5RTtt9lS', '07 May, 2021', 'Bangladesh', 1, 'creator', 'false'),
(43, 'shahid', 'kapur', 'shahidkapur', 'afradbhuiyan@yahoo.com', 'verified', '$2y$10$/PEhT7eYagrwO8aBtuxn7.cIwrUH6SpUh7x8OaxFE0yN6tQpV1Xrq', '07 May, 2021', 'Bangladesh', 1, 'creator', 'false');

-- --------------------------------------------------------

--
-- Table structure for table `user_files`
--

CREATE TABLE `user_files` (
  `ufile_id` int(11) NOT NULL,
  `ufile_name` varchar(255) NOT NULL,
  `ufile_ext` varchar(10) NOT NULL,
  `ufile_usage` varchar(150) NOT NULL,
  `ufile_dimension` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ufile_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_files`
--

INSERT INTO `user_files` (`ufile_id`, `ufile_name`, `ufile_ext`, `ufile_usage`, `ufile_dimension`, `user_id`, `ufile_status`) VALUES
(63, 'wVYdSTEJDhZ', 'jpg', 'profile_img', 'a:3:{s:2:\"lg\";a:2:{s:5:\"width\";i:200;s:6:\"height\";i:200;}s:2:\"md\";a:2:{s:5:\"width\";i:150;s:6:\"height\";i:150;}s:2:\"sm\";a:2:{s:5:\"width\";i:100;s:6:\"height\";i:100;}}', 41, 1),
(64, 'default-bg', 'jpg', 'bg_img', 'a:2:{s:2:\"lg\";a:2:{s:5:\"width\";i:1400;s:6:\"height\";i:350;}s:2:\"sm\";a:2:{s:5:\"width\";i:250;s:6:\"height\";i:100;}}', 41, 0),
(65, '2HZzG_9iCwR', 'jpg', 'profile_img', 'a:3:{s:2:\"lg\";a:2:{s:5:\"width\";i:200;s:6:\"height\";i:200;}s:2:\"md\";a:2:{s:5:\"width\";i:150;s:6:\"height\";i:150;}s:2:\"sm\";a:2:{s:5:\"width\";i:100;s:6:\"height\";i:100;}}', 42, 1),
(66, 'default-bg', 'jpg', 'bg_img', 'a:2:{s:2:\"lg\";a:2:{s:5:\"width\";i:1400;s:6:\"height\";i:350;}s:2:\"sm\";a:2:{s:5:\"width\";i:250;s:6:\"height\";i:100;}}', 42, 0),
(67, 's', 'jpg', 'profile_img', 'a:3:{s:2:\"lg\";a:2:{s:5:\"width\";i:200;s:6:\"height\";i:200;}s:2:\"md\";a:2:{s:5:\"width\";i:150;s:6:\"height\";i:150;}s:2:\"sm\";a:2:{s:5:\"width\";i:100;s:6:\"height\";i:100;}}', 43, 0),
(68, 'default-bg', 'jpg', 'bg_img', 'a:2:{s:2:\"lg\";a:2:{s:5:\"width\";i:1400;s:6:\"height\";i:350;}s:2:\"sm\";a:2:{s:5:\"width\";i:250;s:6:\"height\";i:100;}}', 43, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `catagories`
--
ALTER TABLE `catagories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `comment_author` (`comment_author`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD PRIMARY KEY (`cr_id`),
  ADD KEY `cr_author` (`cr_author`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`nf_id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `to_user_id` (`to_user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `post_cat` (`post_cat`),
  ADD KEY `post_author` (`post_author`);

--
-- Indexes for table `post_files`
--
ALTER TABLE `post_files`
  ADD PRIMARY KEY (`pflile_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `post_ratings`
--
ALTER TABLE `post_ratings`
  ADD PRIMARY KEY (`pr_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD PRIMARY KEY (`sp_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`sub_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sub_owner` (`sub_owner`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_files`
--
ALTER TABLE `user_files`
  ADD PRIMARY KEY (`ufile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catagories`
--
ALTER TABLE `catagories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `comment_replies`
--
ALTER TABLE `comment_replies`
  MODIFY `cr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `nf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `post_files`
--
ALTER TABLE `post_files`
  MODIFY `pflile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `post_ratings`
--
ALTER TABLE `post_ratings`
  MODIFY `pr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `saved_posts`
--
ALTER TABLE `saved_posts`
  MODIFY `sp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `sub_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user_files`
--
ALTER TABLE `user_files`
  MODIFY `ufile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`comment_author`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD CONSTRAINT `comment_replies_ibfk_1` FOREIGN KEY (`cr_author`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comment_replies_ibfk_2` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`post_cat`) REFERENCES `catagories` (`cat_id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`post_cat`) REFERENCES `catagories` (`cat_id`),
  ADD CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`post_cat`) REFERENCES `catagories` (`cat_id`),
  ADD CONSTRAINT `posts_ibfk_4` FOREIGN KEY (`post_author`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `post_files`
--
ALTER TABLE `post_files`
  ADD CONSTRAINT `post_files_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `post_ratings`
--
ALTER TABLE `post_ratings`
  ADD CONSTRAINT `post_ratings_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `post_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `saved_posts`
--
ALTER TABLE `saved_posts`
  ADD CONSTRAINT `saved_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `saved_posts_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD CONSTRAINT `subscribers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `subscribers_ibfk_2` FOREIGN KEY (`sub_owner`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_files`
--
ALTER TABLE `user_files`
  ADD CONSTRAINT `user_files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
