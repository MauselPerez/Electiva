<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}
require_once '../controllers/academic_controller.php';

$controller = new AcademicProgramsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create($_POST);
    header('Location: academic_programs.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller->update($_POST['id'], $_POST);
    header('Location: academic_programs.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
    header('Location: academic_programs.php');
}

$academic_programs = $controller->index();
$title = "Programas académicos";
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
</style>
<div class="row">
    <div class="col-md-12" id="title">
        <h3 class="page-header" style="padding-top: 5px;">
            Estudiantes 
            <button class="btn-sm btn-primary" style="float: right; margin-top:3px;" id="return">
                <i class="fas fa-arrow-left"></i>  Regresar
            </button>
            <button type="button" class="btn-sm btn-primary" data-toggle="modal" data-target="#new_student" style="float: right; margin-top:3px; margin-right: 5px;">
                <i class="fas fa-save"></i>  Nuevo Programa
            </button>
        </h3>
        
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Programa</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
<?php 
            if (count($academic_programs) > 0)
            {

                foreach ($academic_programs as $academic_program)
                { 
?>
                    <tr>
                        <td><?=htmlspecialchars($academic_program['id']); ?></td>
                        <td><?=htmlspecialchars($academic_program['name']); ?></td>
                        <td style="text-align:center;">
                            <?php if ($academic_program['is_active'] == 1) { ?>
                                <span class="badge badge-success" style="padding: 8px;">Activo</span>
                            <?php } else { ?>
                                <span class="badge badge-danger"  style="padding: 8px;">Inactivo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <!-- Modal de editar -->
                            <button type="button" class="btn btn-primary btn-sm" onclick="show_edit(this)">
                                <i class="fa fa-edit"></i>
                            </button>
                            <!-- Boton de elminar -->
                            <button type="button" class="btn btn-danger btn-sm" onclick="delete_program(<?=htmlspecialchars($academic_program['id']); ?>)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
<?php 
                } 
            }
            else
            {
?>
                <tr>
                    <td colspan="8" style="text-align: center;"><span style="background-color:#cccc00; padding: 10px; color:#ffffff; font-size:large;"><b>No hay programas registrados</b></span></td>
                </tr>
<?php
            }
?>
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Programa</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal" id="new_student" tabindex="-1" role="dialog" aria-labelledby="DateRangeModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="DateRangeModalLabel">Registrar Programa academico</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="academic_programs.php?action=create" autocomplete="off">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="name">Programa academico</label>
                        <input type="text" class="form-control" id="name" name="name" required>
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

<div id="edit_program" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Editar Programa academico</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="academic_programs.php?action=update">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Programa academico</label>
                        <input type="text" class="form-control" id="name_edit" name="name_edit" required>
                    </div>
                    <input type="hidden" name="id" id="id">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>  
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
    });

    function show_edit(element) {
        var id = $(element).closest('tr').find('td').eq(0).text();
        var name = $(element).closest('tr').find('td').eq(1).text();
        $('#id').val(id);
        $('#name_edit').val(name);

        $('#edit_program').modal('show');
    }

    function delete_program(id) {
        var confirm_delete = confirm('¿Está seguro de eliminar este programa?');
        if (confirm_delete) {
            window.location.href = 'academic_programs.php?action=delete&id=' + id;
        }
    }
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>
