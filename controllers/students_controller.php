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
            if (empty($data['program_id']) || empty($data['document_number']) || empty($data['first_name']) ||
                empty($data['last_name']) || empty($data['email']) || empty($data['semester'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $studentModel = new Student();
            $result = $studentModel->createStudent([
                'program_id' => $data['program_id'],
                'document_number' => $data['document_number'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'semester' => $data['semester'],
            ]);

            if ($result) {
                $_SESSION['message'] = "Estudiante registrado correctamente.";
                $_SESSION['message_type'] = "success";
            } 
            else 
            {
                $_SESSION['message'] = "Error al registrar el estudiante.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: students.php");
            exit;
        } catch (Exception $e) {
            echo "Error al crear el estudiante: " . $e->getMessage();
        }
    }

    // Actualizar un estudiante existente
    public function update($id, $data) {
        return  $this->studentModel->updateStudent($id, $data);
    }

    // Eliminar un estudiante
    public function delete($id) {
        $result = $this->studentModel->deleteStudent($id);
        if ($result) 
        {
            $_SESSION['message'] = "Estudiante eliminado correctamente.";
            $_SESSION['message_type'] = "success";
            header("Location: students.php");
            exit;
        } 
        else 
        {
            $_SESSION['message'] = "Error al eliminar el estudiante.";
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
