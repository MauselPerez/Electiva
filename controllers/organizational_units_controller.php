<?php
require_once '../models/organizational_units.php';

class OrganizationalUnitsController {
    private $unitModel;

    public function __construct() {
        $this->unitModel = new OrganizationalUnit();
    }

    public function index() {
        return $this->unitModel->getAllUnits();
    }

    public function getAllUnitTypes() {
        return $this->unitModel->getAllUnitTypes();
    }

    public function getParentCandidates($excludeId = null) {
        return $this->unitModel->getParentCandidates($excludeId);
    }

    public function create($data) {
        try {
            if (empty($data['name']) || empty($data['organizational_unit_type_id'])) {
                throw new Exception('Todos los campos obligatorios deben completarse.');
            }

            $result = $this->unitModel->createUnit([
                'parent_id' => $data['parent_id'] ?? null,
                'organizational_unit_type_id' => (int) $data['organizational_unit_type_id'],
                'name' => trim($data['name']),
                'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            ]);

            $_SESSION['message'] = $result
                ? 'Unidad organizacional registrada correctamente.'
                : 'No fue posible registrar la unidad organizacional.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';

            header('Location: organizational_units.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al crear la unidad organizacional: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: organizational_units.php');
            exit;
        }
    }

    public function update($id, $data) {
        try {
            if (empty($id) || empty($data['name']) || empty($data['organizational_unit_type_id'])) {
                throw new Exception('Todos los campos obligatorios deben completarse.');
            }

            $result = $this->unitModel->updateUnit((int) $id, [
                'parent_id' => $data['parent_id'] ?? null,
                'organizational_unit_type_id' => (int) $data['organizational_unit_type_id'],
                'name' => trim($data['name']),
                'is_active' => isset($data['is_active']) ? (int) $data['is_active'] : 1,
            ]);

            $_SESSION['message'] = $result
                ? 'Unidad organizacional actualizada correctamente.'
                : 'No fue posible actualizar la unidad organizacional.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';

            header('Location: organizational_units.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al actualizar la unidad organizacional: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: organizational_units.php');
            exit;
        }
    }

    public function delete($id) {
        try {
            $result = $this->unitModel->deleteUnit((int) $id);
            $_SESSION['message'] = $result
                ? 'Unidad organizacional desactivada correctamente.'
                : 'No fue posible desactivar la unidad organizacional.';
            $_SESSION['message_type'] = $result ? 'success' : 'error';

            header('Location: organizational_units.php');
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = 'Error al desactivar la unidad organizacional: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header('Location: organizational_units.php');
            exit;
        }
    }
}
?>