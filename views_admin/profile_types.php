<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../templates/login.php');
    exit();
}

require_once '../controllers/profile_types_controller.php';

$controller = new ProfileTypesController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create($_POST);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller->update($_POST['id'], $_POST);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
}

$profileTypes = $controller->index();
$title = 'Tipos de perfil';
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
        min-width: 860px;
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
            min-width: 760px;
        }
    }
</style>

<div class="container-fluid module-page">
    <div class="module-hero">
        <div class="row align-items-center no-gutters module-hero-body">
            <div class="col-lg-8">
                <div class="module-title">Tipos de perfil</div>
                <p class="module-text">Define los perfiles institucionales que usarán personas y módulos del sistema.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button class="btn btn-success hero-btn" data-toggle="modal" data-target="#new_profile_type">
                        <i class="fas fa-save mr-1"></i> Nuevo tipo
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
                <h3 class="panel-title">Listado de tipos de perfil</h3>
                <p class="panel-subtitle">Gestiona nombre, descripción y estado de cada tipo.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-shell">
                <table id="table" class="display table table-bordered" style="width:100%">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
<?php foreach ($profileTypes as $type) { ?>
                    <tr>
                        <td><?= htmlspecialchars($type['id']) ?></td>
                        <td><?= htmlspecialchars($type['name']) ?></td>
                        <td><?= htmlspecialchars($type['description'] ?? '') ?></td>
                        <td style="text-align:center;">
<?php if ((int) $type['is_active'] === 1) { ?>
                            <span class="badge badge-success" style="padding:8px;">Activo</span>
<?php } else { ?>
                            <span class="badge badge-secondary" style="padding:8px;">Inactivo</span>
<?php } ?>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="show_edit(this)"
                                data-id="<?= htmlspecialchars($type['id']) ?>"
                                data-name="<?= htmlspecialchars($type['name']) ?>"
                                data-description="<?= htmlspecialchars($type['description'] ?? '') ?>"
                                data-is-active="<?= htmlspecialchars($type['is_active']) ?>">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="delete_profile_type(<?= (int) $type['id'] ?>)">
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

<div class="modal fade" id="new_profile_type" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="profile_types.php?action=create" method="POST">
                <div class="modal-header"><h5 class="modal-title">Nuevo tipo de perfil</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
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

<div class="modal fade" id="edit_profile_type" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="profile_types.php?action=update" method="POST">
                <div class="modal-header"><h5 class="modal-title">Editar tipo de perfil</h5></div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name_edit">Nombre</label>
                        <input type="text" class="form-control" id="name_edit" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description_edit">Descripción</label>
                        <textarea class="form-control" id="description_edit" name="description" rows="2"></textarea>
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
        emptyTable: "No hay tipos de perfil registrados",
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
    $('#description_edit').val($(element).data('description'));
    $('#is_active_edit').val($(element).data('is-active'));
    $('#edit_profile_type').modal('show');
}

function delete_profile_type(id) {
    if (confirm('¿Está seguro de desactivar este tipo de perfil?')) {
        window.location.href = 'profile_types.php?action=delete&id=' + id;
    }
}
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>