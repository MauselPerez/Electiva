<?php
require_once '../models/reports.php';

class ReportsController {
    private $reportsModel;

    public function __construct() {
        $this->reportsModel = new Reports();
    }

    // Listar todos los reportes
    public function getQuantityToDeliver() {
        $data = $this->reportsModel->getQuantityToDeliver();
        return $data[0]['quantity_to_deliver'];
    }

    public function getDelivered()
    {
        $data = $this->reportsModel->getDelivered();
        return $data[0]['delivered'];
    }

    public function getDeliveriesByMonth()
    {
        $data = $this->reportsModel->getDeliveriesByMonth();
        return $data;
    }


    // Registrar un nuevo reporte
    public function create($data) {
        
    }

    // Actualizar un reporte existente
    public function update($id, $data) {
    
    }

    // Eliminar un reporte
    public function delete($id) {
        
    }
}