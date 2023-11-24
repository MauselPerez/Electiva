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

	function Operaciones($sql)
	{
		$obj = new DBConfig();

		$obj->config();
		$obj->conexion();

		$records = $obj->Operaciones($sql);

		$obj->close();
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
		$sql = "SELECT 
					users.id,
					users.user,
					users.password,
					users.is_active,
					e.img,
					e.user_id,
					e.document_number,
					CONCAT(e.first_name,' ',e.last_name) AS name_employee,
					e.id_charges,
					e.id_departments
					
				FROM 
					users 

				INNER JOIN
					ac_employees e
						ON e.user_id = users.id	
						
				WHERE 
					user='$user'";
				
		$lista = $this->consulta_generales($sql);	
		return $lista;
	}

	//REGISTRAR ENTRADA DEL EMPLEADO
	function insert_entry($user_id)
	{
		$sql = "INSERT INTO ac_markings 
					(id_employee, entry_date) 
				VALUES 
					($user_id, NOW())";
				
		return $this->Operaciones($sql);
	}

	//REGISTRAR CREAR USUARIO EMPLEADO
	function insert_user($user,$pass)
	{
		$sql = "INSERT INTO users
					(user,password) 
				VALUES 
					($user, $pass)";
				
		return $this->Operaciones($sql);
	}

	//REGISTRAR CREAR EMPLEADO
	function insert_employees($document_number,$first_name,$last_name,$age,$phone,$img,$admission_date)
	{
		$sql = "INSERT INTO ac_employees 
					(document_number, first_name, last_name, age, phone,img,admission_date,) 
				VALUES 
					($document_number,$first_name,$last_name,$age,$phone,$img,$admission_date)";
				
		return $this->Operaciones($sql);
	}

	 //MUESTRA SELECT DEPARTMENT
	 function get_all_department($start=0, $regsCant = 0)
	 {
		 $sql = "
			 SELECT 
				 * 
			 FROM 
			 ac_departments";
 
		 if ($regsCant > 0 )
			 $sql = "SELECT * from ac_departments $start,$regsCant";
		 $lista = $this->consulta_generales($sql);	
		 return $lista;
	 }

	  //MUESTRA SELECT CARGO
	function get_all_charges($start=0, $regsCant = 0)
	{
		$sql = "
			SELECT 
				* 
			FROM 
				ac_charges";

		if ($regsCant > 0 )
			$sql = "SELECT * from ac_charges $start,$regsCant";
		$lista = $this->consulta_generales($sql);	
		return $lista;
	}
	

}//fin CLASE

?>
