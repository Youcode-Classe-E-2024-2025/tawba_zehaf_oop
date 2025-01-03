<?php 

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Task.php';

class TaskController extends Controller {
    private $task;

    public function __construct($db) {
        parent::__construct($db);
        $this->task = new Task($db);
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $tasks = $this->task->read($_SESSION['user_id'], $_SESSION['role']);
        $this->render('task_list', ['tasks' => $tasks, 'csrf_token' => $this->generateCSRFToken()]);
    }

    public function create() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
            $this->task->title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->assigned_to = filter_input(INPUT_POST, 'assigned_to', FILTER_SANITIZE_NUMBER_INT);
            $this->task->created_by = $_SESSION['user_id'];

            if ($this->task->create()) {
                header("Location: index.php?action=tasks");
                exit;
            } else {
                $error = "Failed to create task";
            }
        }

        $users = $this->getUsers();
        $this->render('task_create', ['users' => $users, 'error' => $error ?? null, 'csrf_token' => $this->generateCSRFToken()]);
    }

    public function update() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
            $this->task->id = $id;
            $this->task->title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->task->assigned_to = filter_input(INPUT_POST, 'assigned_to', FILTER_SANITIZE_NUMBER_INT);

            if ($this->task->update()) {
                header("Location: index.php?action=tasks");
                exit;
            } else {
                $error = "Failed to update task";
            }
        }

        $this->task->getById($id);
        $users = $this->getUsers();
        $this->render('task_edit', ['task' => $this->task, 'users' => $users, 'error' => $error ?? null, 'csrf_token' => $this->generateCSRFToken()]);
    }

    public function delete() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $this->validateCSRFToken();
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($id) {
            $this->task->id = $id;
            if ($this->task->delete()) {
                header("Location: index.php?action=tasks");
                exit;
            }
        }

        header("Location: index.php?action=tasks&error=delete_failed");
        exit;
    }

    public function updateStatus() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $this->validateCSRFToken();

        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$id || !$status) {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }

        $task = new Task($this->db);
        $task->getById($id);

        if ($_SESSION['role'] === 'admin' || $task->assigned_to == $_SESSION['user_id']) {
            if ($task->updateStatus($id, $status)) {
                echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task status']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'You don\'t have permission to update this task']);
        }
        exit;
    }

    public function editTask() {
        $task_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if ($task_id) {
            $query = "SELECT * FROM tasks WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $task_id);
            $stmt->execute();
            $task = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($task) {
                $this->render('task_edit', ['task' => $task, 'users' => $this->getUsers(), 'csrf_token' => $this->generateCSRFToken()]);
            } else {
                // Task not found, handle the error
                $error = "Task not found";
                $this->render('task_edit', ['error' => $error, 'csrf_token' => $this->generateCSRFToken()]);
            }
        } else {
            // No task ID provided, redirect or handle error
            header("Location: index.php?action=tasks");
            exit;
        }
    }
    private function getUsers() {
        $query = "SELECT id, username FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); }

    private function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function validateCSRFToken() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF token validation failed');
        }
    }
}