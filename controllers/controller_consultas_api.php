<?php
session_start();
require_once( "../models/models_admin.php");

class ConsultasDB extends DBConfig {
	function consulta_generales($sql){
		$this->config();
		$this->conexion(); 

		$records = $this->Consultas($sql);	

		$this->close();		
		return $records;				
	}
}


/**
* IMPLEMENTACION DE ACCESO A CONSULTAS PARA PROTEGER MAS LA VISTA
*/
class ExtraerDatos extends ConsultasDB
{
	// ****************************************************************************
	// Agregue aqui debajo el resto de Funciones - Se ha dejado  Listado y detalle
	// ****************************************************************************
    //MUESTRA LISTADO DE EMPLEADOS
	function get_all_employees($start=0, $regsCant = 0)
	{
		$sql = "
			SELECT 
				* 
			FROM 
				ac_employees";

		if ($regsCant > 0 )
			$sql = "SELECT * from ac_employees $start,$regsCant";
		$lista = $this->consulta_generales($sql);	
		return $lista;
	}
	// DETALLE DE EMPLEADOS SELECICONADA SEGUN ID
	function get_employee_by_id($idu)
	{
		$sql = 
			"SELECT 
				*,
				c.name as charge_name,
				d.name as department_name 
			FROM 
				ac_employees e

			INNER JOIN
				ac_charges c
			ON
				c.id = e.id_charges

			INNER JOIN
				ac_departments d
			ON
				d.id = e.id_departments
			WHERE 
				user_id=$idu ";

		$lista = $this->consulta_generales($sql);	
		return $lista[0];
	}
	
	//VALIDAR EL INICIO DE SESION
	function users_validate($user)
	{
		$sql = "
			SELECT 
				* 
			FROM 
				users 
			WHERE 
				user='$user' ";
				
		$lista = $this->consulta_generales($sql);	
		return $lista;
	}

	function insert_entry($user_id)
	{
		$sql = "INSERT INTO 
				ac_markings 
				(id_employee, entry_date) 
			VALUES 
				($user_id, NOW())";
				
		$obj = new DBConfig();
		$obj->config();
		$obj->conexion();
		$records = $obj->Operaciones($sql);

		if ($records) 
		{
            $response = array('success' => true, 'message' => 'Registro de entrada insertado correctamente', 'data' => $records);
        } 
		else 
		{
            $response = array('success' => false, 'message' => 'Error al insertar el registro de entrada', 'data' => $sql);
        }

        return $response;
	}

}//fin CLASE

?>
