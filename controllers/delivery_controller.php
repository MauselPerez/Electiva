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

    public function getScheduleOverview() {
        return $this->deliveryModel->getDeliveryScheduleOverview();
    }

    // Registrar nuevas entregas
    public function create($data) {
        try {
            if (empty($data['students']) || empty($data['delivery_scheduling_id'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $user_id = $_SESSION['user_id'];
            $createdCount = $this->deliveryModel->createDeliveriesBatch(
                $data['students'],
                $data['delivery_scheduling_id'],
                $user_id
            );

            $_SESSION['message'] = "Se registraron {$createdCount} entregas correctamente. Cuando finalice la jornada, márquela como ejecutada.";
            $_SESSION['message_type'] = "success";

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

    public function completeSchedule($id) {
        $schedule = $this->deliveryModel->getDeliveryScheduleOverviewById((int) $id);

        if (!$schedule) {
            $_SESSION['message'] = "La jornada seleccionada no existe.";
            $_SESSION['message_type'] = "danger";
            return;
        }

        if ((int) $schedule['status'] === 2) {
            $_SESSION['message'] = "No se puede ejecutar una jornada anulada.";
            $_SESSION['message_type'] = "danger";
            return;
        }

        if ((int) $schedule['status'] === 1) {
            $_SESSION['message'] = "La jornada ya estaba marcada como ejecutada.";
            $_SESSION['message_type'] = "info";
            return;
        }

        if ((int) $schedule['delivered_students'] === 0) {
            $_SESSION['message'] = "Registra al menos una entrega antes de marcar la jornada como ejecutada.";
            $_SESSION['message_type'] = "warning";
            return;
        }

        $result = $this->deliveryModel->updateDeliverySchedulingStatus((int) $id, 1);
        $_SESSION['message'] = $result
            ? "Jornada marcada como ejecutada correctamente."
            : "No fue posible marcar la jornada como ejecutada.";
        $_SESSION['message_type'] = $result ? "success" : "danger";
    }

    public function reopenSchedule($id) {
        $schedule = $this->deliveryModel->getDeliveryScheduleOverviewById((int) $id);

        if (!$schedule) {
            $_SESSION['message'] = "La jornada seleccionada no existe.";
            $_SESSION['message_type'] = "danger";
            return;
        }

        if ((int) $schedule['status'] !== 1) {
            $_SESSION['message'] = "Solo las jornadas ejecutadas pueden reabrirse.";
            $_SESSION['message_type'] = "warning";
            return;
        }

        $result = $this->deliveryModel->updateDeliverySchedulingStatus((int) $id, 0);
        $_SESSION['message'] = $result
            ? "Jornada reabierta correctamente."
            : "No fue posible reabrir la jornada.";
        $_SESSION['message_type'] = $result ? "success" : "danger";
    }
}
?>