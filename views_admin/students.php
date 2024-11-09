<?php
// Incluir controlador de estudiantes
include_once '../controllers/students_controller.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$title = "Estudiantes";
$studentsController = new StudentsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStudent = [
        'id_programa' => $_POST['id_programa'],
        'n_documento' => $_POST['n_documento'],
        'nombre' => $_POST['nombre'],
        'apellido' => $_POST['apellido'],
        'email' => $_POST['email'],
        'telefono' => $_POST['telefono']
    ];
    
    $result = $studentsController->createStudent($newStudent);
    var_dump($result); die;

    header("Location: students.php");
    exit();
}

// Obtener la lista de estudiantes
$students = $studentsController->getAllStudents();
ob_start();
?>
<style>
    /* estilos */
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
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
<?php 
                foreach ($students as $student)
                { 
?>
                    <tr>
                        <td><?=htmlspecialchars($student['estudiante_id']); ?></td>
                        <td><?=htmlspecialchars($student['n_documento']); ?></td>
                        <td><?=htmlspecialchars($student['nombre']); ?></td>
                        <td><?=htmlspecialchars($student['apellido']); ?></td>
                        <td><?=htmlspecialchars($student['programa']); ?></td>
                        <td>
                            <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                        </td>
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
			<form method="POST" action="">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="id_programa">Programa academico</label>
                        <select name="id_programa" id="id_programa" class="form-control">
                            <option value="1">Ingenieria de sistemas</option>
                            <option value="2">Ingenieria industrial</option>
                            <option value="3">Ingenieria civil</option>
                            <option value="4">Ingenieria mecanica</option>
                            <option value="5">Ingenieria electronica</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="n_documento">Cedula</label>
                        <input type="text" class="form-control" id="n_documento" name="n_documento" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombres</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellidos</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Telefono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
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

<script>
    $(document).ready(function() {
        $('#table').DataTable();
        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });
    });
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>
