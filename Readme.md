# TaskFlow - PHP MVC Task Management Application

## Table of Contents
1. [Introduction](#introduction)
2. [Features](#features)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [Project Structure](#project-structure)
6. [Database Schema](#database-schema)
7. [Usage](#usage)
8. [Security](#security)
9. [Contributing](#contributing)
10. [License](#license)
# TaskFlow

A modern task management application built with PHP MVC architecture.

## Features

- **User Authentication**: Secure login, logout, and registration system
- **Role-Based Access**: Admin and regular user roles with different permissions  
- **Task Management**: Create, read, update and delete tasks
- **Status Tracking**: Monitor task progress with different statuses
- **User Management**: Admin interface for managing users
- **Modern UI**: Responsive design built with Tailwind CSS
- **Security**: CSRF protection and input validation

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Composer

## Installation

1. Clone the repository:
```bash
git clone https://github.com/Youcode-Classe-E-2024-2025/tawba_zehaf_oop.git
cd tawba_zehaf_oop
```

2. Install dependencies:
```bash
composer install
```

3. Configure your database:
- Copy `.env.example` to `.env`
- Update database credentials in `.env`

4. Run database migrations:
```bash
php artisan migrate
```

5. Start the development server:
```bash
php artisan serve
```

## Project Structure

```
tawba-oop/
│   ├── Controllers/    # Application controllers
│   ├── Models/         # Database models
│   └── Views/          # View templates
├── config/             # Configuration files
├── diagrammes/         # diagrammes of erd and uml
```

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'user'),
    created_at TIMESTAMP
);
```

### Tasks Table
```sql
CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100),
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed'),
    user_id INT,
    created_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Usage

1. Register a new account or login with existing credentials
2. Create new tasks with title, description and status
3. View and manage your tasks on the dashboard
4. Admins can access additional user management features

## Security Features

- CSRF Protection
- Password Hashing
- Input Sanitization
- Role-Based Access Control
- Session Management
- SQL Injection Prevention

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please open an issue in the GitHub repository or contact the development team.

## Acknowledgments

- PHP Community
- Tailwind CSS Team
- All contributors who helped shape this project