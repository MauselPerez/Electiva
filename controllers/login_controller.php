<?php
session_start();

class LoginController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = "Por favor, complete todos los campos";
                header("Location: ../templates/login.php");
                exit();
            }

            $url = 'http://127.0.0.1:8000/api/login';
            $data = [
                'grant_type' => 'password',
                'username' => $username,
                'password' => $password,
                'client_id' => 'string',
                'client_secret' => 'string'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_HEADER, true);

            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);
            curl_close($ch);

            $response_data = json_decode($body, true);

            if (isset($response_data) && !empty($response_data['id'])) {
                if (preg_match('/access_token=([^;]+)/', $header, $matches)) {
                    $_SESSION['access_token'] = $matches[1]; 
                }

                $_SESSION['user_id'] = $response_data['id'];
                $_SESSION['username'] = $response_data['sub'];   
                $_SESSION['role'] = $response_data['rol'];

                header("Location: ../views_admin/index.php");
                exit();
            } else {
                $_SESSION['error'] = "Error de autenticación";
                header("Location: ../templates/login.php");
                exit();
            }
        }
    }
}

$controller = new LoginController();
$controller->login();
