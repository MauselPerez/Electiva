<?php
// Define el título de la página
$title = "Página de Administrador";

// Inicia el búfer de salida
ob_start();
?>
<!-- AQUI VA EL CONTENIDO DE LA PÁGINA -->
Hola

<!-- AQUI TERMINA EL CONTENIDO DE LA PÁGINA -->
<?php
// Captura la salida en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base.php';
?>
