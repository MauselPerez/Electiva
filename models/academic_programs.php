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
        $query = $this->db->prepare("
            SELECT
                ou.id,
                ou.name,
                ou.is_active,
                ou.created_at
            FROM ws_organizational_units ou
            INNER JOIN ws_organizational_unit_types outt
                ON outt.id = ou.organizational_unit_type_id
            WHERE outt.name = 'PROGRAM'
            ORDER BY ou.id DESC
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    // Crear un nuevo programa académico
    public function createProgram($data) {
        $query = "
            INSERT INTO ws_organizational_units (
                name,
                organizational_unit_type_id,
                is_active
            )
            SELECT
                :name,
                outt.id,
                1
            FROM ws_organizational_unit_types outt
            WHERE outt.name = 'PROGRAM'
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        
        return $stmt->execute();
    }

    // Actualizar programa académico
    public function updateProgram($id, $data) {
        $query = "UPDATE ws_organizational_units 
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
        $query = "
            UPDATE ws_organizational_units
            SET is_active = 0
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}