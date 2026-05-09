<?php
    session_start();
    include "../controllers/controller_consultas_api.php";

    class loginAPI{
        function validate_users()
        {
            $objDB = new ExtraerDatos();
            $data = [];

            if (isset($_POST))
            {
                $data = $objDB->users_validate($_POST['user']);
            }
            
            if (!empty($data)) 
            {
                if ($data[0]['password'] == sha1($_POST['password'])) 
                {   
                    $_SESSION['id'] = $data[0]['user_id'];
                    $_SESSION['user'] = $data[0]['user'];
                    $_SESSION['name'] = $data[0]['name_employee'];
                    $_SESSION['document_number'] = $data[0]['document_number'];
                    $_SESSION['charges'] = $data[0]['id_charges'];
                    $_SESSION['departments'] = $data[0]['id_departments'];
                    $_SESSION['img'] = $data[0]['img'];
                    
                    header("Location: ../views_admin/index.php");
                }
                else 
                {
                    header("Location: ../templates/login.php?error=1");
                }
            }
            else 
            {
                header("Location: ../templates/login.php?error=2");
            }
        }
    }
