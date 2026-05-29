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
                ou.parent_id,
                parent_ou.name AS parent_name,
                ou.is_active,
                ou.created_at
            FROM ws_organizational_units ou
            INNER JOIN ws_organizational_unit_types outt
                ON outt.id = ou.organizational_unit_type_id
            LEFT JOIN ws_organizational_units parent_ou
                ON parent_ou.id = ou.parent_id
            WHERE outt.name = 'PROGRAM'
            ORDER BY ou.id DESC
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getProgramParentUnits() {
        $query = $this->db->prepare("
            SELECT
                ou.id,
                ou.name
            FROM ws_organizational_units ou
            INNER JOIN ws_organizational_unit_types outt
                ON outt.id = ou.organizational_unit_type_id
            WHERE outt.name = 'FACULTY'
              AND ou.is_active = 1
            ORDER BY ou.name ASC
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    // Crear un nuevo programa académico
    public function createProgram($data) {
        $query = "
            INSERT INTO ws_organizational_units (
                parent_id,
                name,
                organizational_unit_type_id,
                is_active
            )
            SELECT
                :parent_id,
                :name,
                outt.id,
                1
            FROM ws_organizational_unit_types outt
            WHERE outt.name = 'PROGRAM'
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':parent_id', $data['parent_id'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['name']);
        
        return $stmt->execute();
    }

    // Actualizar programa académico
    public function updateProgram($id, $data) {
        $query = "UPDATE ws_organizational_units 
                    SET 
                        name = :name,
                        parent_id = :parent_id
                    WHERE 
                        id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $data['name_edit']);
        $stmt->bindParam(':parent_id', $data['parent_id_edit'], PDO::PARAM_INT);
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