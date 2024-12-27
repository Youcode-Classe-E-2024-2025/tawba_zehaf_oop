<?php
class User {
    private $conn;
    private $table = "users";

    public $id;
    public $username;
    public $email;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
            SET
                username = :username,
                email = :email";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Bind parameters
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);

        return $stmt->execute();
    }
}
public function signUp($username, $password) {
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  
    $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $this->connection->prepare($query);

  
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);

   
    if ($stmt->execute()) {
        return "Inscription réussie!";
    } else {
        return "Une erreur est survenue lors de l'inscription.";
    }
}
public function login($username, $password) {
   
    $query = "SELECT id, username, password FROM users WHERE username = :username";
    $stmt = $this->connection->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

  
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

      
        if (password_verify($password, $user['password'])) {
           
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            return "Connexion réussie!";
        } else {
            return "Mot de passe incorrect.";
        }
    } else {
        return "Nom d'utilisateur incorrect.";
    }
}

?>