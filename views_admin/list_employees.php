<?php
session_start();
include "../public/includes/mistakes.php";
see_errors();
// Define el título de la página
$title = "Página de Usuario";
// Contenido específico de la página
ob_start();
?>
<div class="row">
    <div class="col-md-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Listado de Empleados</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4"><div class="row"><div class="col-sm-12 col-md-6"><div class="dataTables_length" id="example1_length"><label>Mostrar <select name="example1_length" aria-controls="example1" class="custom-select custom-select-sm form-control form-control-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> Entradas</label></div></div><div class="col-sm-12 col-md-6"><div id="example1_filter" class="dataTables_filter"><label>Buscar:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1"></label></div></div></div><div class="row"><div class="col-sm-12"><table id="example1" class="table table-bordered table-striped dataTable dtr-inline" role="grid" aria-describedby="example1_info">
                  <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="ID: activate to sort column descending">Cedula</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Browser: activate to sort column ascending">Nombres</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Platform(s): activate to sort column ascending">Apellidos(s)</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="Engine version: activate to sort column ascending">Edad</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Telefono</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Correo Electronico</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Foto</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Fecha de Admision</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Cargo</th>
                    <th class="sorting" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-label="CSS grade: activate to sort column ascending">Departamento</th>
                  </tr>
                  </thead>
                <tbody>                  
                  <tr role="row" class="odd">
                    <td tabindex="0" class="sorting_1">1004377116</td>
                    <td>Jorge Luis</td>
                    <td>Acosta Gonzalez</td>
                    <td>23</td>
                    <td>3145780355</td>
                    <td>jorgeacosta@infotephvg.edu.co</td>
                    <td>NULL</td>
                    <td>13-11-2023</td>
                    <td>Programador</td>
                    <td>Sistemas</td>
                  </tr>
                  <tr role="row" class="odd">
                    <td tabindex="0" class="sorting_1">1004376216</td>
                    <td>Jhon Sebastian</td>
                    <td>Jimenez Gonzalez</td>
                    <td>27</td>
                    <td>3145090906</td>
                    <td>jhonjimenez@infotephvg.edu.co</td>
                    <td>NULL</td>
                    <td>03-10-2023</td>
                    <td>Programador</td>
                    <td>Sistemas</td>
                  </tr>
                  
                </tbody>
                  <tfoot>
                  <tr>
                    <th rowspan="1" colspan="1">ID</th>
                    <th rowspan="1" colspan="1">Nombres</th>
                    <th rowspan="1" colspan="1">Apellidos</th>
                    <th rowspan="1" colspan="1">Edad</th>
                    <th rowspan="1" colspan="1">Telefono</th>
                    <th rowspan="1" colspan="1">Correo Electronico</th>
                    <th rowspan="1" colspan="1">Foto</th>
                    <th rowspan="1" colspan="1">Fecha de Admision</th>
                    <th rowspan="1" colspan="1">Cargo</th>
                    <th rowspan="1" colspan="1">Departamento</th>

                </tr>
                  </tfoot>
                </table>
            </div>
        </div>

    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="dataTables_info" id="example1_info" role="status" aria-live="polite">Mostrando 1 a 10 de 57 entradas</div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example1_paginate">
                                                        <ul class="pagination">
                                                            <li class="paginate_button page-item previous disabled" id="example1_previous">
                                                                <a href="#" aria-controls="example1" data-dt-idx="0" tabindex="0" class="page-link">Atras</a>
                                                            </li>
                                                            <li class="paginate_button page-item active"><a href="#" aria-controls="example1" data-dt-idx="1" tabindex="0" class="page-link">1</a>
                                                        </li>
                                                        <li class="paginate_button page-item ">
                                                            <a href="#" aria-controls="example1" data-dt-idx="2" tabindex="0" class="page-link">2</a>
                                                        </li><li class="paginate_button page-item ">
                                                            <a href="#" aria-controls="example1" data-dt-idx="3" tabindex="0" class="page-link">3</a>
                                                        </li>
                                                        <li class="paginate_button page-item "><a href="#" aria-controls="example1" data-dt-idx="4" tabindex="0" class="page-link">4</a>
                                                    </li>
                                                    <li class="paginate_button page-item "><a href="#" aria-controls="example1" data-dt-idx="5" tabindex="0" class="page-link">5</a>
                                                </li>
                                                <li class="paginate_button page-item "><a href="#" aria-controls="example1" data-dt-idx="6" tabindex="0" class="page-link">6</a>
                                            </li>
                                            <li class="paginate_button page-item next" id="example1_next">
                                                <a href="#" aria-controls="example1" data-dt-idx="7" tabindex="0" class="page-link">Siguiente</a>
                                            </li>
                                            </ul>
                </div>
                </div>
                </div>
            </div>
        </div>
                            <!-- /.card-body -->
    </div>





        
    </div>
</div>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base.php';
?>
