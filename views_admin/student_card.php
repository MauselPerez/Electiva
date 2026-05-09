<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}

require_once '../controllers/students_controller.php';

$controller = new StudentsController();
$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$student = $studentId > 0 ? $controller->getById($studentId) : null;

if (!$student) {
    http_response_code(404);
}

$qrValue = $student ? ('ID_' . $student['document_number']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnet Estudiante</title>
    <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fontawesome-free/css/all.min.css">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, #dbe9f4, #f4f7fa 40%, #eef4fb 100%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2d3d;
            padding: 24px;
            box-sizing: border-box;
        }

        .card-wrapper {
            width: 100%;
            max-width: 850px;
        }

        .student-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(18, 41, 64, 0.15);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #1f2d3d 0%, #304e6d 62%, #3f7aa5 100%);
            color: #fff;
            padding: 22px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .card-header h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
        }

        .card-header span {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 24px 28px;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 24px;
        }

        .student-info {
            display: grid;
            gap: 16px;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e8edf3;
        }

        .profile img,
        .photo-placeholder {
            width: 92px;
            height: 92px;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid #dce6ef;
            background: #f1f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #597a99;
            font-size: 2rem;
        }

        .profile h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #1f2d3d;
        }

        .profile p {
            margin: 4px 0 0;
            color: #55708b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .info-box {
            border: 1px solid #e6edf4;
            border-radius: 12px;
            padding: 10px 12px;
            background: #fbfdff;
        }

        .info-label {
            display: block;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #68829b;
            margin-bottom: 4px;
            letter-spacing: 0.04em;
        }

        .info-value {
            font-size: 0.95rem;
            color: #203244;
            word-break: break-word;
        }

        .qr-box {
            border: 1px solid #e2ebf3;
            border-radius: 16px;
            background: #f8fbff;
            padding: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        #qrcode {
            width: 210px;
            height: 210px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid #dbe6f1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            padding: 8px;
            box-sizing: border-box;
        }

        .qr-label {
            font-size: 0.85rem;
            color: #5e7891;
            margin-bottom: 2px;
        }

        .qr-value {
            font-weight: 700;
            color: #1f2d3d;
        }

        .card-footer {
            padding: 0 28px 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            border: 0;
            border-radius: 999px;
            padding: 9px 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #1f6f9f;
            color: #fff;
        }

        .btn-secondary {
            background: #e7edf4;
            color: #203244;
        }

        .not-found {
            text-align: center;
            background: #fff;
            border-radius: 16px;
            padding: 26px;
            box-shadow: 0 10px 30px rgba(18, 41, 64, 0.1);
        }

        @media (max-width: 768px) {
            .card-body {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            #qrcode {
                width: 190px;
                height: 190px;
            }
        }
    </style>
</head>
<body>
    <div class="card-wrapper">
<?php if (!$student) { ?>
        <div class="not-found">
            <h2>No se encontró el estudiante</h2>
            <p>Verifica el identificador y vuelve a intentarlo.</p>
            <a href="students.php" class="btn btn-secondary">Volver</a>
        </div>
<?php } else { ?>
        <div class="student-card">
            <div class="card-header">
                <div>
                    <h1>Carnet Estudiantil con QR</h1>
                    <span>Módulo de meriendas</span>
                </div>
                <i class="fas fa-id-card" style="font-size: 1.5rem;"></i>
            </div>

            <div class="card-body">
                <div class="student-info">
                    <div class="profile">
<?php if (!empty($student['photo_path'])) { ?>
                        <img src="../<?= htmlspecialchars($student['photo_path']) ?>" alt="Foto estudiante">
<?php } else { ?>
                        <div class="photo-placeholder"><i class="fas fa-user"></i></div>
<?php } ?>
                        <div>
                            <h2><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></h2>
                            <p><?= htmlspecialchars($student['program_name']) ?></p>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-box">
                            <span class="info-label">ID interno</span>
                            <span class="info-value">#<?= (int) $student['id'] ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Cédula</span>
                            <span class="info-value"><?= htmlspecialchars($student['document_number']) ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Semestre</span>
                            <span class="info-value"><?= htmlspecialchars($student['semester']) ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Estado</span>
                            <span class="info-value"><?= ((int) $student['is_active'] === 1) ? 'Activo' : 'Inactivo' ?></span>
                        </div>
                        <div class="info-box" style="grid-column: 1 / -1;">
                            <span class="info-label">Correo</span>
                            <span class="info-value"><?= htmlspecialchars($student['email']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="qr-box">
                    <div id="qrcode"></div>
                    <div class="qr-label">Código QR de identificación</div>
                    <!--<div class="qr-value"><?= htmlspecialchars($qrValue) ?></div>-->
                </div>
            </div>

            <div class="card-footer">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <a href="students.php" class="btn btn-secondary">Volver</a>
            </div>
        </div>
<?php } ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
<?php if ($student) { ?>
        new QRCode(document.getElementById('qrcode'), {
            text: <?= json_encode($qrValue) ?>,
            width: 190,
            height: 190,
            colorDark: '#1f2d3d',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
<?php } ?>
    </script>
</body>
</html>
