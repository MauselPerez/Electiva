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
        min-width: 640px;
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
            min-width: 520px;
        }
    }
</style>

<div class="container-fluid module-page">
    <div class="module-hero">
        <div class="row align-items-center no-gutters module-hero-body">
            <div class="col-lg-8">
                <div class="module-title">Programas académicos</div>
                <p class="module-text">Mantén la gestión de programas con una vista coherente, moderna y adaptable en todos los dispositivos.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button type="button" class="btn btn-success hero-btn" data-toggle="modal" data-target="#new_student">
                        <i class="fas fa-save mr-1"></i> Nuevo Programa
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
                <h3 class="panel-title">Listado de programas</h3>
                <p class="panel-subtitle">Consulta el estado de cada programa y realiza mantenimiento rápido.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-shell">
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
<?php if (count($academic_programs) > 0) { ?>
<?php foreach ($academic_programs as $academic_program) { ?>
                        <tr>
                            <td><?= htmlspecialchars($academic_program['id']); ?></td>
                            <td><?= htmlspecialchars($academic_program['name']); ?></td>
                            <td style="text-align:center;">
                                <?php if ((int) $academic_program['is_active'] === 1) { ?>
                                    <span class="badge badge-success" style="padding: 8px;">Activo</span>
                                <?php } else { ?>
                                    <span class="badge badge-danger" style="padding: 8px;">Inactivo</span>
                                <?php } ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" onclick="show_edit(this)">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="delete_program(<?= htmlspecialchars($academic_program['id']); ?>)">
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
                            <th>Programa</th>
                            <th>Estado</th>
                            <th style="width: 7%;"></th>
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
                        <label for="name_edit">Programa academico</label>
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
        var dataTableEs = {
            decimal: "",
            emptyTable: "No hay programas registrados",
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
        };

        $('#table').DataTable({
            language: dataTableEs
        });

        $('#return').click(function() {
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
        var id = $(element).closest('tr').find('td').eq(0).text();
        var name = $(element).closest('tr').find('td').eq(1).text();
        $('#id').val(id);
        $('#name_edit').val(name);
        $('#edit_program').modal('show');
    }

    function delete_program(id) {
        if (confirm('¿Está seguro de eliminar este programa?')) {
            window.location.href = 'academic_programs.php?action=delete&id=' + id;
        }
    }
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>