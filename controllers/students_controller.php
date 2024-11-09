<?php
include '../public/includes/mistakes.php';
see_errors();
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}

class StudentsController {
    public function getAllStudents() {
        $url = 'http://127.0.0.1:8000/api/estudiantes';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function createStudent($student) {
        $url = 'http://127.0.0.1:8000/api/estudiante';
    
        // Verifica si existe el token de acceso en la sesión
        if (!isset($_SESSION['access_token'])) {
            $_SESSION['error'] = "No se pudo autenticar. Intente iniciar sesión nuevamente.";
            header("Location: ../templates/login.php");
            exit();
        }
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $_SESSION['access_token']  // Agregar el token al encabezado
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($student));
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error en la solicitud: ' . curl_error($ch);
        } else {
            echo 'Respuesta de la API: ' . $response;
        }
        curl_close($ch);
    
        return json_decode($response, true);
    }
    
}

// Crear una instancia del controlador
$studentsController = new StudentsController();

// Procesar el formulario de creación de estudiante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_student'])) {
    // Crear un arreglo con los datos del estudiante a enviar a la API
    $newStudent = [
        'id_programa' => $_POST['id_programa'],
        'n_documento' => $_POST['n_documento'],
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'email' => $_POST['email'],
        'telefono' => $_POST['telefono']
    ];

    // Llamar al método `createStudent` con los datos del estudiante
    $result = $studentsController->createStudent($newStudent);

    if ($result) {
        // Redirigir o mostrar un mensaje de éxito
        header("Location: ../views_admin/students.php?status=success");
        exit();
    } else {
        // Manejar el error (redireccionar o mostrar mensaje)
        header("Location: ../views_admin/students.php?status=error");
        exit();
    }
}
?>
