<?php
require_once '../models/students.php';

class StudentsController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new Student();
    }

    // Listar todos los estudiantes
    public function index() {
        return $this->studentModel->getAllStudents();
    }

    // Registrar un nuevo estudiante
    public function create($data) {
        try {
            if (
                empty($data['program_id']) ||
                empty($data['document_number']) ||
                empty($data['first_name']) ||
                empty($data['last_name']) ||
                empty($data['email']) ||
                empty($data['semester'])
            ) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $result = $this->studentModel->createStudent([
                'program_id' => trim($data['program_id']),
                'document_number' => trim($data['document_number']),
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'email' => trim($data['email']),
                'semester' => trim($data['semester']),
            ]);

            if ($result) {
                $_SESSION['message'] = "Estudiante registrado correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al registrar el estudiante.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: students.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al crear el estudiante: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: students.php");
            exit;
        }
    }

    // Actualizar un estudiante existente
    public function update($id, $data) {
        try {
            if (
                empty($id) ||
                empty($data['program_id_edit']) ||
                empty($data['document_number_edit']) ||
                empty($data['first_name_edit']) ||
                empty($data['last_name_edit']) ||
                empty($data['email_edit']) ||
                empty($data['semester_edit'])
            ) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $result = $this->studentModel->updateStudent($id, $data);

            if ($result) {
                $_SESSION['message'] = "Estudiante actualizado correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al actualizar el estudiante.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: students.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al actualizar el estudiante: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: students.php");
            exit;
        }
    }

    // Eliminar un estudiante
    public function delete($id) {
        try {
            $result = $this->studentModel->deleteStudent($id);

            if ($result) {
                $_SESSION['message'] = "Estudiante eliminado correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al eliminar el estudiante.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: students.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al eliminar el estudiante: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: students.php");
            exit;
        }
    }

    public function getAllPrograms() {
        return $this->studentModel->getAllPrograms();
    }
}
?>