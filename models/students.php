<?php
require_once '../config/db.php';

class Student {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    // Obtener todos los estudiantes
    public function getAllStudents() {
        $query = $this->db->prepare("
            SELECT 
                s.id,
                s.person_id,
                s.academic_program_id,
                s.semester,
                s.is_active,
                s.photo_path,
                ap.name AS name,
                p.first_name,
                p.last_name,
                p.email,
                p.document_number
            FROM ws_students s
            INNER JOIN ws_academic_programs ap
                ON s.academic_program_id = ap.id
            INNER JOIN ws_persons p
                ON p.id = s.person_id
            ORDER BY s.id DESC
        ");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    public function getStudentById($id) {
        $query = $this->db->prepare("
            SELECT
                s.id,
                s.person_id,
                s.academic_program_id,
                s.semester,
                s.is_active,
                s.photo_path,
                ap.name AS program_name,
                p.first_name,
                p.last_name,
                p.email,
                p.document_number
            FROM ws_students s
            INNER JOIN ws_academic_programs ap
                ON s.academic_program_id = ap.id
            INNER JOIN ws_persons p
                ON p.id = s.person_id
            WHERE s.id = :id
            LIMIT 1
        ");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetch();
    }

    public function getAllPrograms() {
        $query = $this->db->prepare("SELECT * FROM ws_academic_programs WHERE is_active = 1 ORDER BY name ASC");
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }

    // Crear un nuevo estudiante
    public function createStudent($data) {
        try {
            $this->db->beginTransaction();

            // Validar documento duplicado
            $checkDocument = $this->db->prepare("
                SELECT id FROM ws_persons WHERE document_number = :document_number
            ");
            $checkDocument->bindParam(':document_number', $data['document_number']);
            $checkDocument->execute();

            if ($checkDocument->fetch()) {
                throw new Exception("Ya existe una persona con esa cédula.");
            }

            // Validar email duplicado
            $checkEmail = $this->db->prepare("
                SELECT id FROM ws_persons WHERE email = :email
            ");
            $checkEmail->bindParam(':email', $data['email']);
            $checkEmail->execute();

            if ($checkEmail->fetch()) {
                throw new Exception("Ya existe una persona con ese correo.");
            }

            // Insertar en ws_persons
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

            $personId = $this->db->lastInsertId();

            // Insertar en ws_students
            $studentQuery = "
                INSERT INTO ws_students (
                    person_id,
                    academic_program_id,
                    semester,
                    photo_path,
                    is_active
                ) VALUES (
                    :person_id,
                    :program_id,
                    :semester,
                    :photo_path,
                    1
                )
            ";

            $studentStmt = $this->db->prepare($studentQuery);
            $studentStmt->bindParam(':person_id', $personId);
            $studentStmt->bindParam(':program_id', $data['program_id']);
            $studentStmt->bindParam(':semester', $data['semester']);
            $photoPath = $data['photo_path'] ?? null;
            $studentStmt->bindParam(':photo_path', $photoPath);
            $studentStmt->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Actualizar estudiante
    public function updateStudent($id, $data) {
        try {
            $this->db->beginTransaction();

            // Obtener el person_id del estudiante
            $studentQuery = $this->db->prepare("
                SELECT person_id
                FROM ws_students
                WHERE id = :id
            ");
            $studentQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $studentQuery->execute();
            $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                throw new Exception("El estudiante no existe.");
            }

            $personId = $student['person_id'];

            // Validar cédula duplicada en otra persona
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

            // Validar email duplicado en otra persona
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

            // Actualizar persona
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

            // Actualizar estudiante
            $studentUpdate = "
                UPDATE ws_students
                SET
                    academic_program_id = :program_id,
                    semester = :semester,
                    photo_path = :photo_path
                WHERE id = :id
            ";

            $studentStmt = $this->db->prepare($studentUpdate);
            $studentStmt->bindParam(':program_id', $data['program_id_edit']);
            $studentStmt->bindParam(':semester', $data['semester_edit']);
            $photoPath = $data['photo_path'] ?? null;
            $studentStmt->bindParam(':photo_path', $photoPath);
            $studentStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $studentStmt->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Eliminar estudiante
    public function deleteStudent($id) {
        try {
            $this->db->beginTransaction();

            // Obtener person_id
            $studentQuery = $this->db->prepare("
                SELECT person_id
                FROM ws_students
                WHERE id = :id
            ");
            $studentQuery->bindParam(':id', $id, PDO::PARAM_INT);
            $studentQuery->execute();
            $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

            if (!$student) {
                throw new Exception("El estudiante no existe.");
            }

            $personId = $student['person_id'];

            // Eliminar estudiante
            $deleteStudent = $this->db->prepare("
                DELETE FROM ws_students
                WHERE id = :id
            ");
            $deleteStudent->bindParam(':id', $id, PDO::PARAM_INT);
            $deleteStudent->execute();

            // Eliminar persona
            $deletePerson = $this->db->prepare("
                DELETE FROM ws_persons
                WHERE id = :person_id
            ");
            $deletePerson->bindParam(':person_id', $personId, PDO::PARAM_INT);
            $deletePerson->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>