<?php
// Inicia la sesión
session_start();

include "../public/includes/mistakes.php";
see_errors();

// Define el título de la página
$title = "Página de Usuario";

// Contenido específico de la página
ob_start();
?>


<div class="row">
    <div class="col-md-12">
        <h6 style="color: red;">Reporte de Marcaciones</h6>
        <hr>
    </div>
</div>


<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base.php';
?>
