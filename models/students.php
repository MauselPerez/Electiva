<?php
require_once '../config/db.php';

class Student {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    private function getStudentProfileTypeId() {
        $query = $this->db->prepare("SELECT id FROM ws_profile_types WHERE name = 'STUDENT' LIMIT 1");
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("No existe el tipo de perfil STUDENT.");
        }

        return (int) $row['id'];
    }

    // Obtener todos los estudiantes
    public function getAllStudents() {
        $studentProfileTypeId = $this->getStudentProfileTypeId();

        $query = $this->db->prepare("
            SELECT
                pp.id,
                pp.person_id,
                pp.organizational_unit_id AS academic_program_id,
                pp.semester,
                pp.is_active,
                pp.photo_path,
                COALESCE(ou.name, 'Sin unidad') AS name,
                p.first_name,
                p.last_name,
                p.email,
                p.document_number
            FROM ws_person_profiles pp
            INNER JOIN ws_persons p
                ON p.id = pp.person_id
            LEFT JOIN ws_organizational_units ou
                ON ou.id = pp.organizational_unit_id
            WHERE pp.profile_type_id = :profile_type_id
            ORDER BY pp.id DESC
        ");
        $query->bindParam(':profile_type_id', $studentProfileTypeId, PDO::PARAM_INT);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getStudentById($id) {
        $studentProfileTypeId = $this->getStudentProfileTypeId();

        $query = $this->db->prepare("
            SELECT
                pp.id,
                pp.person_id,
                pp.organizational_unit_id AS academic_program_id,
                pp.semester,
                pp.is_active,
                pp.photo_path,
                COALESCE(ou.name, 'Sin unidad') AS program_name,
                p.first_name,
                p.last_name,
                p.email,
                p.document_number
            FROM ws_person_profiles pp
            INNER JOIN ws_persons p
                ON p.id = pp.person_id
            LEFT JOIN ws_organizational_units ou
                ON ou.id = pp.organizational_unit_id
            WHERE pp.id = :id
              AND pp.profile_type_id = :profile_type_id
            LIMIT 1
        ");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':profile_type_id', $studentProfileTypeId, PDO::PARAM_INT);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetch();
    }

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
              AND ou.is_active = 1
            ORDER BY ou.name ASC
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    // Crear un nuevo estudiante
    public function createStudent($data) {
        try {
            $this->db->beginTransaction();

            $studentProfileTypeId = $this->getStudentProfileTypeId();

            $checkDocument = $this->db->prepare("SELECT id FROM ws_persons WHERE document_number = :document_number");
            $checkDocument->bindParam(':document_number', $data['document_number']);
            $checkDocument->execute();

            if ($checkDocument->fetch()) {
                throw new Exception("Ya existe una persona con esa cédula.");
            }

            $checkEmail = $this->db->prepare("SELECT id FROM ws_persons WHERE email = :email");
            $checkEmail->bindParam(':email', $data['email']);
            $checkEmail->execute();

            if ($checkEmail->fetch()) {
                throw new Exception("Ya existe una persona con ese correo.");
            }

            $personQuery = "
                INSERT INTO ws_persons (
                    document_number,
                    first_name,
                    last_name,
                    email,
                    is_active
                ) VALUES (
                    :document_number,
                    :first_name,
                    :last_name,
                    :email,
                    1
                )
            ";

            $personStmt = $this->db->prepare($personQuery);
            $personStmt->bindParam(':document_number', $data['document_number']);
            $personStmt->bindParam(':first_name', $data['first_name']);
            $personStmt->bindParam(':last_name', $data['last_name']);
            $personStmt->bindParam(':email', $data['email']);
            $personStmt->execute();

            $personId = (int) $this->db->lastInsertId();

            $profileQuery = "
                INSERT INTO ws_person_profiles (
                    person_id,
                    profile_type_id,
                    organizational_unit_id,
                    semester,
                    photo_path,
                    is_active
                ) VALUES (
                    :person_id,
                    :profile_type_id,
                    :organizational_unit_id,
                    :semester,
                    :photo_path,
                    1
                )
            ";

            $profileStmt = $this->db->prepare($profileQuery);
            $profileStmt->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $profileStmt->bindParam(':profile_type_id', $studentProfileTypeId, PDO::PARAM_INT);
            $profileStmt->bindParam(':organizational_unit_id', $data['program_id'], PDO::PARAM_INT);
            $profileStmt->bindParam(':semester', $data['semester'], PDO::PARAM_INT);
            $photoPath = $data['photo_path'] ?? null;
            $profileStmt->bindParam(':photo_path', $photoPath);
            $profileStmt->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Actualizar estudiante
    public function updateStudent($id, $data) {
        try {
            $this->db->beginTransaction();

            $studentProfileTypeId = $this->getStudentProfileTypeId();

            $studentQuery = $this->db->prepare("
                SELECT person_id
                FROM ws_person_profiles
                WHERE id = :id
                  AND profile_type_id = :profile_type_id
            ");
            $studentQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $studentQuery->bindParam(':profile_type_id', $studentProfileTypeId, PDO::PARAM_INT);
            $studentQuery->execute();
            $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                throw new Exception("El estudiante no existe.");
            }

            $personId = (int) $student['person_id'];

            $checkDocument = $this->db->prepare("
                SELECT id
                FROM ws_persons
                WHERE document_number = :document_number
                  AND id <> :person_id
            ");
            $checkDocument->bindParam(':document_number', $data['document_number_edit']);
            $checkDocument->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $checkDocument->execute();

            if ($checkDocument->fetch()) {
                throw new Exception("Ya existe otra persona con esa cédula.");
            }

            $checkEmail = $this->db->prepare("
                SELECT id
                FROM ws_persons
                WHERE email = :email
                  AND id <> :person_id
            ");
            $checkEmail->bindParam(':email', $data['email_edit']);
            $checkEmail->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $checkEmail->execute();

            if ($checkEmail->fetch()) {
                throw new Exception("Ya existe otra persona con ese correo.");
            }

            $personUpdate = "
                UPDATE ws_persons
                SET
                    document_number = :document_number,
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email
                WHERE id = :person_id
            ";

            $personStmt = $this->db->prepare($personUpdate);
            $personStmt->bindParam(':document_number', $data['document_number_edit']);
            $personStmt->bindParam(':first_name', $data['first_name_edit']);
            $personStmt->bindParam(':last_name', $data['last_name_edit']);
            $personStmt->bindParam(':email', $data['email_edit']);
            $personStmt->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $personStmt->execute();

            $studentUpdate = "
                UPDATE ws_person_profiles
                SET
                    organizational_unit_id = :organizational_unit_id,
                    semester = :semester,
                    photo_path = :photo_path
                WHERE id = :id
                  AND profile_type_id = :profile_type_id
            ";

            $studentStmt = $this->db->prepare($studentUpdate);
            $studentStmt->bindParam(':organizational_unit_id', $data['program_id_edit'], PDO::PARAM_INT);
            $studentStmt->bindParam(':semester', $data['semester_edit'], PDO::PARAM_INT);
            $photoPath = $data['photo_path'] ?? null;
            $studentStmt->bindParam(':photo_path', $photoPath);
            $studentStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $studentStmt->bindParam(':profile_type_id', $studentProfileTypeId, PDO::PARAM_INT);
            $studentStmt->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Eliminar estudiante
    public function deleteStudent($id) {
        try {
            $this->db->beginTransaction();

            $studentProfileTypeId = $this->getStudentProfileTypeId();

            $studentQuery = $this->db->prepare("
                SELECT person_id
                FROM ws_person_profiles
                WHERE id = :id
                  AND profile_type_id = :profile_type_id
            ");
            $studentQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $studentQuery->bindParam(':profile_type_id', $studentProfileTypeId, PDO::PARAM_INT);
            $studentQuery->execute();
            $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                throw new Exception("El estudiante no existe.");
            }

            $personId = (int) $student['person_id'];

            $deleteProfile = $this->db->prepare("DELETE FROM ws_person_profiles WHERE id = :id");
            $deleteProfile->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteProfile->execute();

            $remainingProfiles = $this->db->prepare("SELECT COUNT(*) AS total FROM ws_person_profiles WHERE person_id = :person_id");
            $remainingProfiles->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $remainingProfiles->execute();
            $profileCount = (int) ($remainingProfiles->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $existingUser = $this->db->prepare("SELECT COUNT(*) AS total FROM users WHERE person_id = :person_id");
            $existingUser->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $existingUser->execute();
            $userCount = (int) ($existingUser->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            if ($profileCount === 0 && $userCount === 0) {
                $deletePerson = $this->db->prepare("DELETE FROM ws_persons WHERE id = :person_id");
                $deletePerson->bindParam(':person_id', $personId, PDO::PARAM_INT);
                $deletePerson->execute();
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
?>