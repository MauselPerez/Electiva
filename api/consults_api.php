<?php
    include "../app/consult-services.php";
    $objAPI = new ConsultasAPI();

    $method = $_SERVER['REQUEST_METHOD'];
    header("Content-Type: Application/json");
    switch ($method) {
        case 'GET':
            $query = isset($_GET['query']) ? $_GET['query'] : '';
    
            switch ($query) {
                case 'get_all_charges':
                    $charges = $objAPI->get_all_charges();
                    echo json_encode($charges);
                    break;

                case 'get_all_department':
                    $department = $objAPI->get_all_department();
                    echo json_encode($department);
                    break;
    
                default:
                    http_response_code(400); // Código de respuesta HTTP 400 para solicitud incorrecta
                    echo json_encode(["error" => "Consulta no válida"]);
                    break;
            }
            break;
    
        default:
            http_response_code(405); // Código de respuesta HTTP 405 para método no permitido
            echo json_encode(["error" => "Método no permitido"]);
            break;
    }
?>