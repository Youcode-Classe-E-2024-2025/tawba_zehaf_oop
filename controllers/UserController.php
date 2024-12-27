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
    
            // Correctly reference the table_name property
            $query = "SELECT id, username, password FROM " . $this->auth->table_name . " WHERE username = ?";  // Correct use of table_name
    
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $username);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row && password_verify($password, $row['password'])) {
                return [
                    'id' => $row['id'],
                    'username' => $row['username']
                ];
            }
            return false;
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
}

