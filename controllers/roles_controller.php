<?php
require_once '../models/roles.php';

class RolesController {
    private $roleModel;

    public function __construct() {
        $this->roleModel = new Role();
    }

    public function index() {
        return $this->roleModel->getAllRoles();
    }

    public function create($data) {
        try {
            if (empty($data['name'])) {
                throw new Exception('El nombre del rol es obligatorio.');
            }

            $result = $this->roleModel->createRole(trim($data['name']));
            $_SESSION['message'] = $result ? 'Rol registrado correctamente.' : 'No fue posible registrar el rol.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al crear el rol: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }

        header('Location: roles.php');
        exit;
    }

    public function update($id, $data) {
        try {
            if (empty($id) || empty($data['name'])) {
                throw new Exception('Todos los campos obligatorios deben completarse.');
            }

            $result = $this->roleModel->updateRole((int) $id, trim($data['name']), (int) ($data['is_active'] ?? 1));
            $_SESSION['message'] = $result ? 'Rol actualizado correctamente.' : 'No fue posible actualizar el rol.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al actualizar el rol: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }

        header('Location: roles.php');
        exit;
    }

    public function delete($id) {
        try {
            $result = $this->roleModel->disableRole((int) $id);
            $_SESSION['message'] = $result ? 'Rol desactivado correctamente.' : 'No fue posible desactivar el rol.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al desactivar el rol: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }

        header('Location: roles.php');
        exit;
    }
}
?>