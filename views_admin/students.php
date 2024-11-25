<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}
require_once '../controllers/students_controller.php';

$controller = new StudentsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create($_POST);
    header('Location: students.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller->update($_POST['id'], $_POST);
    header('Location: students.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
    header('Location: students.php');
}

$students = $controller->index();
$academic_programs = $controller->getAllPrograms();
$title = "Estudiantes";
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
                <i class="fas fa-save"></i>  Nuevo Estudiante
            </button>
        </h3>
        
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Cedula</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Programa</th>
                    <th>Semestre</th>
                    <th>Email</th>
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
<?php 
            if (count($students) > 0)
            {

                foreach ($students as $student)
                { 
?>
                    <tr>
                        <td><?=htmlspecialchars($student['id']); ?></td>
                        <td><?=htmlspecialchars($student['document_number']); ?></td>
                        <td><?=htmlspecialchars($student['first_name']); ?></td>
                        <td><?=htmlspecialchars($student['last_name']); ?></td>
                        <td><?=htmlspecialchars($student['name']); ?></td>
                        <td><?=htmlspecialchars($student['semester']); ?></td>
                        <td><?=htmlspecialchars($student['email']); ?></td>
                        <td>
                            <!-- Modal de editar -->
                            <button type="button" class="btn btn-primary btn-sm" onclick="show_edit(this)">
                                <i class="fa fa-edit"></i>
                            </button>
                            <!-- Boton de elminar -->
                            <button type="button" class="btn btn-danger btn-sm" onclick="delete_student(<?=htmlspecialchars($student['id']); ?>)">
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
                    <td colspan="8" style="text-align: center;"><span style="background-color:#cccc00; padding: 10px; color:#ffffff; font-size:large;"><b>No hay estudiantes registrados</b></span></td>
                </tr>
<?php
            }
?>
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Cedula</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Programa</th>
                    <th>Semestre</th>
                    <th>Email</th>
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
				<h5 class="modal-title" id="DateRangeModalLabel">Registrar estudiante</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST" action="students.php?action=create" autocomplete="off">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="program_id">Programa academico</label>
                        <select name="program_id" id="program_id" class="form-control">
                            <option value="">Seleccione un programa</option>
<?php
                            foreach ($academic_programs as $program)
                            {
?>
                                <option value="<?=htmlspecialchars($program['id']); ?>"><?=htmlspecialchars($program['name']); ?></option>
<?php
                            }
?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="document_number">Cedula</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">Nombres</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Apellidos</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semestre</label>
                        <select name="semester" id="semester" class="form-control" required>
                            <option value="">Seleccione un semestre</option>
                            <option value="1">I</option>
                            <option value="2">II</option>
                            <option value="3">III</option>
                            <option value="4">IV</option>
                            <option value="5">V</option>
                            <option value="6">VI</option>
                            <option value="7">VII</option>
                            <option value="8">VIII</option>
                            <option value="9">IX</option>
                            <option value="10">X</option>
                        </select>
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

<div id="edit_student" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Editar estudiante</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST" action="students.php?action=update">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="program_id_edit">Programa academico</label>
                        <select name="program_id_edit" id="program_id_edit" class="form-control">
                            <option value="">Seleccione un programa</option>
<?php
                            foreach ($academic_programs as $program)
                            {
?>
                                <option value="<?=htmlspecialchars($program['id']); ?>"><?=htmlspecialchars($program['name']); ?></option>
<?php
                            }
?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="document_number_edit">Cedula</label>
                        <input type="text" class="form-control" id="document_number_edit" name="document_number_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name_edit">Nombres</label>
                        <input type="text" class="form-control" id="first_name_edit" name="first_name_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name_edit">Apellidos</label>
                        <input type="text" class="form-control" id="last_name_edit" name="last_name_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="email_edit">Email</label>
                        <input type="text" class="form-control" id="email_edit" name="email_edit" required>
                    </div>
                    <div class="form-group">
                        <label for="semester_edit">Semestre</label>
                        <select name="semester_edit" id="semester_edit" class="form-control" required>
                            <option value="">Seleccione un semestre</option>
                            <option value="1">I</option>
                            <option value="2">II</option>
                            <option value="3">III</option>
                            <option value="4">IV</option>
                            <option value="5">V</option>
                            <option value="6">VI</option>
                            <option value="7">VII</option>
                            <option value="8">VIII</option>
                            <option value="9">IX</option>
                            <option value="10">X</option>
                        </select>
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
        var program_name = $(element).closest('tr').find('td').eq(4).text();
        var document_number = $(element).closest('tr').find('td').eq(1).text();
        var first_name = $(element).closest('tr').find('td').eq(2).text();
        var last_name = $(element).closest('tr').find('td').eq(3).text();
        var email = $(element).closest('tr').find('td').eq(6).text();
        var semester = $(element).closest('tr').find('td').eq(5).text();
        $('#document_number_edit').val(document_number);
        $('#first_name_edit').val(first_name);
        $('#last_name_edit').val(last_name);
        $('#email_edit').val(email);
        $('#semester_edit').val(semester);
        $('#id').val(id);
        
        $('#program_id_edit option').filter(function() {
            return $(this).text() == program_name;
        }).prop('selected', true);

        $('#edit_student').modal('show');
    }

    function delete_student(id) {
        var confirm_delete = confirm('¿Está seguro de eliminar este estudiante?');
        if (confirm_delete) {
            window.location.href = 'students.php?action=delete&id=' + id;
        }
    }
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>
