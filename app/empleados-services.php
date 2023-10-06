<?php 
    include "../controllers/controller_consultas_api.php";


    class empleadosAPI{

        function getAllEmpleados(){
            $objDB = new ExtraerDatos();
            $data = array();

            if (isset($_GET["id"]))
            {
                $data = $objDB->empleadosDetalle($_GET["id"]);
            }
            else
            {
                $data = $objDB->listadoEmpleados();
            }

            $empleados = array();
            $empleados["data"] = array();

            if($data)
            {
                foreach($data as $row)
                {
                    $item = array(
                        "code" => $row["id"],
                        "citizen_card" => $row["document_number"],                    
                        "name" => $row["first_name"],
                        "last_name" => $row["last_name"],
                        "cell_phone" => $row["phone"],
                        "year_old" => $row["age"],
                        "mail" => $row["email"],
                        "photo" => $row["img"],
                        "date_admission" => $row["admission_date"],
                        "date_discharged" => $row["discharged_date"]
                    );
                    array_push($empleados["data"], $item);                
                }
                $empleados["msg"] = "OK";
                $empleados["error"] = "0";
                echo '<pre>'; echo json_encode($empleados, JSON_PRETTY_PRINT); echo '</pre>';
                
            }
            else
            {
                echo json_encode(array("data"=>null, "error"=>"1", "msg"=>"NO hay datos de empleados", ));
            }
        }

        function saveEmpleado()
        {
            echo json_encode(array("data"=>null, "error"=>"0", "msg"=>"Guardar", ));
        }

        function updateEmpleado()
        {
            echo json_encode(array("data"=>null, "error"=>"0", "msg"=>"Actualizar", ));
        }

        function deleteEmpleado()
        {
            echo json_encode(array("data"=>null, "error"=>"0", "msg"=>"Eliminar", ));
        }

        function nullRequest()
        {
            echo json_encode(array("data"=>null, "error"=>"0", "msg"=>"Solicitud Nula", ));
        }
    }

?>

