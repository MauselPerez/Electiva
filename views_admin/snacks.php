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
    <?php if (canAccessModule('organizational_units')) { echo card('organizational_units.php', 'organization_chart2.png', 'Organigrama'); } ?>
    <?php if (canAccessModule('roles')) { echo card('roles.php', 'coordinate.png', 'Roles y permisos'); } ?>
    <?php if (canAccessModule('profile_types')) { echo card('profile_types.php', 'team.png', 'Tipos de perfil'); } ?>
    <?php if (canAccessModule('students')) { echo card('students.php', 'audience.png', 'Estudiantes'); } ?>
    <?php if (canAccessModule('schedules')) { echo card('schedules.php', 'schedule.png', 'Planificacion'); } ?>
    <?php if (canAccessModule('delivery')) { echo card('delivery.php', 'package_delivery.png', 'Entregas/Reparto'); } ?>
    <?php if (canAccessModule('reports')) { echo card('reports.php', 'report.png', 'Reportes'); } ?>
    <?php if (canAccessModule('academic_programs')) { echo card('academic_programs.php', 'academic_program.png', 'Programas academicos'); } ?>
    <?php if (canAccessModule('users')) { echo card('users.php', 'users.png', 'Usuarios'); } ?>
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
