<?php
require_once '../config/db.php';

class Role {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function getAllRoles() {
        $query = $this->db->prepare("SELECT id, name, is_active FROM ws_roles ORDER BY id DESC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRole($name) {
        $query = $this->db->prepare("INSERT INTO ws_roles (name, is_active) VALUES (:name, 1)");
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        return $query->execute();
    }

    public function updateRole($id, $name, $isActive) {
        $query = $this->db->prepare("UPDATE ws_roles SET name = :name, is_active = :is_active WHERE id = :id");
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':is_active', $isActive, PDO::PARAM_INT);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }

    public function disableRole($id) {
        $query = $this->db->prepare("UPDATE ws_roles SET is_active = 0 WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }
}
?>