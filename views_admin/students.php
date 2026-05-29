<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}

require_once '../controllers/students_controller.php';

$controller = new StudentsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create($_POST, $_FILES);
    header('Location: students.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller->update($_POST['id'], $_POST, $_FILES);
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
    .module-page {
        padding: 18px 8px 28px 0;
    }

    .module-hero {
        background: linear-gradient(135deg, #28313b 0%, #485461 60%, #5f7a96 100%);
        border-radius: 20px;
        box-shadow: 0 18px 42px rgba(28, 38, 49, 0.18);
        color: #fff;
        margin: 10px 0 18px;
        overflow: hidden;
        position: relative;
    }

    .module-hero::after {
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 36%);
        content: "";
        inset: 0;
        pointer-events: none;
        position: absolute;
    }

    .module-hero-body {
        padding: 24px 26px;
        position: relative;
        z-index: 1;
    }

    .module-title {
        font-size: 1.85rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .module-text {
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

    .table-shell {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-shell table {
        min-width: 1120px;
    }

    @media (max-width: 575.98px) {
        .module-hero-body {
            padding: 22px 18px;
        }

        .module-title {
            font-size: 1.45rem;
        }

        .hero-actions {
            justify-content: flex-start;
        }

        .table-shell table {
            min-width: 980px;
        }
    }
</style>

<div class="container-fluid module-page">
    <div class="module-hero">
        <div class="row align-items-center no-gutters module-hero-body">
            <div class="col-lg-8">
                <div class="module-title">Estudiantes</div>
                <p class="module-text">Administra fotos, cédulas y datos académicos con una vista uniforme en todo el sistema.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button type="button" class="btn btn-success hero-btn" data-toggle="modal" data-target="#new_student">
                        <i class="fas fa-save mr-1"></i> Nuevo Estudiante
                    </button>
                    <button class="btn btn-warning hero-btn" id="return">
                        <i class="fas fa-arrow-left mr-1"></i> Regresar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Listado de estudiantes</h3>
                <p class="panel-subtitle">Consulta la información académica y accede al carnet QR.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-shell">
                <table id="table" class="display table table-bordered" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Cedula</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Programa</th>
                            <th>Semestre</th>
                            <th>Email</th>
                            <th style="width: 13%;"></th>
                        </tr>
                    </thead>
                    <tbody>
<?php if (count($students) > 0) { ?>
<?php foreach ($students as $student) { ?>
                        <tr>
                            <td><?= htmlspecialchars($student['id']); ?></td>
                            <td style="text-align:center;">
<?php if (!empty($student['photo_path'])) { ?>
                                <img src="../<?= htmlspecialchars($student['photo_path']); ?>" alt="Foto estudiante" style="width:42px; height:42px; border-radius:50%; object-fit:cover; border:2px solid #ced4da;">
<?php } else { ?>
                                <span class="badge badge-secondary" style="padding:8px 10px;">Sin foto</span>
<?php } ?>
                            </td>
                            <td><?= htmlspecialchars($student['document_number']); ?></td>
                            <td><?= htmlspecialchars($student['first_name']); ?></td>
                            <td><?= htmlspecialchars($student['last_name']); ?></td>
                            <td><?= htmlspecialchars($student['name']); ?></td>
                            <td><?= htmlspecialchars($student['semester']); ?></td>
                            <td><?= htmlspecialchars($student['email']); ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" onclick="show_edit(this)" data-id="<?= htmlspecialchars($student['id']); ?>" data-program-id="<?= htmlspecialchars($student['academic_program_id']); ?>" data-document-number="<?= htmlspecialchars($student['document_number']); ?>" data-first-name="<?= htmlspecialchars($student['first_name']); ?>" data-last-name="<?= htmlspecialchars($student['last_name']); ?>" data-email="<?= htmlspecialchars($student['email']); ?>" data-semester="<?= htmlspecialchars($student['semester']); ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="view_card(<?= htmlspecialchars($student['id']); ?>)" title="Ver carnet QR">
                                    <i class="fa fa-qrcode"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="delete_student(<?= htmlspecialchars($student['id']); ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
<?php } ?>
<?php } ?>
                    </tbody>
                    <tfoot class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
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
            <form method="POST" action="students.php?action=create" autocomplete="off" enctype="multipart/form-data">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="program_id">Programa academico</label>
                        <select name="program_id" id="program_id" class="form-control select2">
                            <option value="">Seleccione un programa</option>
<?php foreach ($academic_programs as $program) { ?>
                                <option value="<?= htmlspecialchars($program['id']); ?>"><?= htmlspecialchars($program['name']); ?></option>
<?php } ?>
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
                    <div class="form-group">
                        <label for="photo">Foto del estudiante</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/png,image/jpeg,image/webp">
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG o WEBP. Tamaño máximo: 5MB.</small>
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
            <form method="POST" action="students.php?action=update" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="program_id_edit">Programa academico</label>
                        <select name="program_id_edit" id="program_id_edit" class="form-control select2">
<?php foreach ($academic_programs as $program) { ?>
                                <option value="<?= htmlspecialchars($program['id']); ?>"><?= htmlspecialchars($program['name']); ?></option>
<?php } ?>
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
                    <div class="form-group">
                        <label for="photo_edit">Actualizar foto del estudiante</label>
                        <input type="file" class="form-control" id="photo_edit" name="photo_edit" accept="image/png,image/jpeg,image/webp">
                        <small class="form-text text-muted">Si no seleccionas una foto, se conservará la actual.</small>
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
            language: {
                decimal: "",
                emptyTable: "No hay estudiantes registrados",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                lengthMenu: "Mostrar _MENU_ registros",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No se encontraron coincidencias",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });

        $('#return').click(function() {
            console.log('Regresando a la vista principal de reparto de meriendas');
            window.location.href = 'snacks.php';
        });

        var message = "<?= $_SESSION['message'] ?? '' ?>";
        var messageType = "<?= $_SESSION['message_type'] ?? '' ?>";
        if (message && messageType && typeof toastr === 'object' && typeof toastr[messageType] === 'function') {
            toastr[messageType](message);
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['message_type']); ?>
        }
    });

    function show_edit(element) {
        var id = $(element).data('id');
        var program_id = $(element).data('program-id');
        var document_number = $(element).data('document-number');
        var first_name = $(element).data('first-name');
        var last_name = $(element).data('last-name');
        var email = $(element).data('email');
        var semester = $(element).data('semester');

        $('#document_number_edit').val(document_number);
        $('#first_name_edit').val(first_name);
        $('#last_name_edit').val(last_name);
        $('#email_edit').val(email);
        $('#semester_edit').val(semester);
        $('#id').val(id);
        $('#program_id_edit').val(program_id).trigger('change');
        $('#edit_student').modal('show');
    }

    function delete_student(id) {
        if (confirm('¿Está seguro de eliminar este estudiante?')) {
            window.location.href = 'students.php?action=delete&id=' + id;
        }
    }

    function view_card(id) {
        window.open('student_card.php?id=' + id, '_blank');
    }
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>