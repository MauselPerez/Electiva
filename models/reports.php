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
            FROM ws_person_profiles pp
            INNER JOIN ws_profile_types pt
                ON pt.id = pp.profile_type_id
            WHERE pt.name = 'STUDENT'
              AND pp.is_active = 1"
        );
        return $this->fetchValue($query);
    }

    public function getStudentsWithoutDeliveries() {
        $query = $this->db->prepare(
            "SELECT
                        COUNT(*) AS no_deliveries
                    FROM
                        ws_person_profiles pp
                    INNER JOIN ws_profile_types pt
                        ON pt.id = pp.profile_type_id
                    WHERE
                        pp.is_active = 1
                        AND pt.name = 'STUDENT'
                        AND NOT EXISTS (
                            SELECT 1
                            FROM ws_deliveries d
                            INNER JOIN ws_delivery_scheduling ds
                                ON ds.id = d.delivery_scheduling_id
                            WHERE d.person_profile_id = pp.id
                              AND MONTH(ds.delivery_day) = MONTH(CURDATE())
                              AND YEAR(ds.delivery_day) = YEAR(CURDATE())
                              AND ds.status != 2
                        )"
        );
        return (int) ($this->fetchValue($query)['no_deliveries'] ?? 0);
    }

    public function getStudentsMissingDeliveries() {
        $query = $this->db->prepare(
            "SELECT
                        p.document_number,
                        p.first_name,
                        p.last_name,
                        COALESCE(ou.name, 'Sin unidad') AS academic_program,
                        pp.semester,
                        COUNT(d.id) AS delivered_count,
                        (SELECT COUNT(*) FROM ws_delivery_scheduling WHERE MONTH(delivery_day) = MONTH(CURDATE()) AND YEAR(delivery_day) = YEAR(CURDATE()) AND status != 2) - COUNT(d.id) AS missing_deliveries
                    FROM
                        ws_person_profiles pp
                    INNER JOIN ws_profile_types pt
                        ON pt.id = pp.profile_type_id
                    INNER JOIN ws_persons p
                        ON p.id = pp.person_id
                    LEFT JOIN ws_organizational_units ou
                        ON ou.id = pp.organizational_unit_id
                    LEFT JOIN ws_deliveries d
                        ON d.person_profile_id = pp.id
                    LEFT JOIN ws_delivery_scheduling ds
                        ON ds.id = d.delivery_scheduling_id
                        AND MONTH(ds.delivery_day) = MONTH(CURDATE())
                        AND YEAR(ds.delivery_day) = YEAR(CURDATE())
                        AND ds.status != 2
                    WHERE
                        pp.is_active = 1
                        AND pt.name = 'STUDENT'
                    GROUP BY
                        pp.id,
                        p.document_number,
                        p.first_name,
                        p.last_name,
                        ou.name,
                        pp.semester
                    HAVING
                        missing_deliveries > 0
                    ORDER BY
                        missing_deliveries DESC,
                        p.last_name ASC,
                        p.first_name ASC"
        );
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
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
                        COALESCE(ou.name, 'Sin unidad') AS academic_program,
                        COUNT(ws_deliveries.id) AS delivered
                    FROM
                        ws_deliveries
                    INNER JOIN ws_person_profiles pp
                        ON ws_deliveries.person_profile_id = pp.id
                    LEFT JOIN ws_organizational_units ou
                        ON pp.organizational_unit_id = ou.id
                    GROUP BY
                        ou.id,
                        ou.name
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
                        p.document_number,
                        p.first_name,
                        p.last_name,
                        COALESCE(ou.name, 'Sin unidad') AS academic_program,
                        pp.semester,
                        ds.delivery_day AS delivery_date
                    FROM
                        ws_deliveries d
                    INNER JOIN
                        ws_delivery_scheduling ds
                            ON d.delivery_scheduling_id = ds.id
                    INNER JOIN
                        ws_person_profiles pp
                            ON d.person_profile_id = pp.id
                    INNER JOIN ws_persons p
                        ON p.id = pp.person_id
                    LEFT JOIN ws_organizational_units ou
                        ON ou.id = pp.organizational_unit_id
                    WHERE
                        delivery_scheduling_id = :id
            ");
        $query->bindParam(':id', $id);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }
}