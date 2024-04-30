<?php
session_start();
include "../app/markings-services.php";
$objAPI = new MarkingsAPI();
// Define el título de la página
$title = "Reporte de marcaciones";
// Contenido específico de la página
ob_start();
?>
<?php 
$markings = $objAPI->get_all_markings();
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
                            <th>Fecha/Hora salida</th>
                            <th>Entrada/Salida</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
                    foreach ($markings as $employee) 
                    {
                        if ($employee['state'] == 'Entrada') 
                        {
                            $employee['state'] = '<span class="badge badge-success">Entrada</span>';
                        }
                        else 
                        {
                            $employee['state'] = '<span class="badge badge-danger">Salida</span>';
                        }
?>
                        <tr>
                            <td><?=$employee['citizen_card']; ?></td>
                            <td><?=$employee['name_employee']; ?></td>
                            <td><?=$employee['last_name_employee']; ?></td>
                            <td><?=$employee['name_charge']; ?></td>
                            <td><?=$employee['name_department']; ?></td>
                            <td><?=$employee['electronic_mail']; ?></td>
                            <td><?=$employee['entry']; ?></td>
                            <td><?=$employee['exit']; ?></td>
                            <td style="text-align: center;"><?=$employee['state']; ?></td>
                        </tr>
<?php
                    }
?>
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
