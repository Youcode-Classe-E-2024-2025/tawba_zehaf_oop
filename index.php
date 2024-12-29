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
    case 'create_task': if ($_SESSION['user_role'] === 'admin') {
        $taskController->create();}
        break;
    case 'update_task': if ($_SESSION['user_role'] === 'admin') {
        $taskController->update();}
        break;
    case 'delete_task': if ($_SESSION['user_role'] === 'admin') {
        $taskController->delete();}
        break;
    case 'update_task_status':
        if ($_SESSION['user_role'] === 'admin') {
        $taskController->updateStatus();
    }
        break;
    case 'list_users': if ($_SESSION['user_role'] === 'admin') {
        $userController->listUsers();}
        break;
    case 'edit_user': if ($_SESSION['user_role'] === 'admin') {
        $userController->editUser();}
        break;
    case 'delete_user':  if ($_SESSION['user_role'] === 'admin') {
        $userController->deleteUser();}
        break;
    default:
        header("Location: index.php?action=login");
        exit;
}