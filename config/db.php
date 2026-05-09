<?php
// Configuración de la base de datos
$dbHost = 'localhost';
$dbName = 'gzdbkvwa_db_project';
$dbUser = 'gzdbkvwa_goparty_user';
$dbPass = 'j2UCemCV66kG3wz';

try {
    // Crear la conexión PDO
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    // Configuración de opciones de PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de errores de conexión
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
