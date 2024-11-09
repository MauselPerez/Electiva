<?php
// Define el título de la página
$title = "Reporte de entregas";
// Contenido específico de la página
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
                <span class="info-box-text">Cantidad total a entregar</span>
                <span class="info-box-number">
                    50
                </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-down"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">No entregadas</span>
                <span class="info-box-number">2</span>
            </div>
        </div>
    </div>
    <div class="clearfix hidden-md-up"></div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-thumbs-up"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Entregadas</span>
                <span class="info-box-number">48</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calculator"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Promedio entregas</span>
                <span class="info-box-number">50</span>
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
            labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio"],
            datasets: [
                {
                    label: "Entregas por meses",
                    backgroundColor: "rgba(60,141,188,0.9)",
                    borderColor: "rgba(60,141,188,0.8)",
                    pointRadius: false,
                    pointColor: "#3b8bba",
                    pointStrokeColor: "rgba(60,141,188,1)",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(60,141,188,1)",
                    data: [28, 48, 40, 19, 86, 27, 90]
                }
            ]
        };

        var barChartCanvas = $("#barChart").get(0).getContext("2d");
        var barChart = new Chart(barChartCanvas, {
            type: 'bar',
            data: areaChartData,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                            gridLines: {
                                display: false,
                            }
                        }],
                    yAxes: [{
                            gridLines: {
                                display: false,
                            }
                        }]
                }
            }
        });


    });
</script>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base_modules.php';
?>
