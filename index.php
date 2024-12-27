<?php
session_start();
require_once 'auth/login.php';
require_once 'auth/signup.php';
require_once 'config/Database.php';
require_once 'models/Task.php';
require_once 'models/User.php';
require_once 'controllers/TaskController.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize controllers
$taskController = new TaskController($db);

// Simple routing
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Check if user is logged in for protected routes
$protected_routes = ['index', 'create', 'update'];
if(in_array($action, $protected_routes) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?action=showLogin');
    exit();
}

switch($action) {
    case 'showLogin':
        include 'views/auth/login.php';
        break;
    case 'login':
        $user = new User($db);
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $user->login($_POST['email'], $_POST['password']);
            if($result) {
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                header('Location: index.php?action=index');
            } else {
                $error = "Invalid email or password";
                include 'views/auth/login.php';
            }
        }
        break;
    case 'showSignup':
        include 'views/auth/signup.php';
        break;
    case 'signup':
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User($db);
            $user->username = $_POST['username'];
            $user->email = $_POST['email'];
            $user->password = $_POST['password'];
            
            if($user->create()) {
                header('Location: index.php?action=showLogin');
            } else {
                $error = "Error creating account";
                include 'views/auth/signup.php';
            }
        }
        break;
    case 'logout':
        session_destroy();
        header('Location: index.php?action=showLogin');
        break;
    case 'create':
        $taskController->create();
        break;
    case 'update':
        $taskController->update();
        break;
    default:
        $taskController->index();
        break;
}