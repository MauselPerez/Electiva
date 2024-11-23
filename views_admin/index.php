<?php
include '../public/includes/helpers.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../templates/login.php");
    exit();
}
$title = "Inicio";
ob_start();
?>
<style>
    img:hover {
        transform: scale(1.1);
    }
</style>
<div class="row">
    <?=card('#', 'clinic.png', 'Citas medicas')?>
    <?=card('snacks.php', 'food.png', 'Meriendas')?>
</div>
<?php
$content = ob_get_clean();
include '../templates/base.php';
?>
