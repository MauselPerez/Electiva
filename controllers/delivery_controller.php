<?php
require_once '../models/delivery.php';

class DeliveryController {
    private $deliveryModel;

    public function __construct() {
        $this->deliveryModel = new Delivery();
    }

    // Listar todas las entregas
    public function index() {
        return $this->deliveryModel->getAllDeliveries();
    }

    // Registrar nuevas entregas
    public function create($data) {
        try {
            if (empty($data['students']) || empty($data['delivery_scheduling_id'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $user_id = $_SESSION['user_id'];
            $result = true;

            foreach ($data['students'] as $student_id) {
                $created = $this->deliveryModel->createDelivery([
                    'student_id' => $student_id,
                    'delivery_scheduling_id' => $data['delivery_scheduling_id'],
                    'user_id' => $user_id,
                ]);

                if (!$created) {
                    $result = false;
                    break;
                }
            }

            if ($result) {
                $this->deliveryModel->updateDeliverySchedulingStatus($data['delivery_scheduling_id']);
                $_SESSION['message'] = "Entregas de merienda registradas correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al registrar las entregas de merienda.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: delivery.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al crear la entrega: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: delivery.php");
            exit;
        }
    }

    // Actualizar una entrega existente
    public function update($id, $data) {
        return $this->deliveryModel->updateDelivery($id, $data);
    }

    // Eliminar una entrega
    public function delete($id) {
        try {
            $result = $this->deliveryModel->deleteDelivery($id);

            if ($result) {
                $_SESSION['message'] = "Entrega eliminada correctamente.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error al eliminar la entrega.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: delivery.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al eliminar la entrega: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: delivery.php");
            exit;
        }
    }

    public function getAllDeliveries() {
        return $this->deliveryModel->getAllDeliveries();
    }
}
?>