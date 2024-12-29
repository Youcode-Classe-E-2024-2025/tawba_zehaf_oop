<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/Controller.php';

class UserController extends Controller {
    private $user;

    public function __construct($db) {
        parent::__construct($db);
        $this->user = new User($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // We don't sanitize passwords

            // Server-side validation
            $errors = [];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long";
            }

            if (empty($errors)) {
                $user = $this->user->login($email, $password);

                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $this->regenerateSession();
                    echo json_encode(['success' => true, 'redirect' => 'index.php?action=tasks']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => implode(", ", $errors)]);
            }
            exit;
        } else {
            $this->render('login', ['csrf_token' => $this->generateCSRFToken()]);
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // We don't sanitize passwords

            // Server-side validation
            $errors = [];
            if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
                $errors[] = "Username must be 3-20 characters long and contain only letters, numbers, and underscores";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
            if (strlen($password) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
                $errors[] = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number";
            }

            if (empty($errors)) {
                $user_id = $this->user->create($username, $email, $password);

                if ($user_id) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = 'user';
                    $this->regenerateSession();
                    header("Location: index.php?action=tasks");
                    exit;
                } else {
                    $error = "Registration failed. Username or email may already exist.";
                }
            } else {
                $error = implode(", ", $errors);
            }
            $this->render('register', ['error' => $error, 'csrf_token' => $this->generateCSRFToken()]);
        } else {
            $this->render('register', ['csrf_token' => $this->generateCSRFToken()]);
        }
    }

    public function logout() {
        $this->regenerateSession();
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }

    public function listUsers() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $users = $this->user->getAll();
        $this->render('user_list', ['users' => $users, 'csrf_token' => $this->generateCSRFToken()]);
    }

    public function editUser() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($this->user->update($id, $username, $email, $role)) {
                header("Location: index.php?action=list_users");
                exit;
            } else {
                $error = "Failed to update user";
            }
        }

        $user = $this->user->getById($id);
        $this->render('edit_user', ['user' => $user, 'error' => $error ?? null, 'csrf_token' => $this->generateCSRFToken()]);
    }

    public function deleteUser() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $this->validateCSRFToken();
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($id) {
            if ($this->user->delete($id)) {
                header("Location: index.php?action=list_users");
                exit;
            }
        }

        header("Location: index.php?action=list_users&error=delete_failed");
        exit;
    }

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

    private function regenerateSession() {
        $old_session_id = session_id();
        session_regenerate_id(true);
        $new_session_id = session_id();
        // Update the session ID in the database if you're storing sessions there
    }
}