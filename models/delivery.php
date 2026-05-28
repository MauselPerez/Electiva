<?php
require_once '../config/db.php';

class Delivery {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    private function deliveryExists($studentId, $scheduleId) {
        $checkQuery = $this->db->prepare("
            SELECT id
            FROM ws_deliveries
            WHERE student_id = :student_id
              AND delivery_scheduling_id = :delivery_scheduling_id
        ");
        $checkQuery->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $checkQuery->bindParam(':delivery_scheduling_id', $scheduleId, PDO::PARAM_INT);
        $checkQuery->execute();

        return (bool) $checkQuery->fetch();
    }

    private function insertDelivery($studentId, $scheduleId, $userId) {
        $query = "
            INSERT INTO ws_deliveries (
                student_id,
                delivery_scheduling_id,
                created_at,
                created_by
            ) VALUES (
                :student_id,
                :delivery_scheduling_id,
                NOW(),
                :created_by
            )
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':delivery_scheduling_id', $scheduleId, PDO::PARAM_INT);
        $stmt->bindParam(':created_by', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function deliveryExistsForDay($studentId, $scheduleId) {
        $checkQuery = $this->db->prepare("
            SELECT d.id
            FROM ws_deliveries d
            INNER JOIN ws_delivery_scheduling ds
                ON ds.id = d.delivery_scheduling_id
            INNER JOIN ws_delivery_scheduling selected_ds
                ON selected_ds.id = :delivery_scheduling_id
            WHERE d.student_id = :student_id
              AND DATE(ds.delivery_day) = DATE(selected_ds.delivery_day)
            LIMIT 1
        ");
        $checkQuery->bindParam(':student_id', $studentId, PDO::PARAM_INT);
        $checkQuery->bindParam(':delivery_scheduling_id', $scheduleId, PDO::PARAM_INT);
        $checkQuery->execute();

        return (bool) $checkQuery->fetch();
    }

    public function findStudentByDocumentNumber($documentNumber) {
        $query = $this->db->prepare("
            SELECT
                s.id,
                s.semester,
                s.is_active,
                s.photo_path,
                p.document_number,
                p.first_name,
                p.last_name,
                ap.name AS academic_program
            FROM ws_students s
            INNER JOIN ws_persons p
                ON p.id = s.person_id
            INNER JOIN ws_academic_programs ap
                ON ap.id = s.academic_program_id
            WHERE p.document_number = :document_number
            LIMIT 1
        ");
        $query->bindParam(':document_number', $documentNumber, PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetch();
    }

    public function hasDeliveryForSchedule($studentId, $scheduleId) {
        return $this->deliveryExists((int) $studentId, (int) $scheduleId);
    }

    public function hasDeliveryForDay($studentId, $scheduleId) {
        return $this->deliveryExistsForDay((int) $studentId, (int) $scheduleId);
    }

    // Obtener todas las entregas
    public function getAllDeliveries() {
        $query = $this->db->prepare("
            SELECT
                d.id,
                d.student_id,
                d.delivery_scheduling_id,
                d.created_at,
                d.created_by,
                ds.delivery_day,
                CONCAT(p.document_number, ' - ', p.first_name, ' ', p.last_name) AS student,
                u.username AS created_by_username,
                'Entregado' AS delivery_status,
                ap.name AS academic_program
            FROM ws_deliveries d
            INNER JOIN ws_delivery_scheduling ds
                ON ds.id = d.delivery_scheduling_id
            INNER JOIN ws_students s
                ON s.id = d.student_id
            INNER JOIN ws_persons p
                ON p.id = s.person_id
            INNER JOIN users u
                ON u.id = d.created_by
            INNER JOIN ws_academic_programs ap
                ON ap.id = s.academic_program_id
            ORDER BY d.id DESC
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getDeliveryScheduleOverview() {
        $query = $this->db->prepare("
            SELECT
                ds.id,
                ds.delivery_day,
                ds.status,
                ds.created_at,
                u.username AS created_by,
                COUNT(d.id) AS delivered_students
            FROM ws_delivery_scheduling ds
            INNER JOIN users u
                ON u.id = ds.created_by
            LEFT JOIN ws_deliveries d
                ON d.delivery_scheduling_id = ds.id
            WHERE ds.status != 2
            GROUP BY
                ds.id,
                ds.delivery_day,
                ds.status,
                ds.created_at,
                u.username
            ORDER BY ds.delivery_day DESC
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getDeliveryScheduleOverviewById($id) {
        $query = $this->db->prepare("
            SELECT
                ds.id,
                ds.delivery_day,
                ds.status,
                ds.created_at,
                u.username AS created_by,
                COUNT(d.id) AS delivered_students
            FROM ws_delivery_scheduling ds
            INNER JOIN users u
                ON u.id = ds.created_by
            LEFT JOIN ws_deliveries d
                ON d.delivery_scheduling_id = ds.id
            WHERE ds.id = :id
            GROUP BY
                ds.id,
                ds.delivery_day,
                ds.status,
                ds.created_at,
                u.username
        ");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetch();
    }

    public function hasPendingSchedulesBefore($scheduleId) {
        $query = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM ws_delivery_scheduling current_ds
            INNER JOIN ws_delivery_scheduling earlier_ds
                ON earlier_ds.delivery_day < current_ds.delivery_day
            WHERE current_ds.id = :schedule_id
              AND earlier_ds.status = 0
        ");
        $query->bindParam(':schedule_id', $scheduleId, PDO::PARAM_INT);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['total'] ?? 0) > 0;
    }

    // Crear una nueva entrega
    public function createDelivery($data) {
        $studentId = (int) $data['student_id'];
        $scheduleId = (int) $data['delivery_scheduling_id'];

        if ($this->deliveryExists($studentId, $scheduleId) || $this->deliveryExistsForDay($studentId, $scheduleId)) {
            return false;
        }

        return $this->insertDelivery($studentId, $scheduleId, (int) $data['user_id']);
    }

    public function createDeliveriesBatch($studentIds, $scheduleId, $userId) {
        $createdCount = 0;
        $scheduleId = (int) $scheduleId;
        $userId = (int) $userId;

        try {
            $this->db->beginTransaction();

            foreach ($studentIds as $studentId) {
                $studentId = (int) $studentId;

                if ($this->deliveryExists($studentId, $scheduleId)) {
                    throw new Exception('Ya existe al menos una entrega registrada para uno de los estudiantes seleccionados en esta jornada.');
                }

                if ($this->deliveryExistsForDay($studentId, $scheduleId)) {
                    throw new Exception('Ya existe al menos una entrega registrada para uno de los estudiantes seleccionados en ese dia.');
                }

                if (!$this->insertDelivery($studentId, $scheduleId, $userId)) {
                    throw new Exception('No fue posible registrar una de las entregas seleccionadas.');
                }

                $createdCount++;
            }

            $this->db->commit();
            return $createdCount;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    // Actualizar entrega
    public function updateDelivery($id, $data) {
        $query = "
            UPDATE ws_deliveries
            SET
                student_id = :student_id,
                delivery_scheduling_id = :delivery_scheduling_id
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':student_id', $data['student_id'], PDO::PARAM_INT);
        $stmt->bindParam(':delivery_scheduling_id', $data['delivery_scheduling_id'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Eliminar entrega
    public function deleteDelivery($id) {
        $query = "DELETE FROM ws_deliveries WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateDeliverySchedulingStatus($id, $status) {
        $query = "UPDATE ws_delivery_scheduling SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>