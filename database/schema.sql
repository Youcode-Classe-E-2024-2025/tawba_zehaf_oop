CREATE DATABASE IF NOT EXISTS taskflow;
USE taskflow;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('todo', 'in-progress', 'done') DEFAULT 'todo',
    type ENUM('basic', 'bug', 'feature') DEFAULT 'basic',
    assigned_to INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id)

);
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