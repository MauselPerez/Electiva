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
    <div class="lockscreen-logo">
        <a href="../../templates/AdminLTE-3.0.5/index2.html"><b>CODIGO DEL EMPLEADO</b></a>
    </div>

    <div class="lockscreen-name">Bienvenidos</div>

    <div class="lockscreen-item">
        <div class="lockscreen-image">
            <img src="../../imgs/login.png" alt="User Image">
        </div>

        <form class="lockscreen-credentials">
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
    <!--<div class="text-center">
        <a href="login.html">Or sign in as a different user</a>
    </div>-->
    <div class="lockscreen-footer text-center">
        Empleado que no porte su carnet no podra ingresar al area de trabajo
    </div>


    <div id="response-container"></div>
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
                    // Manejas la respuesta del servidor aquí
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    // Manejas los errores aquí
                    console.error(error);
                }
            });
        });
    });

</script>
</body>
</html>
