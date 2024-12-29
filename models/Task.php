<?php

class Task {
    private $conn;
    private $table_name = "tasks";

    public $id;
    public $title;
    public $description;
    public $status;
    public $type;
    public $assigned_to;
    public $created_by;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET title=:title, description=:description, status=:status, type=:type, assigned_to=:assigned_to, created_by=:created_by";
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->assigned_to = htmlspecialchars(strip_tags($this->assigned_to));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":assigned_to", $this->assigned_to);
        $stmt->bindParam(":created_by", $this->created_by);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read($user_id, $role) {
        $query = "SELECT t.id, t.title, t.description, t.status, t.type, t.assigned_to, u.username as assigned_to_name, t.created_by, c.username as created_by_name, t.created_at, t.updated_at 
                  FROM " . $this->table_name . " t 
                  LEFT JOIN users u ON t.assigned_to = u.id 
                  LEFT JOIN users c ON t.created_by = c.id";
        
        if ($role !== 'admin') {
            $query .= " WHERE t.assigned_to = :user_id";
        }
        
        $query .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($role !== 'admin') {
            $stmt->bindParam(':user_id', $user_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET title = :title, description = :description, status = :status, type = :type, assigned_to = :assigned_to
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->assigned_to = htmlspecialchars(strip_tags($this->assigned_to));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':assigned_to', $this->assigned_to);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->type = $row['type'];
            $this->assigned_to = $row['assigned_to'];
            $this->created_by = $row['created_by'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $status = htmlspecialchars(strip_tags($status));
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}