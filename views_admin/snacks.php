<?php
include '../public/includes/helpers.php';
$title = "Reparto de meriendas";
ob_start();
?>
<div class="row">
    <?=card('students.php', 'audience.png', 'Estudiantes')?>
    <?=card('schedules.php', 'schedule.png', 'Planificacion')?>
    <?=card('delivery.php', 'package_delivery.png', 'Entregas/Reparto')?>
    <?=card('reports.php', 'report.png', 'Reportes')?>
</div>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>
