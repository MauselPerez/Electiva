<?php
require_once '../models/delivery.php';

class DeliveryController {
    private $deliveryModel;

    public function __construct() {
        $this->deliveryModel = new Delivery();
    }

    // Listar todos los envios
    public function index() {
        return $this->deliveryModel->getAllDeliveries();
    }

    // Registrar un nuevo envio
    public function create($data) {
        try {
            if (empty($data['students']) || empty($data['delivery_scheduling_id'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }
            $user_id = $_SESSION['user_id'];
            foreach ($data['students'] as $student_id) 
            {
                $result = $this->deliveryModel->createDelivery([
                    'student_id' => $student_id,
                    'delivery_scheduling_id' => $data['delivery_scheduling_id'],
                    'user_id' => $user_id,
                ]);
            }

            if ($result) 
            {
                $this->deliveryModel->updateDeliverySchedulingStatus($data['delivery_scheduling_id']);
                $_SESSION['message'] = "Entregas de merienda registradas correctamente.";
                $_SESSION['message_type'] = "success";
            }
            else 
            {
                $_SESSION['message'] = "Error al registrar las entregas de merienda.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: delivery.php");
            exit;
        } catch (Exception $e) {
            echo "Error al crear el envio: " . $e->getMessage();
        }
    }

    // Actualizar un envio existente
    public function update($id, $data) {
        return  $this->deliveryModel->updateDelivery($id, $data);

    }

    // Eliminar un envio
    public function delete($id) {
        $result = $this->deliveryModel->deleteDelivery($id);
        if ($result) 
        {
            $_SESSION['message'] = "Entrega eliminada correctamente.";
            $_SESSION['message_type'] = "success";
            header("Location: delivery.php");
            exit;
        } 
        else 
        {
            $_SESSION['message'] = "Error al eliminar la entrega.";
            $_SESSION['message_type'] = "danger";
            header("Location: delivery.php");
            exit;
        }
    }

    public function getAllDeliveries() {
        return $this->deliveryModel->getAllDeliveries();
    }
}