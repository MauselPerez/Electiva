<?php  
    include "../app/evaluation_services.php";
    $objAPI = new EvaluationAPI();

    $method = $_SERVER['REQUEST_METHOD'];
    header("Content-Type: Application/json");
    switch ($method) {
        case 'GET':
            $query = isset($_GET['query']) ? $_GET['query'] : '';
    
            switch ($query) {
                case 'get_all_software':
                    $software = $objAPI->get_all_software();
                    echo json_encode($software);
                    break;

                case 'get_software_by_id':
                    $id = isset($_GET['id']) ? $_GET['id'] : '';
                    $software = $objAPI->get_software_by_id($id);
                    echo json_encode($software);
                    break;
    
                default:
                    http_response_code(400); // Código de respuesta HTTP 400 para solicitud incorrecta
                    echo json_encode(["error" => "Consulta no válida"]);
                    break;
            }
            break;

        case 'POST':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            if ($data === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos JSON no válidos"]);
            } 
            else 
            {
                $name = isset($data['name']) ? $data['name'] : '';
                $systems = isset($data['systems']) ? $data['systems'] : '';
                $developer = isset($data['developer']) ? $data['developer'] : '';
                $requirements = isset($data['requirements']) ? $data['requirements'] : '';
                $description = isset($data['description']) ? $data['description'] : '';
                $price = isset($data['price']) ? $data['price'] : '';


                $software = $objAPI->insert_software($name, $systems, $developer, $requirements, $description, $price);
                echo json_encode($software);
            }
            break;

        //ELIMINAR SOFTWARE
        case 'DELETE':
            $json_data = file_get_contents("php://input");
            $data = json_decode($json_data, true);

            if ($data === null) {
                http_response_code(400);
                echo json_encode(["error" => "Datos JSON no válidos"]);
            } 
            else 
            {
                $id = isset($data['id']) ? $data['id'] : '';

                $software = $objAPI->delete_software($id);
                echo json_encode($software);
            }
            break;
    
        default:
            http_response_code(405); // Código de respuesta HTTP 405 para método no permitido
            echo json_encode(["error" => "Método no permitido"]);
            break;
    }