<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}
require_once '../controllers/schedules_controller.php';

$schedulesController = new SchedulesController();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $schedulesController->create($_POST);
    header('Location: schedules.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $schedulesController->delete($_GET['id']);
    header('Location: schedules.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'cancel') {
    $schedulesController->cancel($_GET['id']);
    header('Location: schedules.php');
}

$schedules = $schedulesController->index();
$title = "Planificaciones";

$days_weeks = [
    "Sunday" => "Domingo",
    "Monday" => "Lunes",
    "Tuesday" => "Martes",
    "Wednesday" => "Miércoles",
    "Thursday" => "Jueves",
    "Friday" => "Viernes",
    "Saturday" => "Sábado"
];

$months = [
    "Jan" => "Ene",
    "Feb" => "Feb",
    "Mar" => "Mar",
    "Apr" => "Abr",
    "May" => "May",
    "Jun" => "Jun",
    "Jul" => "Jul",
    "Aug" => "Ago",
    "Sep" => "Sep",
    "Oct" => "Oct",
    "Nov" => "Nov",
    "Dec" => "Dic"
];

$statusConfig = [
    0 => [
        'label' => 'Pendiente',
        'color' => '#dc3545',
        'badge' => 'danger',
        'icon' => 'far fa-clock'
    ],
    1 => [
        'label' => 'Ejecutado',
        'color' => '#28a745',
        'badge' => 'success',
        'icon' => 'fas fa-check-circle'
    ],
    2 => [
        'label' => 'Anulado',
        'color' => '#fd7e14',
        'badge' => 'warning',
        'icon' => 'fas fa-ban'
    ]
];

$stats = [
    'total' => count($schedules),
    'pending' => 0,
    'executed' => 0,
    'canceled' => 0
];

$upcomingSchedules = [];
$today = new DateTime('today');

foreach ($schedules as $schedule) {
    $statusValue = (int) ($schedule['status'] ?? 0);
    if ($statusValue === 1) {
        $stats['executed']++;
    } elseif ($statusValue === 2) {
        $stats['canceled']++;
    } else {
        $stats['pending']++;
    }

    $deliveryDate = DateTime::createFromFormat('Y-m-d', $schedule['delivery_day']);
    if ($deliveryDate && $deliveryDate >= $today && $statusValue !== 2) {
        $upcomingSchedules[] = $schedule;
    }
}

usort($upcomingSchedules, function ($left, $right) {
    return strcmp($left['delivery_day'], $right['delivery_day']);
});

$upcomingSchedules = array_slice($upcomingSchedules, 0, 5);

ob_start();
?>
<!-- fullCalendar -->
<link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fullcalendar/main.min.css">
<link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fullcalendar-daygrid/main.min.css">
<link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fullcalendar-timegrid/main.min.css">
<link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fullcalendar-bootstrap/main.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<style>
    .schedule-page {
        padding: 18px 8px 28px 0;
    }

    .schedule-hero {
        background: linear-gradient(135deg, #343a40 0%, #343a408e 58%, #3e6b98 100%);
        border-radius: 18px;
        box-shadow: 0 18px 40px rgba(23, 50, 77, 0.18);
        color: #fff;
        margin: 10px 0 18px;
        overflow: hidden;
        position: relative;
    }

    .schedule-hero::after {
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.22), transparent 38%);
        content: "";
        inset: 0;
        position: absolute;
        pointer-events: none;
    }

    .schedule-hero-body {
        padding: 24px 26px;
        position: relative;
        z-index: 1;
    }

    .schedule-hero-title {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .schedule-hero-text {
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 0;
        max-width: 680px;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 12px;
    }

    .hero-btn {
        border-radius: 999px;
        font-weight: 600;
        padding: 10px 16px;
    }

    .hero-btn.btn-light {
        color: #17324d;
    }

    .stats-card {
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(17, 24, 39, 0.06);
        height: 100%;
        overflow: hidden;
        position: relative;
    }

    .stats-card::before {
        content: "";
        height: 4px;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
    }

    .stats-card.total::before {
        background: #17324d;
    }

    .stats-card.pending::before {
        background: #dc3545;
    }

    .stats-card.executed::before {
        background: #28a745;
    }

    .stats-card.canceled::before {
        background: #fd7e14;
    }

    .stats-card-body {
        align-items: center;
        display: flex;
        justify-content: space-between;
        padding: 18px 18px 16px;
    }

    .stats-card h6 {
        color: #6c7a89;
        font-size: 0.86rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .stats-card strong {
        color: #17324d;
        display: block;
        font-size: 1.9rem;
        line-height: 1;
    }

    .stats-card span {
        color: #6c7a89;
        font-size: 0.85rem;
    }

    .stats-icon {
        align-items: center;
        border-radius: 14px;
        color: #fff;
        display: inline-flex;
        font-size: 1.1rem;
        height: 46px;
        justify-content: center;
        width: 46px;
    }

    .stats-icon.total {
        background: #17324d;
    }

    .stats-icon.pending {
        background: #dc3545;
    }

    .stats-icon.executed {
        background: #28a745;
    }

    .stats-icon.canceled {
        background: #fd7e14;
    }

    .panel-card {
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 18px;
        box-shadow: 0 14px 34px rgba(17, 24, 39, 0.07);
        margin-bottom: 18px;
        overflow: hidden;
    }

    .panel-header {
        align-items: center;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        padding: 18px 20px 14px;
    }

    .panel-title {
        color: #17324d;
        font-size: 1.1rem;
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

    .calendar-panel .panel-body {
        padding-top: 10px;
    }

    .status-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-chip {
        background: #f3f6f9;
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
        background: #17324d;
        color: #fff;
    }

    .schedule-table thead th,
    .schedule-table tfoot th {
        background: #343a40;
        border-color: #343a40;
        color: #fff;
    }

    .schedule-table tbody tr {
        transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
    }

    .schedule-table tbody tr:hover {
        background: #f7fafc;
        box-shadow: inset 0 0 0 9999px rgba(23, 50, 77, 0.02);
        transform: translateX(2px);
    }

    .schedule-table tbody tr.is-highlighted {
        background: #eef6ff;
        box-shadow: inset 4px 0 0 #3e6b98;
    }

    .schedule-table .date-chip {
        background: #eef4fb;
        border-radius: 999px;
        color: #17324d;
        display: inline-block;
        font-weight: 700;
        padding: 7px 12px;
    }

    .status-pill {
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 0.78rem;
        font-weight: 700;
        gap: 6px;
        align-items: center;
        justify-content: center;
        min-width: 116px;
        padding: 8px 12px;
    }

    .action-group {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .action-group .btn {
        border-radius: 10px;
        box-shadow: none;
    }

    .calendar-shell {
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #edf2f7;
        border-radius: 16px;
        padding: 12px;
    }

    #calendar {
        min-height: 460px;
    }

    .fc .fc-toolbar-title {
        color: #17324d;
        font-size: 1rem;
        font-weight: 700;
    }

    .fc .fc-button-primary {
        background: #17324d;
        border-color: #17324d;
        box-shadow: none;
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active,
    .fc .fc-button-primary:hover {
        background: #244c73;
        border-color: #244c73;
    }

    .legend-list,
    .upcoming-list {
        display: grid;
        gap: 10px;
        margin: 0;
        padding: 0;
    }

    .legend-item,
    .upcoming-item {
        align-items: center;
        display: flex;
        gap: 10px;
        list-style: none;
    }

    .legend-dot {
        border-radius: 50%;
        height: 12px;
        width: 12px;
    }

    .legend-copy {
        color: #48627b;
        font-size: 0.9rem;
    }

    .upcoming-item {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 14px;
        justify-content: space-between;
        padding: 12px 14px;
    }

    .upcoming-date {
        color: #17324d;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .upcoming-meta {
        color: #6c7a89;
        font-size: 0.85rem;
    }

    .upcoming-empty {
        background: #f8fafc;
        border: 1px dashed #d8e2eb;
        border-radius: 14px;
        color: #6c7a89;
        padding: 14px;
        text-align: center;
    }

    .mini-tip {
        background: #fff8e8;
        border: 1px solid #ffe3a0;
        border-radius: 14px;
        color: #7b5b11;
        font-size: 0.88rem;
        padding: 12px 14px;
    }

    .modal-note {
        background: #fff8e8;
        border: 1px solid #ffe3a0;
        border-radius: 12px;
        color: #7b5b11;
        font-size: 0.95rem;
        margin-bottom: 16px;
        padding: 12px 14px;
    }

    @media (max-width: 991.98px) {
        .schedule-hero-body {
            padding: 22px 18px;
        }

        .hero-actions {
            justify-content: flex-start;
        }

        #calendar {
            min-height: 380px;
        }
    }
</style>
<div class="container-fluid schedule-page">
    <div class="schedule-hero">
        <div class="row align-items-center no-gutters schedule-hero-body">
            <div class="col-lg-8">
                <div class="schedule-hero-title">Cronograma de entregas</div> 
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button type="button" class="btn-sm btn-primary" data-toggle="modal" data-target="#new_schedule">
                        <i class="fas fa-plus"></i> Nueva planificación
                    </button>
                    <button class="btn-sm btn-primary" style="float: right;" id="return">
                        <i class="fas fa-arrow-left"></i>  Regresar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card total">
                <div class="stats-card-body">
                    <div>
                        <h6>Total</h6>
                        <strong><?= $stats['total'] ?></strong>
                        <span>Fechas registradas</span>
                    </div>
                    <div class="stats-icon total">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card pending">
                <div class="stats-card-body">
                    <div>
                        <h6>Pendientes</h6>
                        <strong><?= $stats['pending'] ?></strong>
                        <span>Listas por ejecutar</span>
                    </div>
                    <div class="stats-icon pending">
                        <i class="far fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card executed">
                <div class="stats-card-body">
                    <div>
                        <h6>Ejecutadas</h6>
                        <strong><?= $stats['executed'] ?></strong>
                        <span>Planificaciones completadas</span>
                    </div>
                    <div class="stats-icon executed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="stats-card canceled">
                <div class="stats-card-body">
                    <div>
                        <h6>Anuladas</h6>
                        <strong><?= $stats['canceled'] ?></strong>
                        <span>Fuera del calendario activo</span>
                    </div>
                    <div class="stats-icon canceled">
                        <i class="fas fa-ban"></i>
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
                        <h3 class="panel-title">Registro de planificaciones</h3>
                        <p class="panel-subtitle">Filtra por estado o pulsa una fila para resaltarla dentro del calendario.</p>
                    </div>
                    <div class="status-filter" id="status-filters">
                        <button type="button" class="filter-chip active" data-filter="all">Todas</button>
                        <button type="button" class="filter-chip" data-filter="Pendiente">Pendientes</button>
                        <button type="button" class="filter-chip" data-filter="Ejecutado">Ejecutadas</button>
                        <button type="button" class="filter-chip" data-filter="Anulado">Anuladas</button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-hover schedule-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha de entrega</th>
                                    <th>Creado por</th>
                                    <th>Fecha de creación</th>
                                    <th>Estado</th>
                                    <th style="width: 110px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
                            foreach ($schedules as $schedule) {
                                $fecha_timestamp = strtotime($schedule['delivery_day']);
                                $day_name = $days_weeks[date("l", $fecha_timestamp)];
                                $month_name = $months[date("M", $fecha_timestamp)];
                                $final_date = date("Y", $fecha_timestamp) . " - " . $month_name . " " . date("d", $fecha_timestamp) . " · " . $day_name;
                                $statusValue = (int) ($schedule['status'] ?? 0);
                                $statusData = $statusConfig[$statusValue] ?? $statusConfig[0];
?>
                                <tr data-status="<?= htmlspecialchars($statusData['label']) ?>" data-delivery-day="<?= htmlspecialchars($schedule['delivery_day']) ?>">
                                    <td><strong>#<?= htmlspecialchars($schedule['id']) ?></strong></td>
                                    <td>
                                        <span class="date-chip"><?= htmlspecialchars($final_date) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($schedule['created_by']) ?></td>
                                    <td><?= htmlspecialchars(date("Y-m-d", strtotime($schedule['created_at']))) ?></td>
                                    <td class="text-center">
                                        <span class="status-pill" style="background-color: <?= htmlspecialchars($statusData['color']) ?>;">
                                            <i class="<?= htmlspecialchars($statusData['icon']) ?>"></i>
                                            <?= htmlspecialchars($statusData['label']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <button type="button" class="btn btn-warning btn-sm" title="Cancelar" onclick="cancel_delivery(<?= htmlspecialchars($schedule['id']) ?>)">
                                                <i class="fas fa-recycle"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" title="Eliminar" onclick="delete_schedule(<?= htmlspecialchars($schedule['id']) ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
<?php
                            }
?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha de entrega</th>
                                    <th>Creado por</th>
                                    <th>Fecha de creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="panel-card calendar-panel">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Calendario lateral</h3>
                        <p class="panel-subtitle">Vista compacta para ubicar las entregas sin salir de la tabla.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="calendar-shell mb-3">
                        <div id="calendar"></div>
                    </div>

                    <div class="mini-tip mb-3">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Puedes pulsar una fila para mover el calendario a esa fecha. Los filtros también actualizan los eventos visibles.
                    </div>

                    <ul class="legend-list mb-4">
                        <li class="legend-item">
                            <span class="legend-dot" style="background: #dc3545;"></span>
                            <span class="legend-copy">Pendiente de entrega</span>
                        </li>
                        <li class="legend-item">
                            <span class="legend-dot" style="background: #28a745;"></span>
                            <span class="legend-copy">Entrega ejecutada</span>
                        </li>
                        <li class="legend-item">
                            <span class="legend-dot" style="background: #fd7e14;"></span>
                            <span class="legend-copy">Fecha anulada</span>
                        </li>
                    </ul>

                    <div>
                        <h4 class="panel-title mb-3" style="font-size: 1rem;">Próximas fechas</h4>
<?php if (!empty($upcomingSchedules)) { ?>
                        <ul class="upcoming-list">
<?php
                        foreach ($upcomingSchedules as $schedule) {
                            $statusValue = (int) ($schedule['status'] ?? 0);
                            $statusData = $statusConfig[$statusValue] ?? $statusConfig[0];
                            $fecha_timestamp = strtotime($schedule['delivery_day']);
?>
                            <li class="upcoming-item">
                                <div>
                                    <div class="upcoming-date"><?= htmlspecialchars(date('d', $fecha_timestamp) . ' ' . $months[date('M', $fecha_timestamp)]) ?></div>
                                    <div class="upcoming-meta"><?= htmlspecialchars($days_weeks[date('l', $fecha_timestamp)] . ' · ' . $schedule['created_by']) ?></div>
                                </div>
                                <span class="badge badge-<?= htmlspecialchars($statusData['badge']) ?> px-3 py-2"><?= htmlspecialchars($statusData['label']) ?></span>
                            </li>
<?php
                        }
?>
                        </ul>
<?php } else { ?>
                        <div class="upcoming-empty">No hay próximas fechas activas registradas.</div>
<?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="new_schedule" tabindex="-1" role="dialog" aria-labelledby="DateRangeModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="DateRangeModalLabel">Seleccionar rango de fechas</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="schedules.php?action=create">
				<div class="modal-body">
                    <div class="modal-note">Se tomarán los días <strong>viernes</strong> y <strong>sábados</strong> dentro del rango seleccionado para crear automáticamente las planificaciones de entrega.</div>
                    <div class="form-group">
                        <label for="start_date">Fecha inicio</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">Fecha fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- fullCalendar 2.2.5 -->
<script src="../templates/AdminLTE-3.0.5/plugins/moment/moment.min.js"></script>
<script src="../templates/AdminLTE-3.0.5/plugins/fullcalendar/main.min.js"></script>
<script src="../templates/AdminLTE-3.0.5/plugins/fullcalendar-daygrid/main.min.js"></script>
<script src="../templates/AdminLTE-3.0.5/plugins/fullcalendar-timegrid/main.min.js"></script>
<script src="../templates/AdminLTE-3.0.5/plugins/fullcalendar-interaction/main.min.js"></script>
<script src="../templates/AdminLTE-3.0.5/plugins/fullcalendar-bootstrap/main.min.js"></script>
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

            var rowNode = table.row(dataIndex).node();
            return $(rowNode).data('status') === activeStatusFilter;
        });

        var table = $('#table').DataTable({
            responsive: true,
            autoWidth: false,
            order: [[0, 'desc']],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            }
        });

        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });

        $message = "<?=$_SESSION['message'] ?? ''?>";
        $message_type = "<?=$_SESSION['message_type'] ?? ''?>";
        if ($message) {
            toastr[$message_type]($message);
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['message_type']); ?>
        }

        var Calendar = FullCalendar.Calendar;
        var calendarEl = document.getElementById('calendar');
        
        var calendar =new Calendar(calendarEl, {
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'bootstrap' ],
            themeSystem: 'bootstrap',
            initialView: 'dayGridMonth',
            height: 'auto',
            header    : {
                left  : 'prev,next today',
                center: 'title',
                right : 'dayGridMonth,timeGridWeek'
            },
            events: [
<?php
                    foreach ($schedules as $schedule) {
                        $statusValue = (int) ($schedule['status'] ?? 0);
                        $statusData = $statusConfig[$statusValue] ?? $statusConfig[0];
?>
                        {
                            id             : 'schedule-<?= (int) $schedule['id'] ?>',
                            title          : <?= json_encode($statusData['label']) ?>,
                            start          : '<?= $schedule['delivery_day'] ?>',
                            backgroundColor: '<?= $statusData['color'] ?>',
                            borderColor    : '<?= $statusData['color'] ?>',
                            textColor      : '#ffffff',
                            url            : 'schedules.php?action=delete&id=<?= (int) $schedule['id'] ?>',
                            extendedProps  : {
                                status: <?= json_encode($statusData['label']) ?>,
                                scheduleId: '<?= (int) $schedule['id'] ?>',
                                createdBy: <?= json_encode($schedule['created_by']) ?>
                            }
                        },
<?php
                    }
?>
            ],
            eventDidMount: function(info) {
                info.el.setAttribute('title', info.event.extendedProps.createdBy + ' · ' + info.event.title);
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                if (confirm('¿Está seguro de eliminar la planificación?')) {
                    window.location.href = info.event.url;
                }
            }
        });

        calendar.render();

        function applyFilter(filterValue) {
            activeStatusFilter = filterValue;
            table.draw();

            calendar.getEvents().forEach(function(event) {
                var matches = filterValue === 'all' || event.extendedProps.status === filterValue;
                event.setProp('display', matches ? 'auto' : 'none');
            });
        }

        $('#status-filters').on('click', '.filter-chip', function() {
            $('#status-filters .filter-chip').removeClass('active');
            $(this).addClass('active');
            applyFilter($(this).data('filter'));
        });

        $('#table tbody').on('click', 'tr', function() {
            var deliveryDay = $(this).data('delivery-day');
            $('#table tbody tr').removeClass('is-highlighted');
            $(this).addClass('is-highlighted');

            if (deliveryDay) {
                calendar.gotoDate(deliveryDay);
            }
        });
        
    });

    function delete_schedule(id) {
        if (confirm('¿Está seguro de eliminar la planificación?')) {
            window.location.href = 'schedules.php?action=delete&id=' + id;
        }
    }

    function cancel_delivery(id) {
        if (confirm('¿Está seguro de cancelar la entrega?')) {
            window.location.href = 'schedules.php?action=cancel&id=' + id;
        }
    }
</script>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base_modules.php';
?>
