<?php
    function validate_google_account($code) 
    {
        $token_url = 'https://accounts.google.com/o/oauth2/token';
        $params = array(
            'code' => $code,
            'client_id' => '826238057510-s32b1slhd3e2svbus343gm8o7sdkd8em.apps.googleusercontent.com',
            'client_secret' => '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',
            'redirect_uri' => 'http://localhost/web_electiva/views_admin/login.php',
            'grant_type' => 'authorization_code'
        );
    
        $curl = curl_init($token_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
    
        $token_data = json_decode($response, true);
    
        // Verifica si se obtuvo un token de acceso
        if (isset($token_data['access_token'])) 
        {
            // El usuario ha iniciado sesión correctamente, redirígelo a starter.php
            header("Location: starter.php");
            exit();
        } 
        else 
        {
            // Ocurrió un error al obtener el token de acceso, maneja el error aquí
            echo "Error al iniciar sesión con Google.";
        }
    }
?>