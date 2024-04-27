<?php
session_start();

if (!isset($_SESSION['user'])) 
{
  header('Location: ../templates/login.php');
}
// Define el título de la página}
$title = "Crear usuario";
// Contenido específico de la página
ob_start();
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
                </div>
              </div>
              <div class="col-md-6 col-sm-12 col-12">
                <div class="form-group">
                  <label for="txt_Nombre">Edad</label>
                  <input type="text" class="form-control" id="txt_edad" name="txt_edad" placeholder="Edad">
                </div> 
              </div> 
              <div class="col-md-6 col-sm-6 col-12">
                <div class="form-group">
                  <label for="txt_cantEx">Correo Electronico</label>
                  <input type="email" class="form-control" id="txt_email" name="txt_email" placeholder="Correo Electrónico">
                </div> 
              </div> 
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
                  <select id="charges" name="charges" class="form-control select2bs4 select2-hidden-accessible" style="width: 100%;" data-select2-id="17" tabindex="-1" aria-hidden="true">
                    <option value="0" selected="selected">Seleccione Cargo</option>    

                  </select>
                </div>
              </div>
              <div class="col-md-6 col-sm-12 col-12">
                <div class="form-group">
                  <label>Departamento</label>
                  <select id="departments" name="departments" class="form-control select2bs4 select2-hidden-accessible" style="width: 100%;" data-select2-id="17" tabindex="-1" aria-hidden="true">
                    <option value="0" selected="selected">Seleccione Departamento</option>

                  </select>
                </div>
              </div>
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
            </div>
          </div>  

          <div class="card-footer">
            <button type="submit" id="btn_create_user" name="btn_create_user" class="btn btn-success">Crear Usuario</button>
            <button type="reset" class="btn btn-default">Limpiar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
  document.addEventListener("DOMContentLoaded", function(event) {
    load_charges();
    load_departments();
  });

  load_charges = async () => {
    try {
      const response = await fetch('../api/consults_api.php?query=get_all_charges');
      const data = await response.json();
      let html = '';
      data.forEach(element => {
        html += `<option value="${element.cod}">${element.nombre}</option>`;
      });
      $('#charges').append(html);
    } catch (error) {
      console.log(error);
    }
  }

  load_departments = async () => {
    try {
      const response = await fetch('../api/consults_api.php?query=get_all_department');
      const data = await response.json();
      let html = '';
      data.forEach(element => {
        html += `<option value="${element.cod}">${element.nombre}</option>`;
      });
      $('#departments').append(html);
    } catch (error) {
      console.log(error);
    }
  }
</script>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base.php';
?>

<script>
$(document).ready(function() {
  $("#frm_create_user").off('submit').on('submit', function(e) {
        e.preventDefault(); // Evitar que el formulario se envíe normalmente

        $("#btn_create_user").prop("disabled", true);

        // Obtener los datos del formulario
        var formData = new FormData(this);

        // Agregar la acción específica
        formData.append('action', 'InsertEmployee');

        // Enviar la solicitud AJAX
        $.ajax({
            url: '../api/empleados_api.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Manejar la respuesta del servidor
                console.log(response);
                if (response.success) 
                {
                  //Toastr
                  toastr.success(response.message);
                  //Limpiar todos los campos
                  $("#frm_create_user")[0].reset();
                } 
                else {
                    alert(response.message);
                }
            },
            error: function(error) {
                // Manejar errores
                console.log(error);
            }
        });

        return false;
    });
});
</script>


