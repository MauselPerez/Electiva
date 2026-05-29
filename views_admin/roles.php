<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../templates/login.php');
    exit();
}

require_once '../controllers/roles_controller.php';

$controller = new RolesController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create($_POST);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller->update($_POST['id'], $_POST);
}
    
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
}

$roles = $controller->index();
$title = 'Roles';
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
        min-width: 760px;
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
            min-width: 680px;
        }
    }
</style>

<div class="container-fluid module-page">
    <div class="module-hero">
        <div class="row align-items-center no-gutters module-hero-body">
            <div class="col-lg-8">
                <div class="module-title">Roles y permisos</div>
                <p class="module-text">Los roles representan permisos dentro del sistema, no cargos laborales. Un usuario puede tener varios roles a la vez.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button class="btn btn-success hero-btn" data-toggle="modal" data-target="#new_role">
                        <i class="fas fa-save mr-1"></i> Nuevo rol
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
                <h3 class="panel-title">Listado de roles</h3>
                <p class="panel-subtitle">Gestiona el estado y nombre de cada permiso funcional del sistema.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="alert alert-info" style="border-radius: 14px; margin-bottom: 16px;">
                <strong>Roles recomendados inicialmente:</strong> ADMINISTRADOR, PLANIFICADOR, OPERADOR_ENTREGAS, GESTOR_BENEFICIARIOS, VISOR_REPORTES y AUDITOR.
                Un mismo usuario puede recibir varios roles usando la relación `ws_user_roles`.
            </div>
            <div class="table-shell">
                <table id="table" class="display table table-bordered" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($roles as $role) { ?>
                    <tr>
                        <td><?= htmlspecialchars($role['id']) ?></td>
                        <td><?= htmlspecialchars($role['name']) ?></td>
                        <td style="text-align:center;">
<?php if ((int) $role['is_active'] === 1) { ?>
                            <span class="badge badge-success" style="padding:8px;">Activo</span>
<?php } else { ?>
                            <span class="badge badge-secondary" style="padding:8px;">Inactivo</span>
<?php } ?>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="show_edit(this)"
                                data-id="<?= htmlspecialchars($role['id']) ?>"
                                data-name="<?= htmlspecialchars($role['name']) ?>"
                                data-is-active="<?= htmlspecialchars($role['is_active']) ?>">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="delete_role(<?= (int) $role['id'] ?>)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
<?php } ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="new_role" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="roles.php?action=create" method="POST">
                <div class="modal-header"><h5 class="modal-title">Nuevo rol</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre</label>
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

<div class="modal fade" id="edit_role" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="roles.php?action=update" method="POST">
                <div class="modal-header"><h5 class="modal-title">Editar rol</h5></div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name_edit">Nombre</label>
                        <input type="text" class="form-control" id="name_edit" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="is_active_edit">Estado</label>
                        <select class="form-control" id="is_active_edit" name="is_active">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
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

<script>
$(document).ready(function() {
    var dataTableEs = {
        decimal: "",
        emptyTable: "No hay roles registrados",
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

    $('#return').click(function() { window.location.href = 'snacks.php'; });

    var message = "<?= $_SESSION['message'] ?? '' ?>";
    var messageType = "<?= $_SESSION['message_type'] ?? '' ?>";
    if (message && messageType && typeof toastr === 'object' && typeof toastr[messageType] === 'function') {
        toastr[messageType](message);
        <?php unset($_SESSION['message']); ?>
        <?php unset($_SESSION['message_type']); ?>
    }
});

function show_edit(element) {
    $('#id').val($(element).data('id'));
    $('#name_edit').val($(element).data('name'));
    $('#is_active_edit').val($(element).data('is-active'));
    $('#edit_role').modal('show');
}

function delete_role(id) {
    if (confirm('¿Está seguro de desactivar este rol?')) {
        window.location.href = 'roles.php?action=delete&id=' + id;
    }
}
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>