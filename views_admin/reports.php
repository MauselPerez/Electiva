<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}

require_once '../controllers/reports_controller.php';

$reportsController = new ReportsController();

$quantity_to_deliver = (int) $reportsController->getQuantityToDeliver();
$delivered = (int) $reportsController->getDelivered();
$not_delivered = max($quantity_to_deliver - $delivered, 0);
$average_delivery = round((float) $reportsController->getAverageDelivery(), 1);
$schedules = $reportsController->getSchedules();
$deliveriesByMonth = $reportsController->getDeliveriesByMonth();
$scheduleStatusSummary = $reportsController->getScheduleStatusSummary();
$upcomingSchedules = $reportsController->getUpcomingSchedules(5);
$deliveriesByProgram = $reportsController->getDeliveriesByProgram();
$title = "Reporte de entregas";

$completionRate = $quantity_to_deliver > 0 ? round(($delivered / $quantity_to_deliver) * 100, 1) : 0;
$totalSchedules = (int) ($scheduleStatusSummary['total'] ?? 0);
$pendingSchedules = (int) ($scheduleStatusSummary['pending'] ?? 0);
$executedSchedules = (int) ($scheduleStatusSummary['delivered'] ?? 0);
$canceledSchedules = (int) ($scheduleStatusSummary['canceled'] ?? 0);
$programCount = count($deliveriesByProgram);

$statusConfig = [
    0 => ['label' => 'Pendiente', 'color' => '#dc3545', 'badge' => 'danger', 'icon' => 'far fa-clock'],
    1 => ['label' => 'Ejecutado', 'color' => '#28a745', 'badge' => 'success', 'icon' => 'fas fa-check-circle'],
    2 => ['label' => 'Anulado', 'color' => '#fd7e14', 'badge' => 'warning', 'icon' => 'fas fa-ban']
];

$monthMap = [
    'January' => 'Enero',
    'February' => 'Febrero',
    'March' => 'Marzo',
    'April' => 'Abril',
    'May' => 'Mayo',
    'June' => 'Junio',
    'July' => 'Julio',
    'August' => 'Agosto',
    'September' => 'Septiembre',
    'October' => 'Octubre',
    'November' => 'Noviembre',
    'December' => 'Diciembre'
];

$dayMap = [
    'Sunday' => 'Domingo',
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado'
];

$monthlyLabels = [];
$monthlyValues = [];
foreach ($deliveriesByMonth as $item) {
    $monthlyLabels[] = $monthMap[$item['month']] ?? $item['month'];
    $monthlyValues[] = (int) $item['delivered'];
}

if (empty($monthlyLabels)) {
    $monthlyLabels = ['Sin datos'];
    $monthlyValues = [0];
}

$programLabels = [];
$programValues = [];
foreach ($deliveriesByProgram as $item) {
    $programLabels[] = $item['academic_program'];
    $programValues[] = (int) $item['delivered'];
}

if (empty($programLabels)) {
    $programLabels = ['Sin datos'];
    $programValues = [0];
}

$completionMessage = 'El módulo muestra un comportamiento estable de entregas.';
if ($completionRate < 40) {
    $completionMessage = 'El nivel de cumplimiento es bajo y conviene revisar planificación y ejecución.';
} elseif ($completionRate < 75) {
    $completionMessage = 'El avance es intermedio; todavía hay margen claro de mejora este mes.';
}

ob_start();
?>

