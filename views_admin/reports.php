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
if ($delivered == 0) {
    $average_delivery = 0;
} else {
    $average_delivery = ($quantity_to_deliver / $delivered) * 100;
}
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
<script src="../scripts/chart.js"></script>
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
                <span class="info-box-text">Promedio entregas al mes</span>
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
</div>
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



    });
</script>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base_modules.php';
?>
