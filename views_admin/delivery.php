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
$students = $studentsController->index();
$schedules = $schedulesController->index();
$deliveries = $deliveryController->index();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $deliveryController->create($_POST);
    header('Location: delivery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $deliveryController->delete($_GET['id']);
    header('Location: delivery.php');
}

$title = "Reparto de meriendas";
ob_start();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<style>
    #title {
        background-color: white;
        border: 2px solid gray;
        border-radius: 10px;
        margin-top: 5px;
    }

    .selected {
        background-color: #bfbfbf;
    }

    #table_delivery_scheduling:hover {
        cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-md-12" id="title">
        <h3 class="page-header" style="padding-top: 5px;">
            Entrega de Meriendas 
            <button href="snacks.php" class="btn-sm btn-primary" style="float: right; margin-top:3px;" id="return">
                <i class="fas fa-arrow-left"></i>  Regresar
            </button>
            <button type="button" class="btn-sm btn-primary" data-toggle="modal" data-target="#new_delivery" style="float: right; margin-top:3px; margin-right: 5px;">
                <i class="fas fa-save"></i>  Nueva Entrega
            </button>
        </h3>
        
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Programa academico</th>
                    <th>Estudiante</th>
                    <th>Día de entrega</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
<?php 
                foreach ($deliveries as $delivery) 
                {
?>
                    <tr>
                        <td><?=$delivery['id']?></td>
                        <td><?=$delivery['academic_program']?></td>
                        <td><?=$delivery['student']?></td>
                        <td><?=date("Y-m-d - l",strtotime($delivery['delivery_day']))?></td>
                        <td><?=$delivery['delivery_status']?></td>
                        <td style="text-align:center;">
                            <a href="delivery.php?action=delete&id=<?=$delivery['id']?>" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
<?php
                }
?>
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Programa academico</th> 
                    <th>Estudiante</th>
                    <th>Día de entrega</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                </tr>
            </tfoot>
        </table>
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
                    <div class="row">
                        <div class="col-md-9 col-lg-9">
                            <h5>Estudiantes</h5>
                            <table id="table_students" class="display table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Cedula</th>
                                        <th>Nombre</th>
                                        <th>Programa academico</th>
                                        <th>Semestre</th>
                                        <th style="text-align:center;">
                                            <input type="checkbox" id="check_all_students">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
<?php 
                                    foreach ($students as $student) 
                                    {
?>
                                        <tr>
                                            <td><?=$student['document_number']?></td>
                                            <td><?=$student['first_name']?> <?=$student['last_name']?></td>
                                            <td><?=$student['name']?></td>
                                            <td><?=$student['semester']?></td>
                                            <td style="text-align:center;">
                                                <input type="checkbox" name="students[]" value="<?=$student['id']?>">
                                            </td>
                                        </tr>
<?php 
                                    } 
?>
                                </tbody>
                                <tfoot class="thead-dark">
                                    <tr>
                                        <th>Cedula</th>
                                        <th>Nombre</th>
                                        <th>Programa academico</th>
                                        <th>Semestre</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <h5>Fechas de entrega</h5>
                            <table id="table_delivery_scheduling" class="display table table-bordered" style="width: 100%;">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="display: none;"></th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php 
                                    foreach ($schedules as $schedule) 
                                    {
?>
                                        <tr>
                                            <td style="display: none;"><?=$schedule['id']?></td>
                                            <td><?=$schedule['delivery_day']?></td>
                                        </tr>
<?php
                                    }
?>
                                </tbody>
                                <tfoot class="thead-dark">
                                    <tr>
                                        <th>Fecha</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <input type="hidden" name="delivery_scheduling_id" id="delivery_scheduling_id">
                        </div>
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
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"></script>

<script>
    $(document).ready(function() {
        $('#table').DataTable({
            "order": [[ 2, "desc" ]]
        });

        /* TABLA DE ESTUDIANTES */
        new DataTable(document.querySelector('#table_students'), {
            "order": [[ 1, "asc" ]],
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    if (column.index() == 2 || column.index() == 3) {
                        var select = $('<select><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function(d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>');
                            });
                    }
                });
            }
        });
        /* CHECKBOX DE SELECCIONAR TODOS LOS ESTUDIANTES */
        $('#check_all_students').click(function() {
            if ($(this).is(':checked')) {
                $('#table_students input[type="checkbox"]').prop('checked', true);
            } else {
                $('#table_students input[type="checkbox"]').prop('checked', false);
            }
        });

        /* TABLA DE FECHAS DE ENTREGA */
        const table_delivery_scheduling = new DataTable('#table_delivery_scheduling', {
            select: {
                style: 'single',
                selector: 'tr'
            },
            searching: false
        });
        table_delivery_scheduling.on('click', 'tbody tr', (e) => {
            let classList = e.currentTarget.classList;
            if (classList.contains('selected')) 
            {
                classList.remove('selected');
                $('#delivery_scheduling_id').val('');
            }
            else 
            {
                table_delivery_scheduling.rows('.selected').nodes().each((row) => row.classList.remove('selected'));
                classList.add('selected');
                let id = table_delivery_scheduling.row(e.currentTarget).data()[0];
                $('#delivery_scheduling_id').val(id);
            }
        });

        /* BOTON DE REGRESAR */
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

    });
</script>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base_modules.php';
?>
