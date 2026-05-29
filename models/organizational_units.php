<?php
require_once '../config/db.php';

class OrganizationalUnit {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function getAllUnitTypes() {
        $query = $this->db->prepare("SELECT id, name FROM ws_organizational_unit_types WHERE is_active = 1 ORDER BY name ASC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUnits() {
        $query = $this->db->prepare("
            SELECT
                ou.id,
                ou.name,
                ou.parent_id,
                parent.name AS parent_name,
                outt.id AS organizational_unit_type_id,
                outt.name AS unit_type,
                ou.is_active,
                ou.created_at
            FROM ws_organizational_units ou
            INNER JOIN ws_organizational_unit_types outt
                ON outt.id = ou.organizational_unit_type_id
            LEFT JOIN ws_organizational_units parent
                ON parent.id = ou.parent_id
            ORDER BY
                CASE outt.name
                    WHEN 'RECTORY' THEN 1
                    WHEN 'VICERRECTORY' THEN 2
                    WHEN 'FACULTY' THEN 3
                    WHEN 'DEPARTMENT' THEN 4
                    WHEN 'AREA' THEN 5
                    WHEN 'PROGRAM' THEN 6
                    ELSE 7
                END,
                COALESCE(parent.name, ou.name),
                ou.name
        ");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParentCandidates($excludeId = null) {
        $sql = "
            SELECT id, name
            FROM ws_organizational_units
            WHERE is_active = 1
        ";

        if ($excludeId !== null) {
            $sql .= " AND id <> :exclude_id";
        }

        $sql .= " ORDER BY name ASC";

        $query = $this->db->prepare($sql);
        if ($excludeId !== null) {
            $query->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }

        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUnit($data) {
        $query = $this->db->prepare("
            INSERT INTO ws_organizational_units (
                parent_id,
                organizational_unit_type_id,
                name,
                is_active
            ) VALUES (
                :parent_id,
                :organizational_unit_type_id,
                :name,
                :is_active
            )
        ");

        $parentId = !empty($data['parent_id']) ? (int) $data['parent_id'] : null;
        $isActive = isset($data['is_active']) ? (int) $data['is_active'] : 1;

        if ($parentId === null) {
            $query->bindValue(':parent_id', null, PDO::PARAM_NULL);
        } else {
            $query->bindValue(':parent_id', $parentId, PDO::PARAM_INT);
        }
        $query->bindParam(':organizational_unit_type_id', $data['organizational_unit_type_id'], PDO::PARAM_INT);
        $query->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $query->bindParam(':is_active', $isActive, PDO::PARAM_INT);

        return $query->execute();
    }

    public function updateUnit($id, $data) {
        $id = (int) $id;
        $parentId = !empty($data['parent_id']) ? (int) $data['parent_id'] : null;
        $isActive = isset($data['is_active']) ? (int) $data['is_active'] : 1;

        if ($parentId !== null && $parentId === $id) {
            throw new Exception('Una unidad no puede ser su propio padre.');
        }

        $query = $this->db->prepare("
            UPDATE ws_organizational_units
            SET
                parent_id = :parent_id,
                organizational_unit_type_id = :organizational_unit_type_id,
                name = :name,
                is_active = :is_active
            WHERE id = :id
        ");

        if ($parentId === null) {
            $query->bindValue(':parent_id', null, PDO::PARAM_NULL);
        } else {
            $query->bindValue(':parent_id', $parentId, PDO::PARAM_INT);
        }
        $query->bindParam(':organizational_unit_type_id', $data['organizational_unit_type_id'], PDO::PARAM_INT);
        $query->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $query->bindParam(':is_active', $isActive, PDO::PARAM_INT);
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        return $query->execute();
    }

    public function deleteUnit($id) {
        $id = (int) $id;

        $hasChildren = $this->db->prepare("SELECT COUNT(*) AS total FROM ws_organizational_units WHERE parent_id = :id AND is_active = 1");
        $hasChildren->bindParam(':id', $id, PDO::PARAM_INT);
        $hasChildren->execute();
        $childrenCount = (int) ($hasChildren->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        if ($childrenCount > 0) {
            throw new Exception('No puedes desactivar esta unidad porque tiene unidades hijas activas.');
        }

        $query = $this->db->prepare("UPDATE ws_organizational_units SET is_active = 0 WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }
}
?>