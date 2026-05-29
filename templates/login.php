<?php
    session_start();
    if(isset($_SESSION['user_id'])){
        header('Location: ../views_admin/index.php');
    }else{
        unset($_SESSION['user']);
    }
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
        :root {
            --login-panel-bg: rgba(255, 255, 255, 0.92);
            --login-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
            --login-radius: 28px;
            --login-dark: #343a40;
        }

        body.login-page {
            background:
                linear-gradient(120deg, rgba(52, 58, 64, 0.96), rgba(96, 125, 139, 0.82)),
                url('../imgs/infotep.png') center/cover no-repeat;
            min-height: 100vh;
        }

        .login-shell {
            min-height: 100vh;
            padding: 24px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 32px;
            box-shadow: var(--login-shadow);
            min-height: calc(100vh - 48px);
            overflow: hidden;
            backdrop-filter: blur(6px);
        }

        .login-brand-panel {
            background-image: linear-gradient(rgba(25, 33, 41, 0.38), rgba(25, 33, 41, 0.7)), url('../imgs/infotep.png');
            background-position: center;
            background-size: cover;
            color: #fff;
            display: flex;
            min-height: 260px;
            padding: 40px;
        }

        .login-brand-copy {
            align-self: flex-end;
            max-width: 420px;
        }

        .login-brand-copy h1 {
            font-size: clamp(1.8rem, 4vw, 3rem);
            font-weight: 700;
            margin-bottom: 12px;
        }

        .login-brand-copy p {
            color: rgba(255, 255, 255, 0.86);
            font-size: 1rem;
            margin: 0;
        }

        .gradient-custom-2 {
            background-image: url('../imgs/infotep.png');
            background-size: cover;
        }
        .gradient-custom-3 {
            background: linear-gradient(to right, #343a40, gray);
        }
        .login-form-panel {
            align-items: center;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.97), rgba(248, 250, 252, 0.97));
            display: flex;
            justify-content: center;
            padding: 32px;
        }

        .login-form-card {
            background: var(--login-panel-bg);
            border-radius: var(--login-radius);
            box-shadow: 0 18px 42px rgba(148, 163, 184, 0.18);
            padding: 32px;
            width: min(100%, 520px);
        }

        .login-form-card .brand-logo {
            display: block;
            margin: 0 auto 24px;
            max-width: min(100%, 320px);
            width: 100%;
        }

        .login-form-card .lead-text {
            color: #334155;
            font-size: 1rem;
            margin-bottom: 24px;
            text-align: center;
        }

        .login-form-card .form-control {
            border-radius: 14px;
            min-height: 48px;
        }

        .login-form-card .btn {
            border-radius: 14px;
            font-weight: 700;
            min-height: 48px;
        }

        .login-help-link {
            display: inline-block;
            margin-top: 4px;
            text-align: center;
            width: 100%;
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
        @media (max-width: 991.98px) {
            .login-shell {
                padding: 16px;
            }

            .login-card {
                min-height: calc(100vh - 32px);
            }

            .login-brand-panel {
                min-height: 220px;
                padding: 28px;
            }

            .login-form-panel {
                padding: 20px;
            }

            .login-form-card {
                padding: 28px 22px;
            }
        }

        @media (max-width: 575.98px) {
            .login-shell {
                padding: 10px;
            }

            .login-card {
                border-radius: 22px;
                min-height: calc(100vh - 20px);
            }

            .login-brand-panel {
                min-height: 180px;
                padding: 22px;
            }

            .login-brand-copy h1 {
                font-size: 1.5rem;
            }

            .login-brand-copy p,
            .login-form-card .lead-text {
                font-size: 0.95rem;
            }

            .login-form-panel {
                padding: 12px;
            }

            .login-form-card {
                border-radius: 22px;
                padding: 22px 16px;
            }
        }

        html, body { height: 100%; }
    </style>
</head>
<body class="hold-transition login-page" style="height: 100%;">
    <div class="container-fluid login-shell">
        <div class="row no-gutters login-card">
            <div class="col-lg-6 login-brand-panel">
                <div class="login-brand-copy">
                    <h1>Well Snack</h1>
                    <p>Accede al sistema de gestión de meriendas desde cualquier dispositivo con una interfaz más cómoda, clara y estable.</p>
                </div>
            </div>
            <div class="col-lg-6 login-form-panel">
                <div class="login-form-card">
                    <form action="../controllers/login_controller.php" method="POST" autocomplete="off">
                        <div class="text-center">
                            <img src="../imgs/logo_well_snack.png" class="brand-logo" alt="logo">
                        </div>
                        <p class="lead-text"><b>Ingrese sus credenciales de acceso</b></p>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="username">Usuario</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="Usuario" required/>
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="password">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="contraseña" required/>
                        </div>
                        <div class="text-center pt-1 mb-5 pb-1">
                            <button class="btn btn-primary btn-block fa-lg gradient-custom-3 mb-3" type="submit">Log in</button>
                            <a class="text-muted login-help-link" href="#!">Olvidó la contraseña?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
    if(isset($_SESSION['error'])){
        echo "<script>toastr.error('".$_SESSION['error']."')</script>";
        unset($_SESSION['error']);
    }
?>

