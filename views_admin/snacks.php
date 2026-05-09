<?php
include '../public/includes/helpers.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}
$title = "Reparto de meriendas";
ob_start();
?>
<style>
    img:hover {
        transform: scale(1.1);
    }
</style>
<div class="row">
    <?=card('students.php', 'audience.png', 'Estudiantes')?>
    <?=card('schedules.php', 'schedule.png', 'Planificacion')?>
    <?=card('delivery.php', 'package_delivery.png', 'Entregas/Reparto')?>
    <?=card('reports.php', 'report.png', 'Reportes')?>
    <?=card('academic_programs.php', 'academic_program.png', 'Programas academicos')?>
    <?=card('users.php', 'users.png', 'Usuarios')?>
</div>

<script>
	/*$(document).ready(function(){
		$('.overview-item--c5').each(function(){
			if($(this).find('b').text() == 'Usuarios'){
				$(this).css('cursor', 'not-allowed');
				$(this).click(function(e){
					e.preventDefault();
				});
			}
		});
	});*/
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>
