<?php
    include "../app/empleados-services.php";
    $objAPI = new EmpleadosAPI();

    $method = $_SERVER['REQUEST_METHOD'];
    header("Content-Type: Application/json");
    switch ($method) {
        case 'POST':
            $objAPI->handleRequest();
            break;
            
        default:
            http_response_code(405); // Código de respuesta HTTP 405 para método no permitido
            echo json_encode(["error" => "Método no permitido"]);
            break;
    }
?>