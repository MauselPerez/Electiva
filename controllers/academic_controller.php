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

    // Registrar un nuevo programa académico
    public function create($data) {
        try {
            if (empty($data['name'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $academicProgramModel = new AcademicProgram();
            $result = $academicProgramModel->createProgram([
                'name' => $data['name']
            ]);

            if ($result) {
                $_SESSION['message'] = "Programa académico registrado correctamente.";
                $_SESSION['message_type'] = "success";
            } 
            else 
            {
                $_SESSION['message'] = "Error al registrar el programa académico.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: academic_programs.php");
            exit;
        } catch (Exception $e) {
            echo "Error al crear el programa académico: " . $e->getMessage();
        }
    }

    // Actualizar un programa académico existente
    public function update($id, $data) {
        return  $this->academicProgramModel->updateProgram($id, $data);
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
            $_SESSION['message_type'] = "danger";
        }

        header("Location: academic_programs.php");
        exit;
    }
}