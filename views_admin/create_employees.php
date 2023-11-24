<?php
session_start();
include "../app/empleados-services.php";

$objConsultAPI = new EmpleadosAPI(); //Crear Objeto de la clase loginAPI
$departments = $objConsultAPI->get_all_department();
$charges = $objConsultAPI->get_all_charges();

// Define el título de la página}
$title = "Crear usuario";
// Contenido específico de la página
ob_start();
?>



<?php
    if (isset($_POST)  AND isset($_POST['txt_name'])) 
    {
        $action = "InsertEmployees";
        $objConsultAPI->handleRequest($_POST);
    }
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title" style="color: black;"> Crear usuario </h6>
            </div>
            <div class="card-body">


            <form action="create_employees.php" method="POST" id="frm_create_user" name="frm_create_user">
              <div class="card-body">

                <div class="row">
                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label for="txt_refer">Nombres</label>
                      <input type="text" class="form-control" id="txt_name" name="txt_name" placeholder="Nombre">
                    </div> 
                  </div>  

                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label for="txt_refer">Apellidos</label>
                      <input type="text" class="form-control" id="txt_lastname" name="txt_lastname" placeholder="Apellidos">
                    </div> 
                  </div> 

                  <!-- Control Inputbox ejemplo -->
                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label for="txt_Nombre">Identificación</label>
                      <input type="text" class="form-control" id="txt_cc" name="txt_cc" placeholder="Identificación">
                    </div> 
                  </div>  


                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label for="txt_refer">Usuario</label>
                      <input type="text" class="form-control" id="txt_usern" name="txt_usern" placeholder="Usuario">
                    </div> 
                  </div>

                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label for="txt_refer">Contraseña</label>
                      <input type="password" class="form-control" id="txt_passw" name="txt_passw" placeholder="Contraseña">
                    </div> 
                  </div>                  

                    <div class="col-md-6">
                          <div class="form-group">
                          <label>Telefono:</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                            <input type="text" class="form-control" id="txt_num" name="txt_num" data-inputmask="&quot;mask&quot;: &quot;(999) 999-9999&quot;" data-mask="" im-insert="true">
                          </div>
                          <!-- /.input group -->
                        </div>
                    </div>

                  
                                      
                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label for="txt_Nombre">Edad</label>
                      <input type="text" class="form-control" id="txt_edad" name="txt_edad" placeholder="Edad">
                    </div> 
                  </div> 

                 <!-- Control cantidad  -->
                  <div class="col-md-6 col-sm-6 col-12">
                    <div class="form-group">
                      <label for="txt_cantEx">Correo Electronico</label>
                      <input type="email" class="form-control" id="txt_email" name="txt_email" placeholder="Correo Electrónico">
                    </div> 
                  </div> 

                  <!-- Control VALOR -->
                  <div class="col-md-6 col-sm-6 col-12">
                  <div class="form-group">
                  <label>Fecha de Admision:</label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" id="txt_fechadmin" name="txt_fechadmin" data-target="#reservationdate">
                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                  </div> 


                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label>Cargo</label>
                      <select class="form-control select2bs4 select2-hidden-accessible" style="width: 100%;" id="txt_charges" name="txt_charges" data-select2-id="17" tabindex="-1" aria-hidden="true">
                        <option selected="selected">Seleccione Cargo</option>
                        <?php foreach ($charges as $d){
                          ?>
                          <option value="<?=$d['cod']?>"><?=$d['nombre']?></option>
                          <?php
                        }
                        ?>                    
                        </select>
                    </div>
                  </div>

                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label>Departamento</label>
                      <select class="form-control select2bs4 select2-hidden-accessible" style="width: 100%;" id="txt_dept" name="txt_dept" data-select2-id="17" tabindex="-1" aria-hidden="true">
                        <option selected="selected">Seleccione Departamento</option>
                        <?php foreach ($departments as $d){
                          ?>
                          <option value="<?=$d['cod']?>"><?=$d['nombre']?></option>
                          <?php
                        }
                        ?>                    
                        </select>
                    </div>
                  </div>

                  <!-- Control FileUpload ejemplo -->                
                  <div class="col-md-6 col-sm-12 col-12">
                    <div class="form-group">
                      <label for="txtFile">Subir Foto</label>
                      <div class="input-group">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="txt_File" name="txt_File">
                          <label class="custom-file-label" for="txt_File">Seleccionar</label>
                        </div>                    
                      </div>
                    </div>
                  </div>


                </div>  <!-- /.fin row -->   
                
              </div>  <!-- /.fin card-body -->

              <div class="card-footer">
                <button type="submit" id="btn_create_user" class="btn btn-success">Crear Usuario</button>
                <button type="reset" class="btn btn-default">Limpiar</button>
              </div>

            </form> <!-- /.fin Form -->

            </form>
                
            </div>
        </div>
    </div>
</div>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base.php';
?>


