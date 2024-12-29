<?php

class Controller {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    protected function render($view, $data = []) {
        extract($data);
        include __DIR__ . "/../views/{$view}.php";
    }
}