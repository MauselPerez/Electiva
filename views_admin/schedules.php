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
    #title {
        background-color: white;
        border: 2px solid gray;
        border-radius: 10px;
        margin-top: 5px;
    }
</style>
<div class="row">
    <div class="col-md-12" id="title">
        <h3 class="page-header" style="padding-top: 5px;">
            Cronograma de entregas 
            <button class="btn-sm btn-primary" style="float: right; margin-top:3px;" id="return">
                <i class="fas fa-arrow-left"></i>  Regresar
            </button>
            <button type="button" class="btn-sm btn-primary" data-toggle="modal" data-target="#new_schedule" style="float: right; margin-top:3px; margin-right:5px;">
                <i class="fas fa-save"></i>  Nueva Planificacion
            </button>
        </h3>
        
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha de entrega</th>
                    <th>Creado por</th>
                    <th>Fecha de creacion</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
<?php
                foreach ($schedules as $schedule) 
                {
                    switch ($schedule['status']) {
                        case 1:
                            $status = 'Ejecutado';
                            $background = 'green';
                            break;
                        case 2:
                            $status = 'Anualado';
                            $background = 'orange';
                            break;
                        default:
                            $status = 'Pendiente';
                            $background = 'red';
                            break;
                    }
?>
                    <tr>
                        <td><?= $schedule['id'] ?></td>
                        <td><?= date("Y-M-d - l", strtotime($schedule['delivery_day'])) ?></td>
                        <td><?= $schedule['created_by'] ?></td>
                        <td><?= date("Y-m-d",strtotime($schedule['created_at'])) ?></td>
                        <td style="background-color: <?= $background ?>; color: white; text-align: center;"><?= $status ?></td>
                        <td style="text-align: center;">
                            <button type="button" class="btn btn-warning btn-sm" onclick="cancel_delivery(<?=htmlspecialchars($schedule['id']); ?>)">
                                <i class='fas fa-recycle'></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="delete_schedule(<?=htmlspecialchars($schedule['id']); ?>)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
<?php
                }
?>
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha de entrega</th>
                    <th>Creado por</th>
                    <th>Fecha de creacion</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12" id="title">
        <h3 class="page-header" style="padding-top: 5px;">
            Calendario de entregas
        </h3>
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <div id="calendar"></div>
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
                    <p style="color: red; font-size:medium;">Se tomaran los días <b>viernes</b> y <b>sabados</b> dentro del rango de fecha seleccionado para la creación de los registros de entrega.</p>
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
        $('#table').DataTable({   
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

        var date = new Date()
        var d    = date.getDate(),
            m    = date.getMonth(),
            y    = date.getFullYear()

        var Calendar = FullCalendar.Calendar;
        var Draggable = FullCalendarInteraction.Draggable;
        var calendarEl = document.getElementById('calendar');
        
        var calendar =new Calendar(calendarEl, {
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'bootstrap' ],
            themeSystem: 'bootstrap',
            header    : {
                left  : 'prev,next today',
                center: 'title',
                right : 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
<?php
                    foreach ($schedules as $schedule) 
                    {
                        switch ($schedule['status']) {
                            case 1:
                                $status = 'Ejecutado';
                                $background = 'green';
                                break;
                            case 2:
                                $status = 'Anulado';
                                $background = 'orange';
                                break;
                            default:
                                $status = 'Pendiente';
                                $background = 'red';
                                break;
                        }
?>
                        {
                            title          : '<?= $status ?>',
                            start          : '<?= $schedule['delivery_day'] ?>',
                            backgroundColor: '<?= $background ?>',
                            borderColor    : '<?= $background ?>',
                            url            : 'schedules.php?action=delete&id=<?= $schedule['id'] ?>'
                        },
<?php
                    }
?>
            ],
            editable  : true,
            droppable : true,
            eventClick: function(info) {
                if (confirm('¿Está seguro de eliminar la planificación?')) {
                    window.location.href = info.event.url;
                }
            }
        });

        calendar.render();
        
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
