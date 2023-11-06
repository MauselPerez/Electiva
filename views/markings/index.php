<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Control de acceso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../../imgs/login.png">
    <link rel="stylesheet" href="../../templates/AdminLTE-3.0.5/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="../../templates/AdminLTE-3.0.5/dist/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition lockscreen">
<div class="lockscreen-wrapper">
<?php
    if (isset($_POST) && !empty($_POST)) 
    {
        echo '<pre>'; print_r($_POST); echo '</pre>';
        die("Sirve");
    }
?>

    <div class="lockscreen-logo">
        <a href="../../templates/AdminLTE-3.0.5/index2.html"><b>CODIGO DEL EMPLEADO</b></a>
    </div>

    <div class="lockscreen-name">Bienvenidos</div>

    <div class="lockscreen-item">
        <div class="lockscreen-image">
            <img src="../../imgs/login.png" alt="User Image">
        </div>

        <form class="lockscreen-credentials" autocomplete="off">
            <div class="input-group">
                <input id="qr" type="text" class="form-control" placeholder="QR">

                <div class="input-group-append">
                    <button type="button" class="btn" disabled><i class="fas fa-arrow-right text-muted"></i></button>
                </div>
            </div>
        </form>

    </div>
    <div class="help-block text-center">
        Ingresa el codigo de empleado para iniciar sesion
    </div>
    <div class="lockscreen-footer text-center">
        Empleado que no porte su carnet no podra ingresar al area de trabajo
    </div>

    <!--SI EL EMPLEADO NO EXISTE-->
    <div id="response-container"></div>

    <!--SI EL EMPLEADO EXISTE -->
    <div class="modal fade" id="employee">
        <div class="modal-dialog modal-lg">
            <form id="markings" name="markings" method="POST" action="index.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Información del empleado</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row" style="margin-bottom: 60px;">
                                <div class="col-md-12" style="text-align: center;">
                                    <img id="user_image" src="" alt="User Image" style="width: 200px;">
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 30px;">
                                <div class="col-md-3">
                                    <label for="document_number">Numero de documento</label>
                                    <input type="text" class="form-control" id="document_number" name="document_number" value="">
                                    <input type="text" class="form-control" id="user_id" name="user_id" value="" hidden>
                                </div>
                                <div class="col-md-3">
                                    <label for="first_name">Nombres</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="">
                                </div>
                                <div class="col-md-3">
                                    <label for="last_name">Apellidos</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="">
                                </div>
                                <div class="col-md-3">
                                    <label for="age">Edad</label>
                                    <input type="text" class="form-control" id="age" name="age" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="phone">Telefono</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="">
                                </div>
                                <div class="col-md-3">
                                    <label for="email">Correo</label>
                                    <input type="text" class="form-control" id="email" name="email" value="">
                                </div>
                                <div class="col-md-3">
                                    <label for="charge">Cargo</label>
                                    <input type="text" class="form-control" id="charge" name="charge" value="">
                                </div>
                                <div class="col-md-3">
                                    <label for="department">Departamento</label>
                                    <input type="text" class="form-control" id="department" name="department" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-sign-out-alt"></i> SALIDA</button>
                        <button id="entry" class="btn btn-primary"> <i class="fas fa-user"></i> ENTRADA</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../../templates/AdminLTE-3.0.5/plugins/jquery/jquery.min.js"></script>
<script src="../../templates/AdminLTE-3.0.5/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('#qr').focus();

        $('#qr').on('input', function() {
            var qr = $('#qr').val();
            // Realiza la solicitud AJAX solo si el valor del input no está vacío
            $.ajax({
                url: '../../app/empleados-services.php',
                type: 'POST',
                dataType: 'json',
                data: JSON.stringify({ action: 'getEmployeeByID', qrCode: qr }),
                contentType: 'application/json',
                success: function(response) {
                    console.log(response.data);
                    if (response.success == false || response.success == null) 
                    {
                        $('#response-container').html('<div class="alert alert-danger alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true"> &times;</button> <h5><i class="icon fas fa-ban"></i>No existe</h5> Empleado no se ecnuentra en la base de datos, <b>Consulte con el administrador</b></div>');
                    } 
                    else 
                    {
                        $('#user_id').val(response.data.user_id);
                        $('#document_number').val(response.data.document_number);
                        $('#first_name').val(response.data.first_name);
                        $('#last_name').val(response.data.last_name);
                        $('#age').val(response.data.age);
                        $('#phone').val(response.data.phone);
                        $('#email').val(response.data.email);
                        $('#charge').val(response.data.charge_name);
                        $('#department').val(response.data.department_name);
                        $('#user_image').attr('src', response.data.img);

                        $('#employee').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        $('#entry').on('click', function() {
            $('#markings').submit();
        });
    });

</script>
</body>
</html>
