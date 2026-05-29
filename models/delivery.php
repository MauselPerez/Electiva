<?php
require_once '../config/db.php';

class Delivery {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    private function deliveryExists($personProfileId, $scheduleId) {
        $checkQuery = $this->db->prepare("
            SELECT id
            FROM ws_deliveries
            WHERE person_profile_id = :person_profile_id
              AND delivery_scheduling_id = :delivery_scheduling_id
        ");
        $checkQuery->bindParam(':person_profile_id', $personProfileId, PDO::PARAM_INT);
        $checkQuery->bindParam(':delivery_scheduling_id', $scheduleId, PDO::PARAM_INT);
        $checkQuery->execute();

        return (bool) $checkQuery->fetch();
    }

    private function insertDelivery($personProfileId, $scheduleId, $userId) {
        $query = "
            INSERT INTO ws_deliveries (
                person_profile_id,
                delivery_scheduling_id,
                created_at,
                created_by
            ) VALUES (
                :person_profile_id,
                :delivery_scheduling_id,
                NOW(),
                :created_by
            )
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':person_profile_id', $personProfileId, PDO::PARAM_INT);
        $stmt->bindParam(':delivery_scheduling_id', $scheduleId, PDO::PARAM_INT);
        $stmt->bindParam(':created_by', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    private function deliveryExistsForDay($personProfileId, $scheduleId) {
        $checkQuery = $this->db->prepare("
            SELECT d.id
            FROM ws_deliveries d
            INNER JOIN ws_delivery_scheduling ds
                ON ds.id = d.delivery_scheduling_id
            INNER JOIN ws_delivery_scheduling selected_ds
                ON selected_ds.id = :delivery_scheduling_id
            WHERE d.person_profile_id = :person_profile_id
              AND DATE(ds.delivery_day) = DATE(selected_ds.delivery_day)
            LIMIT 1
        ");
        $checkQuery->bindParam(':person_profile_id', $personProfileId, PDO::PARAM_INT);
        $checkQuery->bindParam(':delivery_scheduling_id', $scheduleId, PDO::PARAM_INT);
        $checkQuery->execute();

        return (bool) $checkQuery->fetch();
    }

    public function findStudentByDocumentNumber($documentNumber) {
        $query = $this->db->prepare("
            SELECT
                pp.id,
                pp.semester,
                pp.is_active,
                pp.photo_path,
                p.document_number,
                p.first_name,
                p.last_name,
                COALESCE(ou.name, 'Sin unidad') AS academic_program
            FROM ws_person_profiles pp
            INNER JOIN ws_profile_types pt
                ON pt.id = pp.profile_type_id
            INNER JOIN ws_persons p
                ON p.id = pp.person_id
            LEFT JOIN ws_organizational_units ou
                ON ou.id = pp.organizational_unit_id
            WHERE p.document_number = :document_number
              AND pt.name = 'STUDENT'
            LIMIT 1
        ");
        $query->bindParam(':document_number', $documentNumber, PDO::PARAM_STR);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetch();
    }

    public function hasDeliveryForSchedule($personProfileId, $scheduleId) {
        return $this->deliveryExists((int) $personProfileId, (int) $scheduleId);
    }

    public function hasDeliveryForDay($personProfileId, $scheduleId) {
        return $this->deliveryExistsForDay((int) $personProfileId, (int) $scheduleId);
    }

    // Obtener todas las entregas
    public function getAllDeliveries() {
        $query = $this->db->prepare("
            SELECT
                d.id,
                d.person_profile_id,
                d.delivery_scheduling_id,
                d.created_at,
                d.created_by,
                ds.delivery_day,
                CONCAT(p.document_number, ' - ', p.first_name, ' ', p.last_name) AS student,
                u.username AS created_by_username,
                'Entregado' AS delivery_status,
                COALESCE(ou.name, 'Sin unidad') AS academic_program
            FROM ws_deliveries d
            INNER JOIN ws_delivery_scheduling ds
                ON ds.id = d.delivery_scheduling_id
            INNER JOIN ws_person_profiles pp
                ON pp.id = d.person_profile_id
            INNER JOIN ws_persons p
                ON p.id = pp.person_id
            INNER JOIN users u
                ON u.id = d.created_by
            LEFT JOIN ws_organizational_units ou
                ON ou.id = pp.organizational_unit_id
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
        $personProfileId = (int) ($data['person_profile_id'] ?? $data['student_id'] ?? 0);
        $scheduleId = (int) $data['delivery_scheduling_id'];

        if ($personProfileId <= 0) {
            return false;
        }

        if ($this->deliveryExists($personProfileId, $scheduleId) || $this->deliveryExistsForDay($personProfileId, $scheduleId)) {
            return false;
        }

        return $this->insertDelivery($personProfileId, $scheduleId, (int) $data['user_id']);
    }

    public function createDeliveriesBatch($personProfileIds, $scheduleId, $userId) {
        $createdCount = 0;
        $scheduleId = (int) $scheduleId;
        $userId = (int) $userId;

        try {
            $this->db->beginTransaction();

            foreach ($personProfileIds as $personProfileId) {
                $personProfileId = (int) $personProfileId;

                if ($this->deliveryExists($personProfileId, $scheduleId)) {
                    throw new Exception('Ya existe al menos una entrega registrada para uno de los estudiantes seleccionados en esta jornada.');
                }

                if ($this->deliveryExistsForDay($personProfileId, $scheduleId)) {
                    throw new Exception('Ya existe al menos una entrega registrada para uno de los estudiantes seleccionados en ese dia.');
                }

                if (!$this->insertDelivery($personProfileId, $scheduleId, $userId)) {
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
                person_profile_id = :person_profile_id,
                delivery_scheduling_id = :delivery_scheduling_id
            WHERE id = :id
        ";

        $personProfileId = (int) ($data['person_profile_id'] ?? $data['student_id'] ?? 0);

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':person_profile_id', $personProfileId, PDO::PARAM_INT);
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