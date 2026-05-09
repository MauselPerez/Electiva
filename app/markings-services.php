<?php
    session_start();
    include "../controllers/controller_consultas_api.php";

    class MarkingsAPI{
        function get_all_markings()
        {
            $objDB = new ExtraerDatos();
            $data = [];

            $data = $objDB->get_all_markings();

            $markings = [];
            
            if ($data) 
            {
                foreach ($data as $d) 
                {
                    $markings[] = array(
                        'citizen_card' => $d['document_number'],
                        'name_employee' => $d['first_name'],
                        'last_name_employee' => $d['last_name'],
                        'name_charge' => $d['charge'],
                        'name_department' => $d['department'],
                        'electronic_mail' => $d['email'],
                        'entry' => $d['entry_date'],
                        'exit' => $d['departure_date'],
                        'state' => $d['status']
                    );
                }
                return $markings;
            }
        }
    }
?>