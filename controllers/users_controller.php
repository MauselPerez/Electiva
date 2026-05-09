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
            if (
                empty($data['username']) ||
                empty($data['password']) ||
                empty($data['document_number']) ||
                empty($data['first_name']) ||
                empty($data['last_name']) ||
                empty($data['email']) ||
                empty($data['role_id'])
            ) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $result = $this->userModel->createUser([
                'username' => trim($data['username']),
                'password' => $data['password'],
                'document_number' => trim($data['document_number']),
                'first_name' => trim($data['first_name']),
                'last_name' => trim($data['last_name']),
                'email' => trim($data['email']),
                'role_id' => trim($data['role_id'])
            ]);

            if ($result) {
                $_SESSION['message'] = "Usuario registrado correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al registrar el usuario.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: users.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al crear el usuario: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: users.php");
            exit;
        }
    }

    // Actualizar un usuario existente
    public function update($id, $data) {
        try {
            if (
                empty($id) ||
                empty($data['username']) ||
                empty($data['document_number']) ||
                empty($data['first_name']) ||
                empty($data['last_name']) ||
                empty($data['email']) ||
                empty($data['role_id'])
            ) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $result = $this->userModel->updateUser($id, $data);

            if ($result) {
                $_SESSION['message'] = "Usuario actualizado correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al actualizar el usuario.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: users.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al actualizar el usuario: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: users.php");
            exit;
        }
    }

    // Eliminar un usuario
    public function delete($id) {
        try {
            $result = $this->userModel->deleteUser($id);

            if ($result) {
                $_SESSION['message'] = "Usuario eliminado correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al eliminar el usuario.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: users.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al eliminar el usuario: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: users.php");
            exit;
        }
    }

    public function getAllRoles() {
        return $this->userModel->getAllRoles();
    }
}
?>