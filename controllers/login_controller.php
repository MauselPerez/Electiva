<?php
session_start();

require_once '../config/db.php';
require_once '../models/user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Por favor ingrese su usuario y contraseña.";
        header("Location: ../templates/login.php");
        exit();
    }

    $userModel = new User($db);
    $user = $userModel->findUserByUsername($username);
    if ($user && $user['password'] == sha1($password)) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 1;
        $_SESSION['document_number'] = $user['document_number'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];

        header("Location: ../views_admin/index.php");
        exit();
    } else {
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: ../templates/login.php");
        exit();
    }
} else {
    header("Location: ../templates/login.php");
    exit();
}
