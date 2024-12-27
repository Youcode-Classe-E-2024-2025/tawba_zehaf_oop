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

            if ($this->task->create()) {
                header("Location: index.php?action=tasks");
                exit;
            } else {
                $error = "Failed to create task";
                $this->render('task_create', ['error' => $error]);
            }
        } else {
            $this->render('task_create');
        }
    }
}