<style>
    .reports-page {
        padding: 18px 8px 28px 0;
    }

    .reports-hero {
        background: linear-gradient(135deg, #28313b 0%, #485461 60%, #5f7a96 100%);
        border-radius: 22px;
        box-shadow: 0 22px 50px rgba(15, 42, 67, 0.18);
        color: #fff;
        margin: 10px 0 18px;
        overflow: hidden;
        position: relative;
    }

    .reports-hero::after {
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.24), transparent 34%);
        content: "";
        inset: 0;
        pointer-events: none;
        position: absolute;
    }

    .reports-hero-body {
        padding: 26px 28px;
        position: relative;
        z-index: 1;
    }

    .reports-title {
        font-size: 1.95rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .reports-text {
        color: rgba(255, 255, 255, 0.88);
        margin-bottom: 0;
        max-width: 760px;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 14px;
    }

    .hero-btn {
        border-radius: 999px;
        font-weight: 700;
        padding: 10px 16px;
    }

    .hero-btn.btn-light {
        color: #0f2a43;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #e7edf3;
        border-radius: 18px;
        box-shadow: 0 14px 34px rgba(17, 24, 39, 0.06);
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    .metric-card::before {
        content: "";
        height: 4px;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
    }

    .metric-card.primary::before { background: #0f2a43; }
    .metric-card.success::before { background: #28a745; }
    .metric-card.danger::before { background: #dc3545; }
    .metric-card.warning::before { background: #ffb703; }
    .metric-card.info::before { background: #1f8ec9; }
    .metric-card.secondary::before { background: #6c7a89; }

    .metric-body {
        align-items: center;
        display: flex;
        justify-content: space-between;
        padding: 18px 18px 16px;
    }

    .metric-title {
        color: #6c7a89;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .metric-number {
        color: #0f2a43;
        display: block;
        font-size: 1.9rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 5px;
    }

    .metric-caption {
        color: #6c7a89;
        font-size: 0.87rem;
    }

    .metric-icon {
        align-items: center;
        border-radius: 14px;
        color: #fff;
        display: inline-flex;
        font-size: 1.1rem;
        height: 48px;
        justify-content: center;
        width: 48px;
    }

    .metric-icon.primary { background: #0f2a43; }
    .metric-icon.success { background: #28a745; }
    .metric-icon.danger { background: #dc3545; }
    .metric-icon.warning { background: #ffb703; color: #503900; }
    .metric-icon.info { background: #1f8ec9; }
    .metric-icon.secondary { background: #6c7a89; }

    .panel-card {
        background: #fff;
        border: 1px solid #e7edf3;
        border-radius: 20px;
        box-shadow: 0 14px 34px rgba(17, 24, 39, 0.06);
        margin-bottom: 18px;
        overflow: hidden;
    }

    .panel-header {
        align-items: center;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        padding: 18px 20px 14px;
    }

    .panel-title {
        color: #0f2a43;
        font-size: 1.08rem;
        font-weight: 700;
        margin: 0;
    }

    .panel-subtitle {
        color: #6c7a89;
        font-size: 0.9rem;
        margin: 4px 0 0;
    }

    .panel-body {
        padding: 18px 20px 20px;
    }

    .insight-box {
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e7eef6;
        border-radius: 16px;
        padding: 16px;
    }

    .insight-copy {
        color: #48627b;
        font-size: 0.92rem;
        margin-bottom: 12px;
    }

    .progress {
        background-color: #e9f0f5;
        border-radius: 999px;
        height: 12px;
        overflow: hidden;
    }

    .progress-bar-custom {
        background: linear-gradient(90deg, #1f8ec9 0%, #28a745 100%);
    }

    .status-stack {
        display: grid;
        gap: 12px;
    }

    .status-row {
        align-items: center;
        display: grid;
        gap: 10px;
        grid-template-columns: 110px 1fr 44px;
    }

    .status-label {
        color: #48627b;
        font-size: 0.88rem;
        font-weight: 700;
    }

    .status-track {
        background: #ecf2f7;
        border-radius: 999px;
        height: 10px;
        overflow: hidden;
    }

    .status-fill {
        border-radius: 999px;
        height: 100%;
    }

    .status-value {
        color: #0f2a43;
        font-size: 0.88rem;
        font-weight: 700;
        text-align: right;
    }

    .timeline-list {
        display: grid;
        gap: 12px;
        margin: 0;
        padding: 0;
    }

    .timeline-item {
        background: #f8fafc;
        border: 1px solid #e9eff5;
        border-radius: 16px;
        display: flex;
        gap: 12px;
        list-style: none;
        padding: 14px;
    }

    .timeline-date {
        align-items: center;
        background: #0f2a43;
        border-radius: 14px;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 66px;
        padding: 8px;
        text-align: center;
    }

    .timeline-date strong {
        font-size: 1.2rem;
        line-height: 1;
    }

    .timeline-date span {
        font-size: 0.75rem;
        letter-spacing: 0.04em;
        margin-top: 4px;
        text-transform: uppercase;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-title {
        color: #0f2a43;
        font-size: 0.96rem;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .timeline-meta {
        color: #6c7a89;
        font-size: 0.86rem;
        margin-bottom: 8px;
    }

    .timeline-empty {
        background: #f8fafc;
        border: 1px dashed #d6e0e9;
        border-radius: 16px;
        color: #6c7a89;
        padding: 16px;
        text-align: center;
    }

    .status-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-chip {
        background: #f2f6fa;
        border: 1px solid transparent;
        border-radius: 999px;
        color: #48627b;
        cursor: pointer;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 7px 12px;
        transition: all 0.2s ease;
    }

    .filter-chip.active,
    .filter-chip:hover {
        background: #0f2a43;
        color: #fff;
    }

    .reports-table thead th,
    .reports-table tfoot th {
        background: #0f2a43;
        border-color: #0f2a43;
        color: #fff;
    }

    .reports-table tbody tr {
        transition: background-color 0.15s ease, transform 0.15s ease;
    }

    .reports-table tbody tr:hover {
        background: #f7fbff;
        transform: translateX(2px);
    }

    .reports-table tbody tr.is-highlighted {
        background: #eef6ff;
        box-shadow: inset 4px 0 0 #1f8ec9;
    }

    .date-pill {
        background: #eef4fb;
        border-radius: 999px;
        color: #0f2a43;
        display: inline-block;
        font-weight: 700;
        padding: 7px 12px;
    }

    .status-pill {
        align-items: center;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        justify-content: center;
        min-width: 118px;
        padding: 8px 12px;
    }

    .delivered-badge {
        background: #edf7ef;
        border-radius: 999px;
        color: #216e39;
        display: inline-block;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 10px;
    }

    .chart-wrap {
        height: 320px;
        position: relative;
    }

    .chart-wrap.chart-sm {
        height: 280px;
    }

    .modal-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .summary-chip {
        background: #f4f8fb;
        border: 1px solid #e2ebf2;
        border-radius: 999px;
        color: #355068;
        font-size: 0.84rem;
        font-weight: 700;
        padding: 8px 12px;
    }

    .details-empty {
        color: #6c7a89;
        padding: 18px 12px;
        text-align: center;
    }

    @media (max-width: 991.98px) {
        .reports-hero-body {
            padding: 22px 18px;
        }

        .hero-actions {
            justify-content: flex-start;
        }

        .status-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid reports-page">
    <div class="reports-hero">
        <div class="row align-items-center no-gutters reports-hero-body">
            <div class="col-lg-8">
                <div class="reports-title">Centro de reportes de entregas</div>
                <p class="reports-text">Unifica en una sola pantalla el seguimiento de entregas, comportamiento mensual, estado de las planificaciones y el detalle por estudiantes para que el usuario pueda decidir rápido y con contexto.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button class="btn btn-success hero-btn" id="exportSummary">
                        <i class="fas fa-file-download mr-1"></i> Exportar resumen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="metric-card primary">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Meta mensual planificada</div>
                        <span class="metric-number"><?= $quantity_to_deliver ?></span>
                        <div class="metric-caption">Días planificados activos del mes x estudiantes activos</div>
                    </div>
                    <div class="metric-icon primary"><i class="fas fa-bullseye"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="metric-card success">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Entregadas</div>
                        <span class="metric-number"><?= $delivered ?></span>
                        <div class="metric-caption">Registros efectivos del mes actual</div>
                    </div>
                    <div class="metric-icon success"><i class="fas fa-check-double"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="metric-card danger">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Pendientes del mes</div>
                        <span class="metric-number"><?= $not_delivered ?></span>
                        <div class="metric-caption">Diferencia entre meta y ejecución</div>
                    </div>
                    <div class="metric-icon danger"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="metric-card info">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Cumplimiento</div>
                        <span class="metric-number"><?= $completionRate ?>%</span>
                        <div class="metric-caption">Avance real frente a la meta del mes</div>
                    </div>
                    <div class="metric-icon info"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="metric-card warning">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Promedio por mes</div>
                        <span class="metric-number"><?= $average_delivery ?></span>
                        <div class="metric-caption">Promedio histórico de entregas registradas</div>
                    </div>
                    <div class="metric-icon warning"><i class="fas fa-calculator"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="metric-card secondary">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Programas impactados</div>
                        <span class="metric-number"><?= $programCount ?></span>
                        <div class="metric-caption">Programas con entregas registradas</div>
                    </div>
                    <div class="metric-icon secondary"><i class="fas fa-university"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Tendencia mensual de entregas</h3>
                        <p class="panel-subtitle">Compara rápidamente el volumen de entregas registradas por mes.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap">
                        <canvas id="deliveriesBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Estado operativo</h3>
                        <p class="panel-subtitle">Lectura rápida del avance general del módulo.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="insight-box mb-4">
                        <div class="insight-copy"><?= htmlspecialchars($completionMessage) ?></div>
                        <div class="d-flex justify-content-between mb-2">
                            <strong style="color:#0f2a43;">Cumplimiento mensual</strong>
                            <strong style="color:#0f2a43;"><?= $completionRate ?>%</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-custom" style="width: <?= min($completionRate, 100) ?>%;"></div>
                        </div>
                    </div>

                    <div class="status-stack">
                        <div class="status-row">
                            <div class="status-label">Pendientes</div>
                            <div class="status-track"><div class="status-fill" style="width: <?= $totalSchedules > 0 ? round(($pendingSchedules / $totalSchedules) * 100, 1) : 0 ?>%; background:#dc3545;"></div></div>
                            <div class="status-value"><?= $pendingSchedules ?></div>
                        </div>
                        <div class="status-row">
                            <div class="status-label">Ejecutadas</div>
                            <div class="status-track"><div class="status-fill" style="width: <?= $totalSchedules > 0 ? round(($executedSchedules / $totalSchedules) * 100, 1) : 0 ?>%; background:#28a745;"></div></div>
                            <div class="status-value"><?= $executedSchedules ?></div>
                        </div>
                        <div class="status-row">
                            <div class="status-label">Anuladas</div>
                            <div class="status-track"><div class="status-fill" style="width: <?= $totalSchedules > 0 ? round(($canceledSchedules / $totalSchedules) * 100, 1) : 0 ?>%; background:#fd7e14;"></div></div>
                            <div class="status-value"><?= $canceledSchedules ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Entregas por programa</h3>
                        <p class="panel-subtitle">Distribución general de las entregas registradas.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="chart-wrap chart-sm">
                        <canvas id="programsDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Planificaciones y entregas</h3>
                        <p class="panel-subtitle">Consulta las planificaciones, sus estados y cuántos estudiantes recibieron entrega en cada fecha.</p>
                    </div>
                    <div class="status-filter" id="status-filters">
                        <button type="button" class="filter-chip active" data-filter="all">Todas</button>
                        <button type="button" class="filter-chip" data-filter="Pendiente">Pendientes</button>
                        <button type="button" class="filter-chip" data-filter="Ejecutado">Ejecutadas</button>
                        <button type="button" class="filter-chip" data-filter="Anulado">Anuladas</button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div style="color:#6c7a89; font-size:0.92rem;">Total de planificaciones: <strong style="color:#0f2a43;"><?= $totalSchedules ?></strong></div>
                        <button class="btn btn-outline-primary btn-sm" id="exportTable">
                            <i class="fas fa-file-csv mr-1"></i> Exportar tabla
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover reports-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Detalle</th>
                                    <th>ID</th>
                                    <th>Fecha de entrega</th>
                                    <th>Creado por</th>
                                    <th>Fecha de creación</th>
                                    <th>Estudiantes entregados</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
<?php 
                            foreach ($schedules as $schedule) {
                                $statusValue = (int) ($schedule['status'] ?? 0);
                                $statusData = $statusConfig[$statusValue] ?? $statusConfig[0];
                                $timestamp = strtotime($schedule['delivery_day']);
                                $formattedDate = date('Y-m-d', $timestamp) . ' · ' . ($dayMap[date('l', $timestamp)] ?? date('l', $timestamp));
?>
                                <tr data-status="<?= htmlspecialchars($statusData['label']) ?>">
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm"
                                            onclick="loadDetails(this)"
                                            data-toggle="modal"
                                            data-target="#detailsModal"
                                            data-id="<?= (int) $schedule['id'] ?>"
                                            data-delivery-day="<?= htmlspecialchars($schedule['delivery_day']) ?>"
                                            data-created-by="<?= htmlspecialchars($schedule['created_by']) ?>"
                                            data-status="<?= htmlspecialchars($statusData['label']) ?>"
                                            data-delivered-students="<?= (int) $schedule['delivered_students'] ?>"
                                        >
                                            <i class="fas fa-users"></i>
                                        </button>
                                    </td>
                                    <td><strong>#<?= (int) $schedule['id'] ?></strong></td>
                                    <td><span class="date-pill"><?= htmlspecialchars($formattedDate) ?></span></td>
                                    <td><?= htmlspecialchars($schedule['created_by']) ?></td>
                                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($schedule['created_at']))) ?></td>
                                    <td><span class="delivered-badge"><?= (int) $schedule['delivered_students'] ?> estudiantes</span></td>
                                    <td class="text-center">
                                        <span class="status-pill" style="background-color: <?= htmlspecialchars($statusData['color']) ?>;">
                                            <i class="<?= htmlspecialchars($statusData['icon']) ?>"></i>
                                            <?= htmlspecialchars($statusData['label']) ?>
                                        </span>
                                    </td>
                                </tr>
<?php 
                            } 
?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Detalle</th>
                                    <th>ID</th>
                                    <th>Fecha de entrega</th>
                                    <th>Creado por</th>
                                    <th>Fecha de creación</th>
                                    <th>Estudiantes entregados</th>
                                    <th>Estado</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Próximas planificaciones</h3>
                        <p class="panel-subtitle">Fechas futuras activas para seguimiento operativo.</p>
                    </div>
                </div>
                <div class="panel-body">
<?php if (!empty($upcomingSchedules)) { ?>
                    <ul class="timeline-list">
<?php foreach ($upcomingSchedules as $schedule) {
    $statusValue = (int) ($schedule['status'] ?? 0);
    $statusData = $statusConfig[$statusValue] ?? $statusConfig[0];
    $timestamp = strtotime($schedule['delivery_day']);
    $monthShort = strtoupper(substr($monthMap[date('F', $timestamp)] ?? date('F', $timestamp), 0, 3));
?>
                        <li class="timeline-item">
                            <div class="timeline-date">
                                <strong><?= date('d', $timestamp) ?></strong>
                                <span><?= htmlspecialchars($monthShort) ?></span>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title"><?= htmlspecialchars($dayMap[date('l', $timestamp)] ?? date('l', $timestamp)) ?></div>
                                <div class="timeline-meta">Creado por <?= htmlspecialchars($schedule['created_by']) ?> · <?= (int) $schedule['delivered_students'] ?> entregas registradas</div>
                                <span class="badge badge-<?= htmlspecialchars($statusData['badge']) ?>"><?= htmlspecialchars($statusData['label']) ?></span>
                            </div>
                        </li>
<?php } ?>
                    </ul>
<?php } else { ?>
                    <div class="timeline-empty">No hay planificaciones futuras activas registradas.</div>
<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailsModal">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Detalle de entregas por planificación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="modal-summary" id="detailsSummary"></div>
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <div id="detailsCount" style="color:#6c7a89; font-size:0.92rem;">Sin datos cargados.</div>
                    <button class="btn btn-success" id="exportExcel">
                        <i class="fas fa-file-excel mr-1"></i> Exportar detalle
                    </button>
                </div>
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
                    <tbody id="tbody_details"></tbody>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        var activeStatusFilter = 'all';

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'table') {
                return true;
            }

            if (activeStatusFilter === 'all') {
                return true;
            }

            var rowNode = mainTable.row(dataIndex).node();
            return $(rowNode).data('status') === activeStatusFilter;
        });

        var mainTable = $('#table').DataTable({
            responsive: true,
            autoWidth: false,
            order: [[1, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            }
        });

        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });

        var monthlyLabels = <?= json_encode($monthlyLabels) ?>;
        var monthlyValues = <?= json_encode($monthlyValues) ?>;
        var programLabels = <?= json_encode($programLabels) ?>;
        var programValues = <?= json_encode($programValues) ?>;
        var chartPalette = ['#0f2a43', '#1f5f8b', '#58a7c8', '#ffb703', '#fb8500', '#8ecae6', '#219ebc'];

        new Chart(document.getElementById('deliveriesBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Entregas registradas',
                    data: monthlyValues,
                    backgroundColor: ['#1f5f8b', '#2d7fb0', '#4196c8', '#58a7c8', '#7cc0d8', '#99cfe2'],
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('programsDoughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: programLabels,
                datasets: [{
                    data: programValues,
                    backgroundColor: chartPalette,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '62%'
            }
        });

        function applyFilter(filterValue) {
            activeStatusFilter = filterValue;
            mainTable.draw();
        }

        $('#status-filters').on('click', '.filter-chip', function() {
            $('#status-filters .filter-chip').removeClass('active');
            $(this).addClass('active');
            applyFilter($(this).data('filter'));
        });

        $('#table tbody').on('click', 'tr', function(event) {
            if ($(event.target).closest('button').length) {
                return;
            }

            $('#table tbody tr').removeClass('is-highlighted');
            $(this).addClass('is-highlighted');
        });

        function sanitizeCell(value) {
            return $('<div>').html(value).text().trim();
        }

        function exportCsv(filename, headers, rows) {
            var csvRows = [];
            csvRows.push(headers.join(','));

            rows.forEach(function(row) {
                var sanitized = row.map(function(cell) {
                    var text = String(cell).replace(/"/g, '""');
                    return '"' + text + '"';
                });

                csvRows.push(sanitized.join(','));
            });

            var blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            URL.revokeObjectURL(link.href);
        }

        $('#exportSummary').click(function() {
            exportCsv('resumen_reportes_entregas.csv', ['Indicador', 'Valor'], [
                ['Meta mensual planificada', <?= json_encode($quantity_to_deliver) ?>],
                ['Entregadas', <?= json_encode($delivered) ?>],
                ['Pendientes del mes', <?= json_encode($not_delivered) ?>],
                ['Cumplimiento', <?= json_encode($completionRate . '%') ?>],
                ['Promedio por mes', <?= json_encode($average_delivery) ?>],
                ['Planificaciones totales', <?= json_encode($totalSchedules) ?>],
                ['Planificaciones pendientes', <?= json_encode($pendingSchedules) ?>],
                ['Planificaciones ejecutadas', <?= json_encode($executedSchedules) ?>],
                ['Planificaciones anuladas', <?= json_encode($canceledSchedules) ?>]
            ]);
        });

        $('#exportTable').click(function() {
            var rows = mainTable.rows({ search: 'applied' }).data().toArray().map(function(row) {
                return [
                    sanitizeCell(row[1]),
                    sanitizeCell(row[2]),
                    sanitizeCell(row[3]),
                    sanitizeCell(row[4]),
                    sanitizeCell(row[5]),
                    sanitizeCell(row[6])
                ];
            });

            exportCsv('planificaciones_y_entregas.csv', ['ID', 'Fecha de entrega', 'Creado por', 'Fecha de creación', 'Estudiantes entregados', 'Estado'], rows);
        });

        $('#exportExcel').click(function() {
            if (!$.fn.DataTable.isDataTable('#table_details')) {
                return;
            }

            var detailsTable = $('#table_details').DataTable();
            var rows = detailsTable.rows({ search: 'applied' }).data().toArray().map(function(row) {
                return row.map(function(cell) {
                    return sanitizeCell(cell);
                });
            });

            exportCsv('detalle_entregas_estudiantes.csv', ['Cedula', 'Nombres', 'Apellidos', 'Programa académico', 'Semestre'], rows);
        });

        $('#detailsModal').on('hidden.bs.modal', function() {
            $('#tbody_details').html('');
            $('#detailsSummary').html('');
            $('#detailsCount').text('Sin datos cargados.');
        });
    });

    function loadDetails(button) {
        var id = $(button).data('id');
        var deliveryDay = $(button).data('delivery-day');
        var createdBy = $(button).data('created-by');
        var status = $(button).data('status');
        var deliveredStudents = $(button).data('delivered-students');

        $('#modalTitle').text('Detalle de la planificación #' + id);
        $('#detailsSummary').html(
            '<span class="summary-chip"><i class="far fa-calendar-alt mr-1"></i>' + deliveryDay + '</span>' +
            '<span class="summary-chip"><i class="fas fa-user mr-1"></i>' + createdBy + '</span>' +
            '<span class="summary-chip"><i class="fas fa-tasks mr-1"></i>' + status + '</span>' +
            '<span class="summary-chip"><i class="fas fa-user-graduate mr-1"></i>' + deliveredStudents + ' estudiantes</span>'
        );
        $('#detailsCount').text('Cargando estudiantes...');
        $('#tbody_details').html('<tr><td colspan="5" class="details-empty">Cargando información...</td></tr>');

        if ($.fn.DataTable.isDataTable('#table_details')) {
            $('#table_details').DataTable().destroy();
        }

        $.ajax({
            url: '../controllers/reports_controller.php',
            type: 'POST',
            data: { action: 'getStudentsByDeliveryScheduling', id: id },
            dataType: 'json',
            success: function(response) {
                $('#tbody_details').empty();

                if (response.error) {
                    $('#tbody_details').html('<tr><td colspan="5" class="details-empty">' + response.error + '</td></tr>');
                    $('#detailsCount').text('Sin estudiantes asociados a esta planificación.');
                    return;
                }

                response.forEach(function(student) {
                    $('#tbody_details').append(
                        '<tr>' +
                        '<td>' + student.document_number + '</td>' +
                        '<td>' + student.first_name + '</td>' +
                        '<td>' + student.last_name + '</td>' +
                        '<td>' + student.academic_program + '</td>' +
                        '<td>' + student.semester + '</td>' +
                        '</tr>'
                    );
                });

                $('#detailsCount').text(response.length + ' estudiantes asociados a la planificación.');

                $('#table_details').DataTable({
                    responsive: true,
                    autoWidth: false,
                    order: [[1, 'asc']],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al procesar la solicitud:', error);
                console.log('Respuesta del servidor:', xhr.responseText);
                $('#tbody_details').html('<tr><td colspan="5" class="details-empty">No fue posible cargar el detalle de estudiantes.</td></tr>');
                $('#detailsCount').text('Error al cargar el detalle.');
            }
        });
    }
</script>
<?php
$content = ob_get_clean();

include '../templates/base_modules.php';
?>