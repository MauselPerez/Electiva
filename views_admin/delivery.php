<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}

require_once '../controllers/students_controller.php';
require_once '../controllers/schedules_controller.php';
require_once '../controllers/delivery_controller.php';

$studentsController = new StudentsController();
$schedulesController = new SchedulesController();
$deliveryController = new DeliveryController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $deliveryController->create($_POST);
    header('Location: delivery.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $deliveryController->delete($_GET['id']);
    header('Location: delivery.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'complete_schedule') {
    $deliveryController->completeSchedule($_GET['id']);
    header('Location: delivery.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'reopen_schedule') {
    $deliveryController->reopenSchedule($_GET['id']);
    header('Location: delivery.php');
    exit();
}

$students = $studentsController->index();
$pendingSchedules = $schedulesController->indexPending();
$deliveries = $deliveryController->index();
$deliverySchedules = $deliveryController->getScheduleOverview();

$activeStudents = array_filter($students, function ($student) {
    return !isset($student['is_active']) || (int) $student['is_active'] === 1;
});

$daysMap = [
    'Sunday' => 'Domingo',
    'Monday' => 'Lunes',
    'Tuesday' => 'Martes',
    'Wednesday' => 'Miércoles',
    'Thursday' => 'Jueves',
    'Friday' => 'Viernes',
    'Saturday' => 'Sábado'
];

$monthsMap = [
    'Jan' => 'Ene',
    'Feb' => 'Feb',
    'Mar' => 'Mar',
    'Apr' => 'Abr',
    'May' => 'May',
    'Jun' => 'Jun',
    'Jul' => 'Jul',
    'Aug' => 'Ago',
    'Sep' => 'Sep',
    'Oct' => 'Oct',
    'Nov' => 'Nov',
    'Dec' => 'Dic'
];

$statusConfig = [
    0 => ['label' => 'Pendiente', 'color' => '#dc3545', 'badge' => 'danger', 'icon' => 'far fa-clock'],
    1 => ['label' => 'Ejecutado', 'color' => '#28a745', 'badge' => 'success', 'icon' => 'fas fa-check-circle'],
    2 => ['label' => 'Anulado', 'color' => '#fd7e14', 'badge' => 'warning', 'icon' => 'fas fa-ban']
];

$stats = [
    'deliveries' => count($deliveries),
    'pending_days' => 0,
    'executed_days' => 0,
    'active_students' => count($activeStudents)
];

foreach ($deliverySchedules as $schedule) {
    if ((int) $schedule['status'] === 1) {
        $stats['executed_days']++;
    } else {
        $stats['pending_days']++;
    }
}

$title = "Reparto de meriendas";
ob_start();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<style>
    .delivery-page {
        padding: 18px 8px 28px 0;
    }

    .delivery-hero {
        background: linear-gradient(135deg, #28313b 0%, #485461 60%, #5f7a96 100%);
        border-radius: 20px;
        box-shadow: 0 18px 42px rgba(28, 38, 49, 0.18);
        color: #fff;
        margin: 10px 0 18px;
        overflow: hidden;
        position: relative;
    }

    .delivery-hero::after {
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 36%);
        content: "";
        inset: 0;
        pointer-events: none;
        position: absolute;
    }

    .delivery-hero-body {
        padding: 24px 26px;
        position: relative;
        z-index: 1;
    }

    .delivery-title {
        font-size: 1.85rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .delivery-text {
        color: rgba(255, 255, 255, 0.86);
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
        color: #28313b;
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

    .metric-card.primary::before { background: #28313b; }
    .metric-card.success::before { background: #28a745; }
    .metric-card.danger::before { background: #dc3545; }
    .metric-card.info::before { background: #1f8ec9; }

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
        color: #28313b;
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

    .metric-icon.primary { background: #28313b; }
    .metric-icon.success { background: #28a745; }
    .metric-icon.danger { background: #dc3545; }
    .metric-icon.info { background: #1f8ec9; }

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
        color: #28313b;
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

    .helper-note {
        background: #fff8e8;
        border: 1px solid #ffe3a0;
        border-radius: 14px;
        color: #7b5b11;
        font-size: 0.9rem;
        padding: 12px 14px;
    }

    .delivery-table thead th,
    .delivery-table tfoot th,
    .schedule-picker thead th,
    .schedule-picker tfoot th {
        background: #343a40;
        border-color: #343a40;
        color: #fff;
    }

    .delivery-table tbody tr,
    .schedule-picker tbody tr {
        transition: background-color 0.15s ease, transform 0.15s ease;
    }

    .delivery-table tbody tr:hover,
    .schedule-picker tbody tr:hover {
        background: #f7fbff;
        transform: translateX(2px);
    }

    .date-chip {
        background: #eef4fb;
        border-radius: 999px;
        color: #28313b;
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
        min-width: 116px;
        padding: 8px 12px;
    }

    .count-pill {
        background: #edf7ef;
        border-radius: 999px;
        color: #216e39;
        display: inline-block;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 10px;
    }

    .selected-row {
        background: #eef6ff !important;
        box-shadow: inset 4px 0 0 #1f8ec9;
    }

    .schedule-list {
        display: grid;
        gap: 12px;
    }

    .schedule-item {
        background: #f8fafc;
        border: 1px solid #e8eef4;
        border-radius: 16px;
        padding: 14px;
    }

    .schedule-item-head {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .schedule-item-title {
        color: #28313b;
        font-size: 0.96rem;
        font-weight: 700;
        margin: 0;
    }

    .schedule-item-meta {
        color: #6c7a89;
        font-size: 0.85rem;
        margin-bottom: 10px;
    }

    .schedule-item-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .schedule-item-empty {
        background: #f8fafc;
        border: 1px dashed #d6e0e9;
        border-radius: 16px;
        color: #6c7a89;
        padding: 16px;
        text-align: center;
    }

    @media (max-width: 991.98px) {
        .delivery-hero-body {
            padding: 22px 18px;
        }

        .hero-actions {
            justify-content: flex-start;
        }
    }
</style>

<div class="container-fluid delivery-page">
    <div class="delivery-hero">
        <div class="row align-items-center no-gutters delivery-hero-body">
            <div class="col-lg-8">
                <div class="delivery-title">Gestión de entregas de meriendas</div>
                <p class="delivery-text">Registra las entregas por estudiante y cierra la jornada solo cuando el día realmente haya terminado. Así se separa la planificación del cierre operativo y los reportes reflejan mejor la realidad.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button type="button" class="btn-sm btn-success" data-toggle="modal" data-target="#new_delivery">
                        <i class="fas fa-plus mr-1"></i> Nueva entrega
                    </button>
                    <button class="btn-sm btn-warning" id="return">
                        <i class="fas fa-arrow-left mr-1"></i> Regresar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="metric-card primary">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Entregas registradas</div>
                        <span class="metric-number"><?= $stats['deliveries'] ?></span>
                        <div class="metric-caption">Registros individuales acumulados</div>
                    </div>
                    <div class="metric-icon primary"><i class="fas fa-box-open"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="metric-card danger">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Jornadas pendientes</div>
                        <span class="metric-number"><?= $stats['pending_days'] ?></span>
                        <div class="metric-caption">Días abiertos para seguir cargando entregas</div>
                    </div>
                    <div class="metric-icon danger"><i class="far fa-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="metric-card success">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Jornadas ejecutadas</div>
                        <span class="metric-number"><?= $stats['executed_days'] ?></span>
                        <div class="metric-caption">Días cerrados manualmente por el usuario</div>
                    </div>
                    <div class="metric-icon success"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="metric-card info">
                <div class="metric-body">
                    <div>
                        <div class="metric-title">Estudiantes activos</div>
                        <span class="metric-number"><?= $stats['active_students'] ?></span>
                        <div class="metric-caption">Base vigente para la meta mensual</div>
                    </div>
                    <div class="metric-icon info"><i class="fas fa-user-graduate"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3 class="panel-title">Entregas registradas</h3>
                        <p class="panel-subtitle">Cada registro representa una entrega individual a un estudiante en una fecha planificada.</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="helper-note mb-3">
                        Primero registra las entregas del día; después, desde el panel lateral, marca la jornada como ejecutada cuando hayas terminado de cargar ese día.
                    </div>
                    <div class="table-responsive">
                        <table id="table_deliveries" class="table table-bordered table-hover delivery-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Programa académico</th>
                                    <th>Estudiante</th>
                                    <th>Día de entrega</th>
                                    <th>Estado</th>
                                    <th style="width: 72px;">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($deliveries as $delivery) {
    $deliveryTimestamp = strtotime($delivery['delivery_day']);
    $deliveryLabel = date('Y', $deliveryTimestamp) . ' - ' . ($monthsMap[date('M', $deliveryTimestamp)] ?? date('M', $deliveryTimestamp)) . ' ' . date('d', $deliveryTimestamp) . ' · ' . ($daysMap[date('l', $deliveryTimestamp)] ?? date('l', $deliveryTimestamp));
?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($delivery['id']) ?></strong></td>
                                    <td><?= htmlspecialchars($delivery['academic_program']) ?></td>
                                    <td><?= htmlspecialchars($delivery['student']) ?></td>
                                    <td><span class="date-chip"><?= htmlspecialchars($deliveryLabel) ?></span></td>
                                    <td><span class="status-pill" style="background-color:#28a745;"><i class="fas fa-check"></i> Entregado</span></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="delete_delivery(<?= (int) $delivery['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
<?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Programa académico</th>
                                    <th>Estudiante</th>
                                    <th>Día de entrega</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
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
                        <h3 class="panel-title">Control de jornadas</h3>
                        <p class="panel-subtitle">Desde aquí decides cuándo un día queda realmente ejecutado.</p>
                    </div>
                </div>
                <div class="panel-body">
<?php if (!empty($deliverySchedules)) { ?>
                    <div class="schedule-list">
<?php foreach ($deliverySchedules as $schedule) {
    $statusValue = (int) ($schedule['status'] ?? 0);
    $statusData = $statusConfig[$statusValue] ?? $statusConfig[0];
    $scheduleTimestamp = strtotime($schedule['delivery_day']);
    $scheduleLabel = date('Y', $scheduleTimestamp) . ' - ' . ($monthsMap[date('M', $scheduleTimestamp)] ?? date('M', $scheduleTimestamp)) . ' ' . date('d', $scheduleTimestamp) . ' · ' . ($daysMap[date('l', $scheduleTimestamp)] ?? date('l', $scheduleTimestamp));
?>
                        <div class="schedule-item">
                            <div class="schedule-item-head">
                                <div class="schedule-item-title"><?= htmlspecialchars($scheduleLabel) ?></div>
                                <span class="status-pill" style="background-color: <?= htmlspecialchars($statusData['color']) ?>; min-width: auto;">
                                    <i class="<?= htmlspecialchars($statusData['icon']) ?>"></i>
                                    <?= htmlspecialchars($statusData['label']) ?>
                                </span>
                            </div>
                            <div class="schedule-item-meta">Creado por <?= htmlspecialchars($schedule['created_by']) ?> · <?= (int) $schedule['delivered_students'] ?> entregas registradas</div>
                            <div class="schedule-item-actions">
<?php if ($statusValue === 0) { ?>
                                <button type="button" class="btn btn-success btn-sm" onclick="complete_schedule(<?= (int) $schedule['id'] ?>)">
                                    <i class="fas fa-check-circle mr-1"></i> Marcar día ejecutado
                                </button>
<?php } elseif ($statusValue === 1) { ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="reopen_schedule(<?= (int) $schedule['id'] ?>)">
                                    <i class="fas fa-undo mr-1"></i> Reabrir jornada
                                </button>
<?php } ?>
                            </div>
                        </div>
<?php } ?>
                    </div>
<?php } else { ?>
                    <div class="schedule-item-empty">No hay jornadas activas registradas.</div>
<?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="new_delivery" tabindex="-1" role="dialog" aria-labelledby="DateRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DateRangeModalLabel">Registrar entrega de meriendas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="delivery.php?action=create">
                <div class="modal-body">
                    <div class="helper-note mb-3">
                        Selecciona una jornada pendiente y marca los estudiantes que recibieron la merienda. El día seguirá pendiente hasta que lo cierres manualmente desde el panel de jornadas.
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-lg-8">
                            <h5>Estudiantes activos</h5>
                            <table id="table_students" class="display table table-bordered delivery-table">
                                <thead>
                                    <tr>
                                        <th>Cédula</th>
                                        <th>Nombre</th>
                                        <th>Programa académico</th>
                                        <th>Semestre</th>
                                        <th style="text-align:center;">
                                            <input type="checkbox" id="check_all_students">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
<?php foreach ($activeStudents as $student) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['document_number']) ?></td>
                                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                        <td><?= htmlspecialchars($student['name']) ?></td>
                                        <td><?= htmlspecialchars($student['semester']) ?></td>
                                        <td style="text-align:center;">
                                            <input type="checkbox" name="students[]" value="<?= (int) $student['id'] ?>">
                                        </td>
                                    </tr>
<?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Cédula</th>
                                        <th>Nombre</th>
                                        <th>Programa académico</th>
                                        <th>Semestre</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <h5>Jornadas pendientes</h5>
                            <table id="table_delivery_scheduling" class="display table table-bordered schedule-picker" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="display: none;">ID</th>
                                        <th>Fecha</th>
                                        <th>Creado por</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php foreach ($pendingSchedules as $schedule) {
    $scheduleTimestamp = strtotime($schedule['delivery_day']);
    $scheduleLabel = date('Y-m-d', $scheduleTimestamp) . ' · ' . ($daysMap[date('l', $scheduleTimestamp)] ?? date('l', $scheduleTimestamp));
?>
                                    <tr>
                                        <td style="display: none;"><?= (int) $schedule['id'] ?></td>
                                        <td><?= htmlspecialchars($scheduleLabel) ?></td>
                                        <td><?= htmlspecialchars($schedule['created_by']) ?></td>
                                    </tr>
<?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="display: none;">ID</th>
                                        <th>Fecha</th>
                                        <th>Creado por</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <input type="hidden" name="delivery_scheduling_id" id="delivery_scheduling_id">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar entregas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table_deliveries').DataTable({
            responsive: true,
            autoWidth: false,
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            }
        });

        new DataTable(document.querySelector('#table_students'), {
            responsive: true,
            autoWidth: false,
            order: [[1, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            },
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if (column.index() === 2 || column.index() === 3) {
                        var select = $('<select><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                    }
                });
            }
        });

        const scheduleTable = new DataTable('#table_delivery_scheduling', {
            responsive: true,
            autoWidth: false,
            paging: false,
            searching: false,
            info: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
            }
        });

        $('#table_delivery_scheduling tbody').on('click', 'tr', function() {
            $('#table_delivery_scheduling tbody tr').removeClass('selected-row');
            $(this).addClass('selected-row');
            $('#delivery_scheduling_id').val(scheduleTable.row(this).data()[0]);
        });

        $('#check_all_students').click(function() {
            $('#table_students input[type="checkbox"]').prop('checked', $(this).is(':checked'));
        });

        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });

        $message = "<?= $_SESSION['message'] ?? '' ?>";
        $message_type = "<?= $_SESSION['message_type'] ?? '' ?>";
        if ($message) {
            toastr[$message_type]($message);
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['message_type']); ?>
        }
    });

    function delete_delivery(id) {
        if (confirm('¿Está seguro de eliminar la entrega?')) {
            window.location.href = 'delivery.php?action=delete&id=' + id;
        }
    }

    function complete_schedule(id) {
        if (confirm('¿Marcar esta jornada como ejecutada?')) {
            window.location.href = 'delivery.php?action=complete_schedule&id=' + id;
        }
    }

    function reopen_schedule(id) {
        if (confirm('¿Reabrir esta jornada para seguir registrando entregas?')) {
            window.location.href = 'delivery.php?action=reopen_schedule&id=' + id;
        }
    }
</script>
<?php
$content = ob_get_clean();

include '../templates/base_modules.php';
?>