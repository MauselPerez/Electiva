<?php 
include "../controllers/controller_consultas_api.php";

class EmpleadosAPI {

    function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $input = json_decode(file_get_contents("php://input"), true);

            if (isset($input['action'])) 
            {
                $action = $input['action'];
                $response = $this->routeRequest($action, $input);
            } 
            else 
            {
                $response = array('success' => false, 'message' => 'Falta el parámetro de acción');
            }

            echo json_encode($response);
        } 
        else 
        {
            echo "<script>console.log('No es una solicitud POST');</script>";
        }

        
    }

    function routeRequest($action, $input) 
    {
        switch ($action) 
        {
            case 'getEmployeeByID':
                return $this->getEmployeeByID($input);
            case 'otraAccion':
                return $this->otraAccion($input);
            default:
                return array('success' => false, 'message' => 'Acción no válida');
        }
    }

    function getEmployeeByID($input) 
    {
        $idu = $input['qrCode'];
        $sql = new ExtraerDatos();
        $result = $sql->get_employee_by_id($idu);
        $response = array('success' => true, 'message' => 'Empleado encontrado', 'data' => $result);
        if (empty($result)) 
        {
            $response = array('success' => false, 'message' => 'Empleado no encontrado');
        }

        return $response;
    }

    function otraAccion($input) 
    {
        // Lógica para la acción otraAccion
        $response = array('success' => true, 'message' => 'Otra accion');
        return $response;
    }
}

// Instancia la clase y maneja la solicitud
$api = new EmpleadosAPI();
$api->handleRequest();
?>
