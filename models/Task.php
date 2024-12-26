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
    public function __construct($id, $title, $description, $status, $type, $assigned_to, $created_at, $updated_at) {
}
}