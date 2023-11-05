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
            case 'getAllEmployees':
                return $this->getAllEmployees($input);
            case 'otraAccion':
                return $this->otraAccion($input);
            default:
                return array('success' => false, 'message' => 'Acción no válida');
        }
    }

    function getAllEmployees($input) 
    {
        // Lógica para la acción getAllEmployees
        $response = array('success' => true, 'message' => 'Funciona para la consulta');
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
