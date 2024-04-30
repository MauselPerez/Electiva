<?php 
include "../controllers/controller_consultas_api.php";

class EmpleadosAPI {

    function handleRequest() 
    {
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
            case 'InsertMarking':
                return $this->InsertMarking($input);
<<<<<<< HEAD
<<<<<<< HEAD
            case 'InsertEmployee':
                return $this->InsertEmployee($input);
=======
            case 'InsertExit':
                return $this->InsertExit($input);
>>>>>>> f81b941b5b31ad7904cc50cbe1f7bc1915e33b85
=======
>>>>>>> parent of 5609c72... Up
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
<<<<<<< HEAD

    function InsertEmployee($input) 
    {
        $user = $input['txt_usern'];
        $password = $input['txt_passw'];
        $name = $input['txt_name'];
        $lastname = $input['txt_lastname'];
        $identification = $input['txt_cc'];
        $phone = preg_replace('/[^0-9]/', '', $input['txt_num']);
        $age = $input['txt_edad'];
        $email = $input['txt_email'];
        $entryDate = date("Y/m/d",strtotime($input['txt_fechadmin']));
        $chargeId = $input['charges'];
        $departmentId = $input['departments'];

        // Validar los campos según tus requerimientos antes de la inserción

        $sql = new ExtraerDatos();
        $result = $sql->insert_user($user, $password);
        
        if (empty($result)) 
        {
            $response = array('success' => false, 'message' => 'No se insertó el registro de usuario');
        }
        else 
        {
            $userId = $result; // Obtener el ID del usuario recién insertado
            $resultEmployee = $sql->insert_employee($userId, $name, $lastname, $identification, $phone, $age, $email, $entryDate, $chargeId, $departmentId);

            if (empty($resultEmployee)) 
            {
                $response = array('success' => false, 'message' => 'No se insertó el registro completo del empleado');
            }
            else 
            {
                $response = array('success' => true, 'message' => 'Empleado registrado correctamente', 'data' => $resultEmployee);
            }
        }

        return $response;
    }
<<<<<<< HEAD


=======
>>>>>>> f81b941b5b31ad7904cc50cbe1f7bc1915e33b85
=======
>>>>>>> parent of 5609c72... Up
}

// Instancia la clase y maneja la solicitud
$api = new EmpleadosAPI();
$api->handleRequest();
?>
