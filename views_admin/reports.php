<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}

require_once '../controllers/reports_controller.php';

$reportsController = new ReportsController();

$quantity_to_deliver = $reportsController->getQuantityToDeliver();
$delivered = $reportsController->getDelivered();
$not_delivered = $quantity_to_deliver - $delivered;
$average_delivery = $reportsController->getAverageDelivery();
$schedules = $reportsController->getSchedules();
$title = "Reporte de entregas";
ob_start();
?>

<style>
    #title {
        background-color: white;
        border: 2px solid gray;
        border-radius: 10px;
        margin-top: 5px;
    }
</style>
<div class="row">
    <div class="col-md-12" id="title" style="margin-bottom: 1em;">
        <h3 class="page-header" style="padding-top: 5px;">
            Reporte de entrega 
            <button href="snacks.php" class="btn-sm btn-primary" style="float: right; margin-top:3px;" id="return">
                <i class="fas fa-arrow-left"></i>  Regresar
            </button>
        </h3>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-handshake"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Cantidad a entregar mensual</span>
                <span class="info-box-number">
                    <?=$quantity_to_deliver?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-down"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">No entregadas al mes</span>
                <span class="info-box-number"><?=$not_delivered?></span>
            </div>
        </div>
    </div>
    <div class="clearfix hidden-md-up"></div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-thumbs-up"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Entregadas al mes</span>
                <span class="info-box-number"><?=$delivered?></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calculator"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Promedio entregas mensual</span>
                <span class="info-box-number"><?=$average_delivery?> %</span>
            </div>
        </div>
    </div>

    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <div class="card-body">
            <div class="chart">
                <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-12" id="title" style="margin-bottom: 1em;">
        <h3 class="page-header" style="padding-top: 5px;">
            Reporte de entregas a estudiantes 
        </h3>
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th></th>
                    <th style="display:none;">ID</th>
                    <th>Fecha de entrega</th>
                    <th>Creado por</th>
                    <th>Fecha de creacion</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
<?php
                foreach ($schedules as $schedule) 
                {
?>
                    <tr>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" onclick="loadDetails(<?=$schedule['id']?>)" data-toggle="modal" data-target="#detailsModal">
                                <i class="fa fa-plus"></i>
                            </button>
                        </td>
                        <td style="display:none;"><?=$schedule['id']?></td>
                        <td><?=$schedule['delivery_day']?></td>
                        <td><?=$schedule['created_by']?></td>
                        <td><?=$schedule['created_at']?></td>
                        <td>
<?php
                            if ($schedule['status'] == 1) 
                            {
?>
                                <span class="badge badge-success">Entregado</span>
<?php
                            } 
                            else 
                            {
?>
                                <span class="badge badge-danger">No entregado</span>
<?php                    
                            }
?>
                        </td>
                    </tr>
<?php
                }
?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="detailsModal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Listado de entregas a estudiantes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Exportar a excel -->
                <button class="btn btn-success float-left" id="exportExcel">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                </button>
                <table id="table_details" class="display table table-bordered" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Programa academico</th>
                            <th>Semestre</th>
                        </tr>
                    </thead>
                    <tbody id="tbody_details">
                    </tbody>
                    <tfoot class="thead-dark">
                        <tr>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Programa academico</th>
                            <th>Semestre</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--<script src="../scripts/chart.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "order": [[ 2, "desc" ]]
        });

        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });

        var areaChartData = {
            labels: [
<?php
                $deliveries = $reportsController->getDeliveriesByMonth();
                foreach ($deliveries as $delivery) 
                {
                    echo "'" . $delivery['month'] . "',";
                }
?>
            ],
            datasets: [{
                label: 'Entregas',
                backgroundColor: 'rgba(60,141,188,0.9)',
                borderColor: 'rgba(60,141,188,0.8)',
                pointRadius: false,
                pointColor: '#3b8bba',
                pointStrokeColor: 'rgba(60,141,188,1)',
                pointHighlightFill: '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data: [
<?php
                    foreach ($deliveries as $delivery) 
                    {
                        echo $delivery['delivered'] . ",";
                    }
?>
                ]
            }]
        }

        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        var barChartData = jQuery.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        barChartData.datasets[0] = temp0

        var barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false
        }

        var barChart = new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })

        $('#exportExcel').click(function() {
            var table = $('#table_details').DataTable();
            var data = table.data().toArray();
            var csv = 'Cedula,Nombres,Apellidos,Programa academico,Semestre\n';
            data.forEach(function(row) {
                csv += row.join(',');
                csv += "\n";
            });

            var hiddenElement = document.createElement('a');
            hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
            hiddenElement.target = '_blank';
            hiddenElement.download = 'reporte_entregas.csv';
            hiddenElement.click();
        });
    });

    function loadDetails(id) {
        if ($.fn.DataTable.isDataTable('#table_details')) {
            $('#table_details').DataTable().destroy();
        }
        $.ajax({
            url: '../controllers/reports_controller.php',
            type: 'POST',
            data: { action: 'getStudentsByDeliveryScheduling', id: id },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (typeof data === "string") {
                    data = JSON.parse(data);
                }

                $("#tbody_details").empty();
                response.forEach(function(student) {
                    $("#tbody_details").append(
                        "<tr>" +
                        "<td>" + student.document_number + "</td>" +
                        "<td>" + student.first_name + "</td>" +
                        "<td>" + student.last_name + "</td>" +
                        "<td>" + student.academic_program + "</td>" +
                        "<td>" + student.semester + "</td>" +
                        "</tr>"
                    );
                });

                $('#table_details').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'excel', 'pdf'
                    ],
                    "order": [[ 1, "asc" ]],
                    initComplete: function () {
                        this.api().columns().every(function () {
                            var column = this;
                            var select = $('<select><option value=""></option></select>')
                                .appendTo($(column.footer()).empty())
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex(
                                        $(this).val()
                                    );

                                    column
                                        .search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });

                            column.data().unique().sort().each(function (d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>')
                            });
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al procesar la solicitud:', error);
                console.log('Respuesta del servidor:', xhr.responseText);
            }

        });
    }


</script>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base_modules.php';
?>
