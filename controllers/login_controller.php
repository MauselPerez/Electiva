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

    $userModel = new User();
    $user = $userModel->findUserByUsername($username);
    if ($user && (int) ($user['is_active'] ?? 0) === 1 && $userModel->verifyPassword($user['password'], $password)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user'] = $user['username'];
        $_SESSION['role'] = $user['role_id'] ?? null;
        $_SESSION['role_name'] = $user['rol'] ?? null;
        $_SESSION['role_names'] = $user['role_names'] ?? null;
        $_SESSION['document_number'] = $user['document_number'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name']  = $user['first_name'] . ' ' . $user['last_name'];

        if ($userModel->needsPasswordRehash($user['password'])) {
            $userModel->rehashPasswordForUser((int) $user['id'], $password);
        }

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
