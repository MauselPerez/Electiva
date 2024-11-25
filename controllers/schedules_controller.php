<?php
require_once '../models/schedules.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}
class SchedulesController {
    private $scheduleModel;

    public function __construct() {
        $this->scheduleModel = new Schedule();
    }

    // Listar todas las planificaciones
    public function index() {
        return $this->scheduleModel->getAllSchedules();
    }

    //Traer todas las planificaciones exepto las canceladas
    public function indexNotCanceled() {
        return $this->scheduleModel->getAllSchedulesNotCanceled();
    }

    // Registrar una nueva planificación
    public function create($data) {
        try {
            if (empty($data['start_date']) || empty($data['end_date'])) 
            {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $start_date = new DateTime($data['start_date']);
            $end_date = new DateTime($data['end_date']);
            $end_date = $end_date->modify('+1 day');
            $interval = new DateInterval('P1D');
            $daterange = new DatePeriod($start_date, $interval, $end_date);
            $user_id = $_SESSION['user_id'];
            
            $days = [];
            foreach ($daterange as $date) 
            {
                $day = $date->format("N");
                if (!($day != 5 && $day != 6)) 
                {
                    //$days[] = $date->format("Y-m-d");
                    $date = $date->format("Y-m-d");
                    $result = $this->scheduleModel->createSchedule([
                        'delivery_day' => $date,
                        'created_by' => $user_id
                    ]);
                }
            }

            if ($result) 
            {
                $_SESSION['message'] = "Planificación creada correctamente.";
                $_SESSION['message_type'] = "success";
            }
            else 
            {
                $_SESSION['message'] = "Error al crear la planificación.";
                $_SESSION['message_type'] = "danger";
            }

            header("Location: schedules.php");
            exit;
        } catch (Exception $e) {
            echo "Error al crear la planificación: " . $e->getMessage();
        }
    }

    // Actualizar una planificación existente
    public function update($id, $data) {
        return  $this->scheduleModel->updateSchedule($id, $data);
    }

    // Eliminar una planificación
    public function delete($id) {
        $result = $this->scheduleModel->deleteSchedule($id);
        if ($result) 
        {
            $_SESSION['message'] = "Programación eliminada correctamente.";
            $_SESSION['message_type'] = "success";
            header("Location: schedules.php");
            exit;
        } else 
        {
            $_SESSION['message'] = "Error al eliminar la programación.";
            $_SESSION['message_type'] = "danger";
            header("Location: schedules.php");
            exit;
        }
    }

    //Cancelar una planificación
    public function cancel($id) {
        $result = $this->scheduleModel->cancelSchedule($id);
        if ($result) 
        {
            $_SESSION['message'] = "Programación cancelada correctamente.";
            $_SESSION['message_type'] = "success";
            header("Location: schedules.php");
            exit;
        } else 
        {
            $_SESSION['message'] = "Error al cancelar la programación.";
            $_SESSION['message_type'] = "danger";
            header("Location: schedules.php");
            exit;
        }
    }
}