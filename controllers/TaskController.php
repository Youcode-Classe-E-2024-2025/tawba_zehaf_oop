<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Task.php';

class TaskController extends Controller {
    private $task;

    public function __construct($db) {
        parent::__construct($db);
        $this->task = new Task($this->db);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $tasks = $this->task->read();
        $this->render('task_list', ['tasks' => $tasks]);
    }
public function create() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?action=login");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->task->title = $_POST['title'];
        $this->task->description = $_POST['description'];
        $this->task->status = $_POST['status'];
        $this->task->type = $_POST['type'];
        $this->task->assigned_to = $_POST['assigned_to'];
        $this->task->created_by = $_SESSION['user_id'];

        if ($this->task->create()) {
            header("Location: index.php?action=tasks");
            exit;
        } else {
            $error = "Failed to create task";
        }
    }

    $users = $this->getUsers();
    $this->render('task_create', ['users' => $users, 'error' => $error ?? null]);
} 
}
public function update() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php?action=login");
        exit;
    }

    $id = $_GET['id'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->task->id = $id;
        $this->task->title = $_POST['title'];
        $this->task->description = $_POST['description'];
        $this->task->status = $_POST['status'];
        $this->task->type = $_POST['type'];
        $this->task->assigned_to = $_POST['assigned_to'];

        if ($this->task->update()) {
            header("Location: index.php?action=tasks");
            exit;
        } else {
            $error = "Failed to update task";
        }
    }

    $this->task->getById($id);
    $users = $this->getUsers();
    $this->render('task_edit', ['task' => $this->task, 'users' => $users, 'error' => $error ?? null]);
}