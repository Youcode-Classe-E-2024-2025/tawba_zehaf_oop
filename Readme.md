# TaskFlow - Task Management System

A simple task management system built with PHP using MVC architecture.

## Deployment Instructions

1. Server Requirements:
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Apache with mod_rewrite enabled

2. Database Setup:
   - Create a MySQL database
   - Import `config/init.sql`
   - Update database credentials in `config/config.php`

3. Application Setup:
   - Upload all files to your web server
   - Ensure the web server has write permissions for the `logs` directory
   - Configure your domain to point to the `public` directory

4. Configuration:
   - Update `BASE_URL` in `config/config.php`
   - Set `DEBUG_MODE` to `false` in production

## Directory Structure

```
taskflow/
├── config/         # Configuration files
├── controllers/    # MVC Controllers
├── models/         # MVC Models
├── views/          # MVC Views
└── public/         # Public assets