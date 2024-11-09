<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../imgs/login.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/dist/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <script src="../scripts/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <style>
        .gradient-custom-2 {
            background-image: url('../imgs/infotep.png');
            background-size: cover;
        }
        .gradient-custom-3 {
            background: linear-gradient(to right, #343a40, gray);
        }
        @media (min-width: 768px) {
            .gradient-form { height: 100vh !important; }
        }
        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }
        html, body { height: 100%; }
    </style>
</head>
<body class="hold-transition login-page" style="height: 100%;">
    <div class="container-fluid h-100">
        <div class="row g-0 h-100">
            <!-- Primera columna -->
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2 h-100">
                <div class="text-white px-3 py-4 p-md-5 mx-md-4"></div>
            </div>
            <!-- Segunda columna -->
            <div class="col-lg-6 d-flex align-items-center h-100">
                <div class="card-body p-md-5 mx-md-4">
                    <div class="text-center">
                        <h1 class="mt-1 mb-5 pb-1">Well Snack</h1>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form action="../controllers/login_controller.php" method="POST" autocomplete="off">
                        <p><b>Ingrese sus credenciales de acceso</b></p>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="form2Example11">Usuario</label>
                            <input type="text" id="form2Example11" name="username" class="form-control" placeholder="Usuario" required/>
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="form2Example22">Contraseña</label>
                            <input type="password" id="form2Example22" name="password" class="form-control" placeholder="contraseña" required/>
                        </div>
                        <div class="text-center pt-1 mb-5 pb-1">
                            <button class="btn btn-primary btn-block fa-lg gradient-custom-3 mb-3" type="submit">Log in</button>
                            <a class="text-muted" href="#!">Olvidó la contraseña?</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>
</html>
