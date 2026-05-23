<?php
session_start();

// Simular lo que hace el controlador
if ($_GET['action'] === 'test_cedula') {
    $_SESSION['message'] = "Error al crear el usuario: Ya existe una persona con esa cédula.";
    $_SESSION['message_type'] = "error";
    session_write_close();
    header("Location: test_session.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Session</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
</head>
<body>
    <h1>Test de Sesión y Toastr</h1>
    <p><a href="test_session.php?action=test_cedula">Simular error de cédula</a></p>
    
    <script>
        $(document).ready(function() {
            var message = "<?=$_SESSION['message'] ?? ''?>";
            var messageType = "<?=$_SESSION['message_type'] ?? ''?>";
            
            console.log('Message:', message);
            console.log('Type:', messageType);
            console.log('toastr type:', typeof toastr);
            console.log('Method exists:', typeof toastr[messageType]);
            
            if (message && messageType && typeof toastr === 'object' && typeof toastr[messageType] === 'function') {
                toastr[messageType](message);
                <?php unset($_SESSION['message'], $_SESSION['message_type']); session_write_close(); ?>
            }
        });
    </script>
</body>
</html>
