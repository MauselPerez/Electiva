<?php
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
