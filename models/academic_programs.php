<?php
require_once '../config/db.php';

class AcademicProgram {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    // Obtener todos los programas académicos
    public function getAllPrograms() {
        $query = $this->db->prepare("SELECT * FROM ws_academic_programs");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    // Crear un nuevo programa académico
    public function createProgram($data) {
        $query = "INSERT INTO ws_academic_programs (name) 
                    VALUES (:name)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        
        return $stmt->execute();
    }

    // Actualizar programa académico
    public function updateProgram($id, $data) {
        $query = "UPDATE ws_academic_programs 
                    SET 
                        name = :name 
                    WHERE 
                        id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name_edit']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Eliminar programa académico
    public function deleteProgram($id) {
        $query = "DELETE FROM ws_academic_programs WHERE id = :id; ALTER TABLE ws_academic_programs AUTO_INCREMENT = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}