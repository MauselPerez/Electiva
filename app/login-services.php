<?php
    session_start();
    require_once "../config/db.php";
    require_once "../models/user.php";

    class loginAPI{
        function validate_users()
        {
            $username = trim($_POST['user'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                header("Location: ../templates/login.php?error=1");
                exit();
            }

            $userModel = new User();
            $user = $userModel->findUserByUsername($username);

            if ($user && (int) ($user['is_active'] ?? 0) === 1 && $userModel->verifyPassword($user['password'], $password)) {
                session_regenerate_id(true);
                $_SESSION['id'] = $user['id'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['username'];
                $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['document_number'] = $user['document_number'];
                $_SESSION['role'] = $user['role_id'] ?? null;
                $_SESSION['role_name'] = $user['rol'] ?? null;
                $_SESSION['role_names'] = $user['role_names'] ?? null;

                header("Location: ../views_admin/index.php");
                exit();
            }

            header("Location: ../templates/login.php?error=2");
            exit();
        }
    }
