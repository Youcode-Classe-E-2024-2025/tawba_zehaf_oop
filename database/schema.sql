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
INSERT INTO users (username, email) VALUES ('john_doe', 'john.doe@example.com');
INSERT INTO users (username, email) VALUES ('jane_smith', 'jane.smith@example.com');
INSERT INTO users (username, email) VALUES ('alex_jones', 'alex.jones@example.com');
-- Insert tasks into the 'tasks' table
INSERT INTO tasks (title, description, status, type, assigned_to) 
VALUES ('Fix login bug', 'There is an issue with the login page not redirecting after login.', 'in-progress', 'bug', 1);

INSERT INTO tasks (title, description, status, type, assigned_to) 
VALUES ('Add new feature for profile page', 'Implement a new profile page for the app users.', 'todo', 'feature', 2);

INSERT INTO tasks (title, description, status, type, assigned_to) 
VALUES ('Fix typo in the footer', 'There is a minor typo in the website footer text.', 'done', 'basic', 1);

INSERT INTO tasks (title, description, status, type, assigned_to) 
VALUES ('Improve performance of homepage', 'Optimize the homepage loading time and fix rendering issues.', 'in-progress', 'feature', 3);