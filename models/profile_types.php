<?php
require_once '../config/db.php';

class ProfileType {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function getAllProfileTypes() {
        $query = $this->db->prepare("SELECT id, name, description, is_active FROM ws_profile_types ORDER BY id DESC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createProfileType($data) {
        $query = $this->db->prepare("INSERT INTO ws_profile_types (name, description, is_active) VALUES (:name, :description, 1)");
        $query->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $query->bindParam(':description', $data['description'], PDO::PARAM_STR);
        return $query->execute();
    }

    public function updateProfileType($id, $data) {
        $query = $this->db->prepare("UPDATE ws_profile_types SET name = :name, description = :description, is_active = :is_active WHERE id = :id");
        $query->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $query->bindParam(':description', $data['description'], PDO::PARAM_STR);
        $query->bindParam(':is_active', $data['is_active'], PDO::PARAM_INT);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }

    public function disableProfileType($id) {
        $query = $this->db->prepare("UPDATE ws_profile_types SET is_active = 0 WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }
}
?>