<?php
session_start();
if (!isset($_SESSION['user'])) 
{
  header('Location: ../templates/login.php');
}
// Define el título de la página
$title = "Página de Usuario";
// Contenido específico de la página
ob_start();
?>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title" style="color: red;">Listado de Empleados </h6>
        <a href="#" class="btn btn-primary" style="float: right;"> <i class="nav-icon fas fa-id-badge"></i> Crear Empleado</a>
      </div>
      <div class="card-body">
        <table id="employees" class="data-table table table-bordered table-striped">
          <thead>
            <tr>
              <th>Cedula</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Edad</th>
              <th>Telefono</th>
              <th>Email</th>
              <th>Fecha de admision</th>
              <th>Cargo</th>
              <th>Departamento</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>123456789</td>
              <td>Loran</td>
              <td>Peñuela</td>
              <td>30</td>
              <td>3002004050</td>
              <td>lpeñuela@gmail.com</td>
              <td>2023-10-06</td>
              <td>Personal de apoyó</td>
              <td>Bienestar</td>
              <td style="text-align: center;"><span class="badge badge-success">Activo</span></td>
              <td style="text-align: center;">
                <a href="#" class="btn btn-warning btn-sm"> <i class="nav-icon fas fa-edit"></i></a>
                <a href="#" class="btn btn-danger btn-sm"> <i class="nav-icon fas fa-trash"></i></a>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th>Cedula</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Edad</th>
              <th>Telefono</th>
              <th>Email</th>
              <th>Fecha de admision</th>
              <th>Cargo</th>
              <th>Departamento</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base.php';
?>
