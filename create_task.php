<?php
session_start();
require_once 'config/Database.php';
require_once 'models\Task.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$task = new Task($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $task->title = htmlspecialchars(strip_tags($_POST['title']));
    $task->description = htmlspecialchars(strip_tags($_POST['description']));
    $task->status = htmlspecialchars(strip_tags($_POST['status']));
    $task->type = htmlspecialchars(strip_tags($_POST['type']));
    $task->assigned_to = intval($_POST['assigned_to']);

    // Perform additional validation
    if (empty($task->title)) {
        $error = "Title is required.";
    } elseif (!in_array($task->status, ['todo', 'in-progress', 'done'])) {
        $error = "Invalid status.";
    } elseif (!in_array($task->type, ['basic', 'bug', 'feature'])) {
        $error = "Invalid task type.";
    } else {
        // Attempt to create the task
        if ($task->create()) {
            header("Location: index.php?action=tasks");
            exit;
        } else {
            $error = "Unable to create task. Please try again.";
        }
    }
}

// Include the view
include 'views/create_task.php';