<?php
    session_start();

    if (!isset($_SESSION['user'])) 
    {
        header('Location: /Electiva/templates/login.php');
        exit;
    }


    /*$requestUri = $_SERVER['REQUEST_URI'];
    echo '<pre>'; print_r($requestUri); echo '</pre>';
    if ($requestUri === '/views_admin/index_admin') 
    {
        # code...
    }
    else 
    {
        // Ruta no válida, mostrar un error 404
        http_response_code(404);
        echo 'Error 404 - Página no encontrada';
        exit;
    }*/
?>