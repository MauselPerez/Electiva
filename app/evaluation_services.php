<?php
session_start();
include "../controllers/controller_consultas_api.php";
class EvaluationAPI{
    function get_all_software(){
        $Objt= new ExtraerDatos();
        $data = [];
        $software = [];
        $data = $Objt->get_all_software();
        
        foreach ($data as $d) {
            $item = array(
                "codigo" => $d['cod'],
                "nombre" => $d['name'],
                "system" => $d['systems'],
                "desarrollador" => $d['developer'],
                "requerimientos" => $d['requirements'],
                "descripcion" => $d['description'],
                "precio" => $d['price'],
            );
            array_push($software, $item);
        }
        return ($software);
    }

    //INSERTAR SOFTWARE
    function insert_software($name, $systems, $developer, $requirements, $description, $price)
    {
        $Objt= new ExtraerDatos();
        $data = [];
        $software = [];
        $result = $Objt->insert_software($name, $systems, $developer, $requirements, $description, $price);

        if ($result == 1) 
        {
            $data = $Objt->get_all_software();
            foreach ($data as $d) {
                $item = array(
                    "cod" => $d['cod'],
                    "name" => $d['name'],
                    "systems" => $d['systems'],
                    "developer" => $d['developer'],
                    "requirements" => $d['requirements'],
                    "description" => $d['description'],
                    "price" => $d['price'],
                );
                array_push($software, $item);
            }
            return ($software);
        }
        else
        {
            return (0);
        }
    }

    //CONSULTAR SOFTWARE
    function get_software_by_id($id)
    {
        $Objt= new ExtraerDatos();
        $data = [];
        $software = [];
        $data = $Objt->get_software_by_id($id);
        
        foreach ($data as $d) {
            $item = array(
                "codigo" => $d['cod'],
                "nombre" => $d['name'],
                "system" => $d['systems'],
                "desarrollador" => $d['developer'],
                "requerimientos" => $d['requirements'],
                "descripcion" => $d['description'],
                "precio" => $d['price'],
            );
            array_push($software, $item);
        }
        return ($software);
    }

    //ELIMINAR SOFTWARE
    function delete_software($id)
    {
        $Objt= new ExtraerDatos();
        $data = [];
        $software = [];
        $result = $Objt->delete_software($id);

        if ($result == 1) 
        {
            $data = $Objt->get_all_software();
            foreach ($data as $d) {
                $item = array(
                    "cod" => $d['cod'],
                    "name" => $d['name'],
                    "systems" => $d['systems'],
                    "developer" => $d['developer'],
                    "requirements" => $d['requirements'],
                    "description" => $d['description'],
                    "price" => $d['price'],
                );
                array_push($software, $item);
            }
            return ($software);
        }
        else
        {
            return (0);
        }
    }
}