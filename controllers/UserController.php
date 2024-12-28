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
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->auth->login($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php?action=tasks");
                exit;
            } else {
                $error = "Invalid username or password";
                $this->render('login', ['error' => $error]);
            }
        } else {
            $this->render('login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user_id = $this->auth->register($username, $email, $password);

            if ($user_id) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                header("Location: index.php?action=tasks");
                exit;
            } else {
                $error = "Registration failed. Username or email may already exist.";
                $this->render('register', ['error' => $error]);
            }
        } else {
            $this->render('register');
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }

    public function listUsers() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $query = "SELECT id, username, email, role FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('user_list', ['users' => $users]);
    }

    public function editUser() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $id = $_GET['id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $role = $_POST['role'];

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

        $this->render('edit_user', ['user' => $user, 'error' => $error ?? null]);
    }

    public function deleteUser() {
        if ($_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=tasks");
            exit;
        }

        $id = $_GET['id'] ?? null;

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
}