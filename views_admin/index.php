<?php
session_start();
include "../public/includes/mistakes.php";
see_errors();
// Define el título de la página
$title = "Página de Usuario";
// Contenido específico de la página
ob_start();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title" style="color: red;"> Reporte de Marcaciones </h6>
            </div>
            <div class="card-body">
                <table id="markings" class="data-table table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Cargo</th>
                            <th>Departamento</th>
                            <th>Email</th>
                            <th>Fecha/Hora entrada</th>
                            <th>Fecha/Hora entrada</th>
                            <th>Entrada/Salida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>123456789</td>
                            <td>Nombre</td>
                            <td>Apellido</td>
                            <td>Cargo</td>
                            <td>Departamento</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Cargo</th>
                            <th>Departamento</th>
                            <th>Email</th>
                            <th>Fecha/Hora entrada</th>
                            <th>Fecha/Hora entrada</th>
                            <th>Entrada/Salida</th>
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
