<?php
require_once 'models/Task.php';  // Ensure this points to the correct path
require_once 'models/User.php';   // Ensure this points to the correct path

class TaskController {
    private $task;
    private $user;

    public function __construct($db) {
        $this->task = new Task($db);
        $this->user = new User($db);
    }

    public function index() {
        $tasks = $this->task->read();
        $users = $this->user->read();
        include 'views/tasks/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->task->title = $_POST['title'];
            $this->task->description = $_POST['description'];
            $this->task->status = $_POST['status'];
            $this->task->type = $_POST['type'];
            $this->task->assigned_to = $_POST['assigned_to'];

            if ($this->task->create()) {
                header('Location: index.php?action=index');
            }
        }
        $users = $this->user->read();
        include 'views/tasks/create.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->task->id = $_POST['id'];
            $this->task->title = $_POST['title'];
            $this->task->description = $_POST['description'];
            $this->task->status = $_POST['status'];
            $this->task->type = $_POST['type'];
            $this->task->assigned_to = $_POST['assigned_to'];

            if ($this->task->update()) {
                header('Location: index.php?action=index');
            }
        }
    }
}