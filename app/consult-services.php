<?php
session_start();
include "../controllers/controller_consultas_api.php";
class ConsultasAPI{
    function get_all_department(){
        $Objt= new ExtraerDatos();
        $data = [];
        $department = [];
        $data = $Objt->get_all_departments();
        
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
    
}