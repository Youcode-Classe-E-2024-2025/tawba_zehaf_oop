<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/TaskController.php';

$action = $_GET['action'] ?? 'login';

$database = new Database();
$db = $database->getConnection();

$userController = new UserController($db);
$taskController = new TaskController($db);

switch ($action) {
    case 'login':
        $userController->login();
        break;
    case 'register':
        $userController->register();
        break;
    case 'logout':
        $userController->logout();
        break;
    case 'tasks':
        $taskController->index();
        break;
    case 'create_task':
        $taskController->create();
        break;
    default:
        header("Location: index.php?action=login");
        exit;
}

