<?php

require_once __DIR__ . '/../models/Authentification.php';
require_once __DIR__ . '/Controller.php';

class UserController extends Controller {
    private $auth;

    public function __construct($db) {
        parent::__construct($db);
        $this->auth = new Authentication($this->db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS); // Updated to sanitize special chars
            $password = $_POST['password']; // We don't sanitize passwords

            $user = $this->auth->login($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $this->regenerateSession();
                echo json_encode(['success' => true, 'redirect' => 'index.php?action=tasks']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
            }
            exit;
        } else {
            $this->render('login', ['csrf_token' => $this->generateCSRFToken()]);
        }
    }

    public function register() {
        error_log('Session data: ' . print_r($_SESSION, true));
        error_log('POST data: ' . print_r($_POST, true));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS); // Updated to sanitize special chars
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // Email sanitization remains the same
            $password = $_POST['password']; // We don't sanitize passwords

            $user_id = $this->auth->register($username, $email, $password);

            if ($user_id) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                $this->regenerateSession();
                header("Location: index.php?action=tasks");
                exit;
            } else {
                $error = "Registration failed. Username or email may already exist.";
                $this->render('register', ['error' => $error, 'csrf_token' => $this->generateCSRFToken()]);
            }
        } else {
            $csrf_token = $this->generateCSRFToken();
            error_log('Generated CSRF token: ' . $csrf_token);
            $this->render('register', ['csrf_token' => $csrf_token]);
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

        $query = "SELECT id, username, email, role FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS); // Updated to sanitize special chars
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // Email sanitization remains the same
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS); // Updated to sanitize special chars

            $query = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                header("Location: index.php?action=list_users");
                exit;
            } else {
                $error = "Failed to update user";
            }
        }

        $query = "SELECT id, username, email, role FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
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
        error_log('Validating CSRF token');
        error_log('Session token: ' . ($_SESSION['csrf_token'] ?? 'not set'));
        error_log('POST token: ' . ($_POST['csrf_token'] ?? 'not set'));

        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            error_log('CSRF validation failed');
            die('CSRF token validation failed');
        }
        error_log('CSRF validation passed');
    }

    private function regenerateSession() {
        $old_session_id = session_id();
        session_regenerate_id(true);
        $new_session_id = session_id();
        // Update the session ID in the database if you're storing sessions there
    }
}