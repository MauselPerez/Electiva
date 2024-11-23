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
$title = "Usuarios";
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
            Usuarios 
            <button class="btn-sm btn-primary" style="float: right; margin-top:3px;" id="return">
                <i class="fas fa-arrow-left"></i>  Regresar
            </button>
            <button type="button" class="btn-sm btn-primary" data-toggle="modal" data-target="#new_user" style="float: right; margin-top:3px; margin-right: 5px;">
                <i class="fas fa-save"></i>  Nuevo Usuario
            </button>
        </h3>
        
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Cedula</th>
                    <th>Usuario</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
<?php 
            if (count($users) > 0)
            {
                foreach ($users as $user) 
                { 
?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['document_number'] ?></td>
                        <td><?= $user['username'] ?></td>
                        <td><?= $user['first_name'] ?></td>
                        <td><?= $user['last_name'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td style="text-align:center;">
                            <?php if ($user['is_active'] == 1) { ?>
                                <span class="badge badge-success" style="padding: 8px;">Activo</span>
                            <?php } else { ?>
                                <span class="badge badge-danger"  style="padding: 8px;">Inactivo</span>
                            <?php } ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" onclick="show_edit(this)">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="delete_user(<?=htmlspecialchars($user['id']); ?>)">
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
                    <td colspan="8" style="text-align: center;"><span style="background-color:#cccc00; padding: 10px; color:#ffffff; font-size:large;"><b>No hay usuarios registrados.</b></span></td>
                </tr>
<?php
            }
?>
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Cedula</th>
                    <th>Usuario</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Modal de nuevo usuario -->
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

<!-- Modal de editar usuario -->
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
                        <label for="document_number">Cedula</label>
                        <input type="text" class="form-control" id="document_number_edit" name="document_number" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input type="text" class="form-control" id="username_edit" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">Nombres</label>
                        <input type="text" class="form-control" id="first_name_edit" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Apellidos</label>
                        <input type="text" class="form-control" id="last_name_edit" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email_edit" name="email" required>
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
        $('#table').DataTable({});
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
        var document_number = $(element).closest('tr').find('td').eq(1).text();
        var username = $(element).closest('tr').find('td').eq(2).text();
        var first_name = $(element).closest('tr').find('td').eq(3).text();
        var last_name = $(element).closest('tr').find('td').eq(4).text();
        var email = $(element).closest('tr').find('td').eq(5).text();

        $('#id').val(id);
        $('#document_number_edit').val(document_number);
        $('#username_edit').val(username);
        $('#first_name_edit').val(first_name);
        $('#last_name_edit').val(last_name);
        $('#email_edit').val(email);

        $('#edit_user').modal('show');
    }

    function delete_user(id) {
        var confirm_delete = confirm('¿Está seguro de eliminar este usuario?');
        if (confirm_delete) {
            window.location.href = 'users.php?action=delete&id=' + id;
        }
    }
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>
