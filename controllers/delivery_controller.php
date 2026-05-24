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
            $_SESSION['message_type'] = "error";
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
                $_SESSION['message_type'] = "error";
            }

            header("Location: delivery.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['message'] = "Error al eliminar la entrega: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
            header("Location: delivery.php");
            exit;
        }
    }

    public function getAllDeliveries() {
        return $this->deliveryModel->getAllDeliveries();
    }

    private function parseQrDocumentNumber($qrCode) {
        $qrCode = trim((string) $qrCode);
        if ($qrCode === '') {
            throw new Exception('Debe escanear un código QR válido.');
        }

        if (stripos($qrCode, 'ID_') !== 0) {
            throw new Exception('Formato QR inválido. Se espera ID_NUMERO_DE_CEDULA.');
        }

        $documentNumber = substr($qrCode, 3);
        if (!preg_match('/^\d+$/', $documentNumber)) {
            throw new Exception('El código QR no contiene una cédula válida.');
        }

        return $documentNumber;
    }

    public function getStudentByQrForDelivery($qrCode, $scheduleId) {
        $documentNumber = $this->parseQrDocumentNumber($qrCode);
        $scheduleId = (int) $scheduleId;

        if ($scheduleId <= 0) {
            throw new Exception('Seleccione una jornada pendiente antes de escanear.');
        }

        $schedule = $this->deliveryModel->getDeliveryScheduleOverviewById($scheduleId);
        if (!$schedule) {
            throw new Exception('La jornada seleccionada no existe.');
        }

        if ((int) $schedule['status'] !== 0) {
            throw new Exception('La jornada ya no está pendiente. Seleccione otra para registrar entregas.');
        }

        $student = $this->deliveryModel->findStudentByDocumentNumber($documentNumber);
        if (!$student) {
            throw new Exception('No existe un estudiante asociado a esa cédula.');
        }

        if ((int) $student['is_active'] !== 1) {
            throw new Exception('El estudiante está inactivo y no puede recibir entrega.');
        }

        $alreadyDelivered = $this->deliveryModel->hasDeliveryForSchedule((int) $student['id'], $scheduleId);

        return [
            'student' => $student,
            'schedule' => $schedule,
            'already_delivered' => $alreadyDelivered
        ];
    }

    public function registerDeliveryByQr($qrCode, $scheduleId) {
        $data = $this->getStudentByQrForDelivery($qrCode, $scheduleId);

        if ($data['already_delivered']) {
            throw new Exception('Este estudiante ya tiene entrega registrada en la jornada seleccionada.');
        }

        $studentId = (int) $data['student']['id'];
        $scheduleId = (int) $scheduleId;
        $userId = (int) $_SESSION['user_id'];

        $created = $this->deliveryModel->createDelivery([
            'student_id' => $studentId,
            'delivery_scheduling_id' => $scheduleId,
            'user_id' => $userId,
        ]);

        if (!$created) {
            throw new Exception('No fue posible registrar la entrega por QR.');
        }

        return $data;
    }

    public function completeSchedule($id) {
        $schedule = $this->deliveryModel->getDeliveryScheduleOverviewById((int) $id);

        if (!$schedule) {
            $_SESSION['message'] = "La jornada seleccionada no existe.";
            $_SESSION['message_type'] = "error";
            return;
        }

        if ((int) $schedule['status'] === 2) {
            $_SESSION['message'] = "No se puede ejecutar una jornada anulada.";
            $_SESSION['message_type'] = "error";
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

        if ($this->deliveryModel->hasPendingSchedulesBefore((int) $id)) {
            $_SESSION['message'] = "No puedes marcar esta jornada como ejecutada mientras haya jornadas anteriores pendientes. Ejecuta primero los días anteriores.";
            $_SESSION['message_type'] = "warning";
            return;
        }

        $result = $this->deliveryModel->updateDeliverySchedulingStatus((int) $id, 1);
        $_SESSION['message'] = $result
            ? "Jornada marcada como ejecutada correctamente."
            : "No fue posible marcar la jornada como ejecutada.";
        $_SESSION['message_type'] = $result ? "success" : "error";
    }

    public function reopenSchedule($id) {
        $schedule = $this->deliveryModel->getDeliveryScheduleOverviewById((int) $id);

        if (!$schedule) {
            $_SESSION['message'] = "La jornada seleccionada no existe.";
            $_SESSION['message_type'] = "error";
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
        $_SESSION['message_type'] = $result ? "success" : "error";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');

    try {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Su sesión ha expirado. Inicie sesión nuevamente.');
        }

        $controller = new DeliveryController();
        $action = $_POST['action'];

        switch ($action) {
            case 'getStudentByQrForDelivery':
                $response = $controller->getStudentByQrForDelivery(
                    $_POST['qr_code'] ?? '',
                    $_POST['delivery_scheduling_id'] ?? 0
                );
                echo json_encode(['ok' => true] + $response);
                break;

            case 'registerDeliveryByQr':
                $response = $controller->registerDeliveryByQr(
                    $_POST['qr_code'] ?? '',
                    $_POST['delivery_scheduling_id'] ?? 0
                );
                echo json_encode(['ok' => true] + $response);
                break;

            default:
                throw new Exception('Acción no válida.');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'ok' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}
?>