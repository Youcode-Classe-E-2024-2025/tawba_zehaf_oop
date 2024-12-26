<?php
class Task {
    private $conn;
    private $table = "tasks";

    // Properties
    public $id;
    public $title;
    public $description;
    public $status;
    public $type;
    public $assigned_to;
    public $created_at;
    public $updated_at;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
            SET
                title = :title,
                description = :description,
                status = :status,
                type = :type,
                assigned_to = :assigned_to,
                created_at = :created_at,
                updated_at = :updated_at";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->assigned_to = $this->assigned_to ? intval($this->assigned_to) : null;

        // Bind parameters
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":assigned_to", $this->assigned_to);
        $stmt->bindParam(":created_at", date('Y-m-d H:i:s'));
        $stmt->bindParam(":updated_at", date('Y-m-d H:i:s'));

        return $stmt->execute();
    }

    public function read() {
        $query = "SELECT t.*, u.username as assigned_username 
                 FROM " . $this->table . " t 
                 LEFT JOIN users u ON t.assigned_to = u.id 
                 ORDER BY t.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table . "
            SET
                title = :title,
                description = :description,
                status = :status,
                type = :type,
                assigned_to = :assigned_to,
                updated_at = :updated_at
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->assigned_to = $this->assigned_to ? intval($this->assigned_to) : null;

        // Bind parameters
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":assigned_to", $this->assigned_to);
        $stmt->bindParam(":updated_at", date('Y-m-d H:i:s'));

        return $stmt->execute();
    }
}
