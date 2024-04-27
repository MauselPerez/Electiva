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
                <h6 class="card-title" style="color: red;">Listado de Estudiantes </h6>
            </div>
            <div class="card-body">
                <table id="students" class="data-table table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Edad</th>
                            <th>Telefono</th>
                            <th>Email</th>
                            <th>Fecha de admision</th>
                            <th>Carrera</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1004375621</td>
                            <td>Mausel</td>
                            <td>Perez</td>
                            <td>23</td>
                            <td>3046756587</td>
                            <td>mperez@gmail.com</td>
                            <td>2022-10-06</td>
                            <td>Tecnología en Gestion de Sistemas informaticos</td>
                            <td style="text-align: center;"><span class="badge badge-success">Aplica</span></td>
                            <td style="text-align: center;">
                                <a href="#" class="btn btn-warning btn-sm"> <i class="nav-icon fas fa-edit"></i></a>
                                <a href="#" class="btn btn-danger btn-sm"> <i class="nav-icon fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>987456321</td>
                            <td>Jorge</td>
                            <td>Acosta</td>
                            <td>24</td>
                            <td>3026547819</td>
                            <td>jacosta@gmail.com</td>
                            <td>2023-10-06</td>
                            <td>Tecnología en Gestion de Sistemas informaticos</td>
                            <td style="text-align: center;"><span class="badge badge-danger">No aplica</span></td>
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
                            <th>Carrera</th>
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
