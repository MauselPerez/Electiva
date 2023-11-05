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
		$sql = "
			SELECT 
				* 
			FROM 
				ac_employees 
			WHERE 
				user_id=$idu ";

		$lista = $this->consulta_generales($sql);	
		return $lista;
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
}//fin CLASE

?>
