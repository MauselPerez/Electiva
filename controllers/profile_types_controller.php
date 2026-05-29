<?php
require_once '../models/profile_types.php';

class ProfileTypesController {
    private $profileTypeModel;

    public function __construct() {
        $this->profileTypeModel = new ProfileType();
    }

    public function index() {
        return $this->profileTypeModel->getAllProfileTypes();
    }

    public function create($data) {
        try {
            if (empty($data['name'])) {
                throw new Exception('El nombre del tipo de perfil es obligatorio.');
            }

            $result = $this->profileTypeModel->createProfileType([
                'name' => trim($data['name']),
                'description' => trim($data['description'] ?? ''),
            ]);

            $_SESSION['message'] = $result ? 'Tipo de perfil registrado correctamente.' : 'No fue posible registrar el tipo de perfil.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al crear el tipo de perfil: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }

        header('Location: profile_types.php');
        exit;
    }

    public function update($id, $data) {
        try {
            if (empty($id) || empty($data['name'])) {
                throw new Exception('Todos los campos obligatorios deben completarse.');
            }

            $result = $this->profileTypeModel->updateProfileType((int) $id, [
                'name' => trim($data['name']),
                'description' => trim($data['description'] ?? ''),
                'is_active' => (int) ($data['is_active'] ?? 1),
            ]);

            $_SESSION['message'] = $result ? 'Tipo de perfil actualizado correctamente.' : 'No fue posible actualizar el tipo de perfil.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al actualizar el tipo de perfil: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }

        header('Location: profile_types.php');
        exit;
    }

    public function delete($id) {
        try {
            $result = $this->profileTypeModel->disableProfileType((int) $id);
            $_SESSION['message'] = $result ? 'Tipo de perfil desactivado correctamente.' : 'No fue posible desactivar el tipo de perfil.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al desactivar el tipo de perfil: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }

        header('Location: profile_types.php');
        exit;
    }
}
?>