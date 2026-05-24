<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}

require_once '../controllers/users_controller.php';

$controller = new UsersController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create($_POST);
    header('Location: users.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller->update($_POST['id'], $_POST);
    header('Location: users.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
    header('Location: users.php');
}

$users = $controller->index();
$roles = $controller->getAllRoles();
$title = "Usuarios";
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
        min-width: 980px;
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
            min-width: 860px;
        }
    }
</style>

<div class="container-fluid module-page">
    <div class="module-hero">
        <div class="row align-items-center no-gutters module-hero-body">
            <div class="col-lg-8">
                <div class="module-title">Usuarios</div>
                <p class="module-text">Gestiona los accesos del sistema con una interfaz uniforme, clara y adaptable a cualquier pantalla.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button type="button" class="btn btn-success hero-btn" data-toggle="modal" data-target="#new_user">
                        <i class="fas fa-save mr-1"></i> Nuevo Usuario
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
                <h3 class="panel-title">Listado de usuarios</h3>
                <p class="panel-subtitle">Consulta, edita o elimina usuarios registrados en el sistema.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-shell">
                <table id="table" class="display table table-bordered" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cedula</th>
                            <th>Usuario</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th style="width: 7%;"></th>
                            <th style="width: 7%;"></th>
                        </tr>
                    </thead>
                    <tbody>
<?php if (count($users) > 0) { ?>
<?php foreach ($users as $user) { ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td><?= htmlspecialchars($user['document_number']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['first_name']); ?></td>
                            <td><?= htmlspecialchars($user['last_name']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['rol']); ?></td>
                            <td style="text-align:center;">
                                <?php if ((int) $user['is_active'] === 1) { ?>
                                    <span class="badge badge-success" style="padding: 8px;">Activo</span>
                                <?php } else { ?>
                                    <span class="badge badge-danger" style="padding: 8px;">Inactivo</span>
                                <?php } ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" onclick="show_edit(this)" data-id="<?= htmlspecialchars($user['id']); ?>" data-document-number="<?= htmlspecialchars($user['document_number']); ?>" data-username="<?= htmlspecialchars($user['username']); ?>" data-first-name="<?= htmlspecialchars($user['first_name']); ?>" data-last-name="<?= htmlspecialchars($user['last_name']); ?>" data-email="<?= htmlspecialchars($user['email']); ?>" data-role-id="<?= htmlspecialchars($user['role_id']); ?>" data-is-active="<?= htmlspecialchars($user['is_active']); ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="delete_user(<?= htmlspecialchars($user['id']); ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
<?php } ?>
<?php } else { ?>
                        <tr>
                            <td colspan="10" style="text-align: center;"><span style="background-color:#cccc00; padding: 10px; color:#ffffff; font-size:large;"><b>No hay usuarios registrados.</b></span></td>
                        </tr>
<?php } ?>
                    </tbody>
                    <tfoot class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cedula</th>
                            <th>Usuario</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="new_user" tabindex="-1" role="dialog" aria-labelledby="new_user" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="users.php?action=create" method="POST" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nuevo Usuario</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="document_number">Cedula</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
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
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Rol</label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="">Seleccione un rol</option>
                            <?php foreach ($roles as $role) { ?>
                                <option value="<?= htmlspecialchars($role['id']) ?>"><?= htmlspecialchars($role['name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
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

<div class="modal fade" id="edit_user" tabindex="-1" role="dialog" aria-labelledby="edit_user" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="users.php?action=update" method="POST" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Usuario</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="document_number_edit">Cedula</label>
                        <input type="text" class="form-control" id="document_number_edit" name="document_number" required>
                    </div>
                    <div class="form-group">
                        <label for="username_edit">Usuario</label>
                        <input type="text" class="form-control" id="username_edit" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name_edit">Nombres</label>
                        <input type="text" class="form-control" id="first_name_edit" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name_edit">Apellidos</label>
                        <input type="text" class="form-control" id="last_name_edit" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email_edit">Email</label>
                        <input type="email" class="form-control" id="email_edit" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="role_id_edit">Rol</label>
                        <select class="form-control" id="role_id_edit" name="role_id" required>
                            <option value="">Seleccione un rol</option>
                            <?php foreach ($roles as $role) { ?>
                                <option value="<?= htmlspecialchars($role['id']) ?>"><?= htmlspecialchars($role['name']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_active_edit">Estado</label>
                        <select class="form-control" id="is_active_edit" name="is_active" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password_edit">Nueva contraseña</label>
                        <input type="password" class="form-control" id="password_edit" name="password">
                        <small class="form-text text-muted">Déjelo vacío si no desea cambiar la contraseña.</small>
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json' }
        });

        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });

        var message = "<?=$_SESSION['message'] ?? ''?>";
        var messageType = "<?=$_SESSION['message_type'] ?? ''?>";

        if (message && messageType && typeof toastr === 'object' && typeof toastr[messageType] === 'function') {
            toastr[messageType](message);
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['message_type']); ?>
        }
    });

    function show_edit(element) {
        var id = $(element).data('id');
        var document_number = $(element).data('document-number');
        var username = $(element).data('username');
        var first_name = $(element).data('first-name');
        var last_name = $(element).data('last-name');
        var email = $(element).data('email');
        var role_id = $(element).data('role-id');
        var is_active = $(element).data('is-active');

        $('#id').val(id);
        $('#document_number_edit').val(document_number);
        $('#username_edit').val(username);
        $('#first_name_edit').val(first_name);
        $('#last_name_edit').val(last_name);
        $('#email_edit').val(email);
        $('#role_id_edit').val(role_id);
        $('#is_active_edit').val(is_active);
        $('#password_edit').val('');

        $('#edit_user').modal('show');
    }

    function delete_user(id) {
        if (confirm('¿Está seguro de eliminar este usuario?')) {
            window.location.href = 'users.php?action=delete&id=' + id;
        }
    }
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>