<?php
require_once '../models/user.php';

class UsersController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Listar todos los usuarios
    public function index() {
        return $this->userModel->getAllUsers();
    }

    // Registrar un nuevo usuario
    public function create($data) {
        try {
            if (empty($data['username']) || empty($data['password']) || empty($data['document_number']) || empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $userModel = new User();
            $result = $userModel->createUser([
                'username' => $data['username'],
                'password' => $data['password'],
                'document_number' => $data['document_number'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email']
            ]);

            if ($result) {
                $_SESSION['message'] = "Usuario registrado correctamente.";
                $_SESSION['message_type'] = "success";
            } 
            else 
            {
                $_SESSION['message'] = "Error al registrar el usuario.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: users.php");
            exit;
        } catch (Exception $e) {
            echo "Error al crear el usuario: " . $e->getMessage();
        }
    }

    // Actualizar un usuario existente
    public function update($id, $data) {
        $result =  $this->userModel->updateUser($id, $data);

        if ($result) 
        {
            $_SESSION['message'] = "Usuario actualizado correctamente.";
            $_SESSION['message_type'] = "success";
        } 
        else 
        {
            $_SESSION['message'] = "Error al actualizar el usuario.";
            $_SESSION['message_type'] = "danger";
        }

        header("Location: users.php");
        exit;
    }

    // Eliminar un usuario
    public function delete($id) {
        $result = $this->userModel->deleteUser($id);
        if ($result) 
        {
            $_SESSION['message'] = "Usuario eliminado correctamente.";
            $_SESSION['message_type'] = "success";
        } 
        else 
        {
            $_SESSION['message'] = "Error al eliminar el usuario.";
            $_SESSION['message_type'] = "danger";
        }

        header("Location: users.php");
        exit;
    }
}