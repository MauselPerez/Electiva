<?php
require_once '../config/db.php';

class Reports {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    private function fetchValue($query) {
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetch();
    }

    // Obtener todos los reportes
    public function getDeliveryDaysPerMonth() {
        $query = $this->db->prepare(
    "SELECT
                COUNT(*) AS delivery_days
            FROM
                ws_delivery_scheduling
            WHERE MONTH(delivery_day) = MONTH(CURDATE())
              AND YEAR(delivery_day) = YEAR(CURDATE())
              AND status != 2
            ");
        return $this->fetchValue($query);
    }

    // Obtener todos los estudiantes
    public function getStudents() {
        $query = $this->db->prepare(
    "SELECT
                COUNT(*) AS students
            FROM
                ws_students
            WHERE
                is_active = 1
            ");
        return $this->fetchValue($query);
    }

    public function getDelivered()
    {
        $query = $this->db->prepare(
    "SELECT
                COUNT(*) AS delivered
            FROM
                ws_deliveries
            INNER JOIN
                ws_delivery_scheduling
                    ON ws_delivery_scheduling.id = ws_deliveries.delivery_scheduling_id
            WHERE
                MONTH(ws_delivery_scheduling.delivery_day) = MONTH(CURDATE())
                AND YEAR(ws_delivery_scheduling.delivery_day) = YEAR(CURDATE())
        ");
        return $this->fetchValue($query);
    }

    //Promedio de entregas al mes
    public function getDeliveriesByMonth()
    {
        $query = $this->db->prepare(
        "SELECT 
                    COUNT(*) AS delivered,
                    MONTHNAME(ws_delivery_scheduling.delivery_day) AS month,
                    MONTH(ws_delivery_scheduling.delivery_day) AS month_number
                FROM 
                    ws_deliveries
                INNER JOIN
                    ws_delivery_scheduling
                        ON ws_delivery_scheduling.id = ws_deliveries.delivery_scheduling_id
                GROUP BY
                    MONTH(ws_delivery_scheduling.delivery_day),
                    MONTHNAME(ws_delivery_scheduling.delivery_day)
                ORDER BY
                    MONTH(ws_delivery_scheduling.delivery_day)
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getSchedules() {
        $query = $this->db->prepare(
            "SELECT 
                        ws_delivery_scheduling.id,
                        ws_delivery_scheduling.delivery_day,
                        ws_delivery_scheduling.created_at,
                        ws_delivery_scheduling.status,
                        users.username AS created_by,
                        COUNT(ws_deliveries.id) AS delivered_students
                    FROM 
                        ws_delivery_scheduling
                    INNER JOIN 
                        users
                            ON ws_delivery_scheduling.created_by = users.id
                    LEFT JOIN
                        ws_deliveries
                            ON ws_delivery_scheduling.id = ws_deliveries.delivery_scheduling_id
                    GROUP BY
                        ws_delivery_scheduling.id,
                        ws_delivery_scheduling.delivery_day,
                        ws_delivery_scheduling.created_at,
                        ws_delivery_scheduling.status,
                        users.username
                    ORDER BY
                        ws_delivery_scheduling.delivery_day DESC
            ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getScheduleStatusSummary() {
        $query = $this->db->prepare(
            "SELECT
                        COUNT(*) AS total,
                        SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS pending,
                        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS delivered,
                        SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS canceled
                    FROM
                        ws_delivery_scheduling"
        );
        return $this->fetchValue($query);
    }

    public function getUpcomingSchedules($limit = 5) {
        $limit = (int) $limit;

        $query = $this->db->prepare(
            "SELECT
                        ws_delivery_scheduling.id,
                        ws_delivery_scheduling.delivery_day,
                        ws_delivery_scheduling.status,
                        users.username AS created_by,
                        COUNT(ws_deliveries.id) AS delivered_students
                    FROM
                        ws_delivery_scheduling
                    INNER JOIN
                        users
                            ON ws_delivery_scheduling.created_by = users.id
                    LEFT JOIN
                        ws_deliveries
                            ON ws_delivery_scheduling.id = ws_deliveries.delivery_scheduling_id
                    WHERE
                        ws_delivery_scheduling.delivery_day >= CURDATE()
                        AND ws_delivery_scheduling.status != 2
                    GROUP BY
                        ws_delivery_scheduling.id,
                        ws_delivery_scheduling.delivery_day,
                        ws_delivery_scheduling.status,
                        users.username
                    ORDER BY
                        ws_delivery_scheduling.delivery_day ASC
                    LIMIT {$limit}"
        );
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getDeliveriesByProgram() {
        $query = $this->db->prepare(
            "SELECT
                        ws_academic_programs.name AS academic_program,
                        COUNT(ws_deliveries.id) AS delivered
                    FROM
                        ws_deliveries
                    INNER JOIN
                        ws_students
                            ON ws_deliveries.student_id = ws_students.id
                    INNER JOIN
                        ws_academic_programs
                            ON ws_students.academic_program_id = ws_academic_programs.id
                    GROUP BY
                        ws_academic_programs.id,
                        ws_academic_programs.name
                    ORDER BY
                        delivered DESC"
        );
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getStudentsByDeliveryScheduling($id) {
        $query = $this->db->prepare(
            "SELECT 
                        s.document_number,
                        s.first_name,
                        s.last_name,
                        ap.name AS academic_program,
                        s.semester,
                        ds.delivery_day AS delivery_date
                    FROM
                        ws_deliveries d
                    INNER JOIN
                        ws_delivery_scheduling ds
                            ON d.delivery_scheduling_id = ds.id
                    INNER JOIN
                        ws_students s
                            ON d.student_id = s.id
                    INNER JOIN
                        ws_academic_programs ap
                            ON s.academic_program_id = ap.id
                    WHERE
                        delivery_scheduling_id = :id
            ");
        $query->bindParam(':id', $id);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }
}