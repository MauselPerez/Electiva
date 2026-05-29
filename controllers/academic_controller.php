<?php
require_once '../models/academic_programs.php';

class AcademicProgramsController {
    private $academicProgramModel;

    public function __construct() {
        $this->academicProgramModel = new AcademicProgram();
    }

    // Listar todos los programas académicos
    public function index() {
        return $this->academicProgramModel->getAllPrograms();
    }

    public function getProgramParentUnits() {
        return $this->academicProgramModel->getProgramParentUnits();
    }

    // Registrar un nuevo programa académico
    public function create($data) {
        try {
            if (empty($data['name']) || empty($data['parent_id'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $academicProgramModel = new AcademicProgram();
            $result = $academicProgramModel->createProgram([
                'name' => $data['name'],
                'parent_id' => (int) $data['parent_id']
            ]);

            if ($result) {
                $_SESSION['message'] = "Programa académico registrado correctamente.";
                $_SESSION['message_type'] = "success";
            } 
            else 
            {
                $_SESSION['message'] = "Error al registrar el programa académico.";
                $_SESSION['message_type'] = "error";
            }

            header("Location: academic_programs.php");
            exit;
        } catch (Exception $e) {
            echo "Error al crear el programa académico: " . $e->getMessage();
        }
    }

    // Actualizar un programa académico existente
    public function update($id, $data) {
        if (empty($data['name_edit']) || empty($data['parent_id_edit'])) {
            $_SESSION['message'] = "Todos los campos son obligatorios.";
            $_SESSION['message_type'] = "error";
            header("Location: academic_programs.php");
            exit;
        }

        $payload = [
            'name_edit' => $data['name_edit'],
            'parent_id_edit' => (int) $data['parent_id_edit']
        ];

        $result = $this->academicProgramModel->updateProgram($id, $payload);
        if ($result) {
            $_SESSION['message'] = "Programa académico actualizado correctamente.";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error al actualizar el programa académico.";
            $_SESSION['message_type'] = "error";
        }

        header("Location: academic_programs.php");
        exit;
    }

    // Eliminar un programa académico
    public function delete($id) {
        $result = $this->academicProgramModel->deleteProgram($id);
        if ($result) 
        {
            $_SESSION['message'] = "Programa académico eliminado correctamente.";
            $_SESSION['message_type'] = "success";
        } 
        else 
        {
            $_SESSION['message'] = "Error al eliminar el programa académico.";
            $_SESSION['message_type'] = "error";
        }

        header("Location: academic_programs.php");
        exit;
    }
}