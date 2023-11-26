<?php 
include "../controllers/controller_consultas_api.php";

class EmpleadosAPI {

    function handleRequest() 
    {
        echo '<pre>'; print_r($_SERVER['REQUEST_METHOD']); echo '</pre>'; die;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $input = json_decode(file_get_contents("php://input"), true);
            echo '<pre>'; var_dump($input); echo '</pre>'; die;
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
            case 'InsertMarking':
                return $this->InsertMarking($input);
            case 'InsertEmployees':
                return $this->insert_user($input);
        }
    }

    function getEmployeeByID($input) 
    {
        $idu = $input['qrCode'];
        $sql = new ExtraerDatos();
        $result = $sql->get_employee_by_id($idu);

        if (empty($result)) 
        {
            $response = array('success' => false, 'message' => 'Empleado no encontrado');
        }
        else 
        {
            $response = array('success' => true, 'message' => 'Empleado encontrado', 'data' => $result);
        }

        return $response;
    }

    function InsertMarking($input) 
    {
        $user_id = $input['user_id'];
        $sql = new ExtraerDatos();
        $result = $sql->insert_entry($user_id);

        if (empty($result)) 
        {
            $response = array('success' => false, 'message' => 'No se inserto el registro de marcaciones');
        }
        else 
        {
            $response = array('success' => true, 'message' => 'Registrada la marcación correctamente', 'data' => $result);
        }

        return $response;
    }

    function get_all_department(){
        $Objt= new ExtraerDatos();
        $data = [];
        $department = [];
        $data = $Objt->get_all_department();
        
        foreach ($data as $d) {
            $item = array(
                "cod" => $d['id'],
                "nombre" => $d['name']
            );
            array_push($department, $item);
        }
         return ($department);
    }


    function get_all_charges(){
        $Objt= new ExtraerDatos();
        $data = [];
        $charges = [];
        $data = $Objt->get_all_charges();
        
        foreach ($data as $d) {
            $item = array(
                "cod" => $d['id'],
                "nombre" => $d['name']
            );
            array_push($charges, $item);
        }
         return ($charges);
    }

    function insert_user($user){
        echo '<pre>'; print_r($user); echo '</pre>'; die;
        $Objt= new ExtraerDatos();
        $data = $Objt->insert_user();  
         return ($data);
    }

    function insert_employees(){
        $Objt= new ExtraerDatos(); 
        $data = $Objt->insert_employees();          
        return ($data);
    }

}

// Instancia la clase y maneja la solicitud
$api = new EmpleadosAPI();
$api->handleRequest();
?>
