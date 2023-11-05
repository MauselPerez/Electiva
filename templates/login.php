<?php 
    include "../app/login-services.php";
    include "public/includes/google_auth.php";

    $objAPI = new loginAPI();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Inicar Sesion</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../imgs/login.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/toastr/toastr.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/templates/AdminLTE-3.0.5/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</head>
<body class="hold-transition login-page">

<?php 
    if (isset($_POST['user']) && isset($_POST['password'])) 
    {
        $objAPI->validate_users();
    }

    if (isset($_GET['code'])) 
    {
        $code = $_GET['code'];
        validate_google_account($code);
    }

    if (isset($_GET['error'])) 
    {
        $error = $_GET['error'];
        if ($error == 1) 
        {
            echo "<script>toastr.error('Contraseña incorrecta', 'Error');</script>";
        }
        else if ($error == 2) 
        {
            echo "<script>toastr.error('Usuario no existe', 'Error');</script>";
        }
    }
?>
<div class="login-box">
    <div class="login-logo">
        <a href="index2.html">
            <b>Iniciar Sesión <span class="fas fa-user"></span></b>
        </a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            
            <form action="login.php" method="POST" id="frm_user" name="frm_user">
                <div class="input-group mb-3">
                    <input id="user" name="user" class="form-control" placeholder="Usuario" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Recordarme
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <div class="social-auth-links text-center mb-3">
                <p>- O -</p>
                <a href="#" class="btn btn-block btn-primary">
                    <i class="fab fa-facebook mr-2"></i> Iniciar sesión usando Facebook
                </a>
                <a href="https://accounts.google.com/o/oauth2/auth?client_id=826238057510-s32b1slhd3e2svbus343gm8o7sdkd8em.apps.googleusercontent.com&redirect_uri=http://localhost/web_electiva/views_admin/login.php&scope=https://www.googleapis.com/auth/userinfo.email&response_type=code&access_type=offline&prompt=select_account" class="btn btn-block btn-danger">
                    <i class="fab fa-google mr-2"></i> Iniciar sesión usando Google
                </a>

            </div>
            <!-- /.social-auth-links -->

            <p class="mb-1" style="float: left;">
                <a href="../views_admin/forgot-password.php">Olvidé mi contraseña</a>
            </p>
            <!--<p class="mb-0" style="float: right;">
                <a href="register.php" class="text-center">Registrarme</a>
            </p>-->
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="AdminLTE-3.0.5/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="AdminLTE-3.0.5/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="AdminLTE-3.0.5/dist/js/adminlte.min.js"></script>
<script src="AdminLTE-3.0.5/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
</body>
</html>
