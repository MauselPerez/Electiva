<?php
require_once '../config/db.php';

class Schedule {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    // Obtener todas las planificaciones
    public function getAllSchedules() {
        $query = $this->db->prepare(
            "SELECT 
                ds.*,
                u.username AS created_by
            FROM 
                ws_delivery_scheduling ds
            INNER JOIN 
                users u
                    ON ds.created_by = u.id");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    // Obtener todas las planificaciones exepto las canceladas
    public function getAllSchedulesNotCanceled() {
        $query = $this->db->prepare(
            "SELECT 
                        ws_delivery_scheduling.*,
                        users.username AS created_by
                    FROM 
                        ws_delivery_scheduling
                    INNER JOIN 
                        users
                            ON ws_delivery_scheduling.created_by = users.id
                    WHERE 
                        ws_delivery_scheduling.status != 2");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getPendingSchedules() {
        $query = $this->db->prepare(
            "SELECT
                        ws_delivery_scheduling.*,
                        users.username AS created_by
                    FROM
                        ws_delivery_scheduling
                    INNER JOIN
                        users
                            ON ws_delivery_scheduling.created_by = users.id
                    WHERE
                        ws_delivery_scheduling.status = 0
                    ORDER BY
                        ws_delivery_scheduling.delivery_day ASC"
        );
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    // Crear una nueva planificación
    public function createSchedule($data) {
        $query = "INSERT INTO ws_delivery_scheduling (delivery_day, created_at, created_by) 
                    VALUES (:delivery_day, now(), :created_by)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':delivery_day', $data['delivery_day']);
        $stmt->bindParam(':created_by', $data['created_by']);

        return $stmt->execute();
    }

    // Actualizar planificación
    public function updateSchedule($id, $data) {
        $query = "UPDATE ws_delivery_scheduling 
                    SET 
                        start_date = :start_date, 
                        end_date = :end_date, 
                        delivery_day = :delivery_day 
                    WHERE 
                        id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':delivery_day', $data['delivery_day']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Eliminar planificación
    public function deleteSchedule($id) {
        $query = "DELETE FROM ws_delivery_scheduling WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    //Cancelar entrega
    public function cancelSchedule($id) {
        $query = "UPDATE ws_delivery_scheduling 
                    SET 
                        status = 2
                    WHERE 
                        id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}