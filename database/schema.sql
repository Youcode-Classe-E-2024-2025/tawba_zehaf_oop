CREATE DATABASE IF NOT EXISTS taskflow;
USE taskflow;
-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user', 'admin') DEFAULT 'user',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
);

-- Tasks table
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `status` enum('todo', 'doing', 'done') DEFAULT 'todo',
  `type` enum('basic', 'bug', 'feature') DEFAULT 'basic',
  `assigned_to` int(11),
  `created_by` int(11),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ;

-- many-to-one relationship between tasks and users
-- Insert users into the 'users' table
-- Inserting Users
INSERT INTO `users` (`username`, `email`, `password`, `role`) 
VALUES 
('john_doe', 'john.doe@example.com', 'password123', 'user'),
('jane_smith', 'jane.smith@example.com', 'password456', 'user'),
('admin_user', 'admin@example.com', 'adminpassword', 'admin'),
('mark_taylor', 'mark.taylor@example.com', 'password789', 'user'),
('lisa_jones', 'lisa.jones@example.com', 'password101', 'user'),
('alice_williams', 'alice.williams@example.com', 'password102', 'admin'),
('bob_brown', 'bob.brown@example.com', 'password103', 'user'),
('charlie_davis', 'charlie.davis@example.com', 'password104', 'user'),
('susan_white', 'susan.white@example.com', 'password105', 'admin'),
('tom_jackson', 'tom.jackson@example.com', 'password106', 'user');
-- Inserting Tasks
INSERT INTO `tasks` (`title`, `description`, `status`, `type`, `assigned_to`, `created_by`) 
VALUES
('Fix Login Bug', 'Resolve the issue where users cannot log in with correct credentials', 'doing', 'bug', 2, 3),
('Develop User Dashboard', 'Create a dashboard to display user statistics and activities', 'todo', 'feature', 4, 1),
('UI Improvements', 'Improve the user interface for the homepage', 'todo', 'feature', 1, 2),
('Fix Button Alignment', 'Ensure all buttons on the settings page are aligned properly', 'done', 'bug', 5, 3),
('Task Filtering', 'Implement a feature to filter tasks by status and type', 'doing', 'feature', 3, 1),
('API Documentation', 'Write documentation for the newly developed API endpoints', 'done', 'basic', 2, 5),
('Refactor Code', 'Refactor the codebase to improve readability and performance', 'todo', 'basic', 7, 4),
('Add Search Functionality', 'Add a search feature to the tasks page', 'todo', 'feature', 8, 6),
('Improve Task Notifications', 'Send email notifications when a task is updated', 'todo', 'bug', 9, 3),
('Test New Features', 'Perform testing for the new features developed in the last sprint', 'doing', 'basic', 6, 2);
