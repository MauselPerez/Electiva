<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la sesión completamente
session_destroy();

// Redirigir al login
header("Location: ../templates/login.php");
exit;
?>
