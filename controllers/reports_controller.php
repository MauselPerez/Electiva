<?php
require_once '../models/reports.php';

class ReportsController {
    private $reportsModel;

    public function __construct() {
        $this->reportsModel = new Reports();
    }

    // Cantidad a entregar mensual
    public function getQuantityToDeliver() {
        $deliveryDays = $this->reportsModel->getDeliveryDaysPerMonth();
        $students = $this->reportsModel->getStudents();
        return $deliveryDays['delivery_days'] * $students['students'];
    }

    // Promedio de entregas al mes
    public function getAverageDelivery() {
        $data = $this->reportsModel->getDeliveriesByMonth();
        $total = 0;
        foreach ($data as $month) {
            $total += $month['delivered'];
        }
        return $total / count($data);
    }

    // Buscar todas las planificaciones de entrega
    public function getSchedules() {
        $data = $this->reportsModel->getSchedules();
        return $data;
    }

    public function getDelivered() {
        $data = $this->reportsModel->getDelivered();
        return $data['delivered'];
    }

    public function getDeliveriesByMonth() {
        $data = $this->reportsModel->getDeliveriesByMonth();
        return $data;
    }

    // Buscar estudiantes que recibieron entrega por ID de planificación
    public function getStudentsByDeliveryScheduling() {
        $id = intval($_POST['id']);
        $data = $this->reportsModel->getStudentsByDeliveryScheduling($id);
    
        if (!empty($data)) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'No se encontraron estudiantes para esta planificación.']);
        }
        exit;
    }

    // Registrar un nuevo reporte
    public function create($data) {
        // Lógica para crear un reporte
    }

    // Actualizar un reporte existente
    public function update($id, $data) {
        // Lógica para actualizar un reporte
    }

    // Eliminar un reporte
    public function delete($id) {
        // Lógica para eliminar un reporte
    }
}

//Procesar la solicitud AJAX de la vista
if (isset($_POST['action'])) {
    $reportsController = new ReportsController();
    switch ($_POST['action']) {
        case 'getStudentsByDeliveryScheduling':
            $reportsController->getStudentsByDeliveryScheduling();
            break;
        case 'create':
            $reportsController->create($_POST);
            break;
        case 'update':
            $reportsController->update($_POST['id'], $_POST);
            break;
        case 'delete':
            $reportsController->delete($_POST['id']);
            break;
        default:
            echo json_encode(['error' => 'Acción no válida']);
            http_response_code(400);
            break;
    }
}
?>
