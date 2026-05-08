<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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

    public function getById($id) {
        return $this->studentModel->getStudentById((int) $id);
    }

    private function uploadPhoto($photo, $currentPhotoPath = null) {
        if (empty($photo) || !isset($photo['error']) || $photo['error'] === UPLOAD_ERR_NO_FILE) {
            return $currentPhotoPath;
        }

        if ($photo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("No fue posible cargar la foto del estudiante.");
        }

        if ($photo['size'] > 5 * 1024 * 1024) {
            throw new Exception("La foto no puede superar los 5MB.");
        }

        $allowedMime = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $photo['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowedMime[$mimeType])) {
            throw new Exception("Formato de imagen no permitido. Use JPG, PNG o WEBP.");
        }

        $uploadDirectory = __DIR__ . '/../imgs/students';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
            throw new Exception("No fue posible crear el directorio para fotos.");
        }

        $fileName = 'student_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $allowedMime[$mimeType];
        $targetPath = $uploadDirectory . '/' . $fileName;

        if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
            throw new Exception("No fue posible guardar la foto del estudiante.");
        }

        if (!empty($currentPhotoPath)) {
            $currentAbsolutePath = __DIR__ . '/../' . ltrim($currentPhotoPath, '/');
            if (is_file($currentAbsolutePath)) {
                @unlink($currentAbsolutePath);
            }
        }

        return 'imgs/students/' . $fileName;
    }

    // Registrar un nuevo estudiante
    public function create($data, $files = []) {
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

            echo '<pre>'; print_r($files); echo '</pre>'; die();

            $photoPath = $this->uploadPhoto($files['photo'] ?? null);

            $result = $this->studentModel->createStudent([
                'program_id' => trim($data['program_id']),
                'document_number' => trim($data['document_number']),
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'email' => trim($data['email']),
                'semester' => trim($data['semester']),
                'photo_path' => $photoPath,
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
    public function update($id, $data, $files = []) {
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

            $student = $this->studentModel->getStudentById((int) $id);
            if (!$student) {
                throw new Exception("El estudiante no existe.");
            }

            $photoPath = $this->uploadPhoto($files['photo_edit'] ?? null, $student['photo_path'] ?? null);

            $data['photo_path'] = $photoPath;

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
            $student = $this->studentModel->getStudentById((int) $id);
            $result = $this->studentModel->deleteStudent($id);

            if ($result) {
                if (!empty($student['photo_path'])) {
                    $absolutePath = __DIR__ . '/../' . ltrim($student['photo_path'], '/');
                    if (is_file($absolutePath)) {
                        @unlink($absolutePath);
                    }
                }

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