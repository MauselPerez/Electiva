<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- daterange picker -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="../templates/AdminLTE-3.0.5/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">


</head>
<body class="sidebar-collapse sidebar-mini">

<?php include "includes/config.php"; ?>

<!-- Site wrapper -->
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand <?php echo $headerStyle; ?>">
    <?php 
      include "includes/header.php";
    ?>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar <?php echo $lateralStyle; ?> elevation-4">
    <?php 
    include "includes/lateralaside.php";
     ?>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>¡Bienvenido!</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
              
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <div class="col-md-4 col-sm-6">
                <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Registrar Empleado</a>
                    </li>                    
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                            <!-- Inicio Formulario -->
                            <form role="form" name="frm_prods" id="frm_prods" method="POST" action="productos_crear.php" enctype="multipart/form-data">
                                        <div class="card-body">
                                                    <!-- Control Numero Documento  -->
                                            <div class="row">
                                                        <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                                <label>Documento</label>
                                                                <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                                        </div>
                                                                        <input type="text" class="form-control" id="txt_doc" required name="txt_doc" data-inputmask="&quot;mask&quot;: &quot;9999999999&quot;" data-mask="" im-insert="true">
                                                                </div>
                                                            </div>
                                                        </div>  

                                                    <!-- Control Nombre  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                        <label for="txt_Nombre">Nombres</label>
                                                        <input type="text" class="form-control" id="txt_name" required name="txt_name" placeholder="Nombres">
                                                        </div> 
                                                    </div>  
                                                    <!-- Control Apellidos  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                            <label for="txt_apellidos">Apellidos</label>
                                                            <input type="text" class="form-control" id="txt_laname" required name="txt_laname" placeholder="Apellidos">
                                                        </div> 
                                                    </div> 

                                                        <!-- Control Edad  -->          
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                    <label for="txt_Nombre">Edad</label>
                                                    <input type="number" id="txt_edad" name="txt_edad" required class="form-control" value="18" step="1">
                                                    </div>

                                                    <!-- Control Genero  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                           <label>Sexo</label>
                                                                <select class="custom-select">
                                                                    <option></option>
                                                                    <option>Masculino</option>
                                                                    <option>Femenino</option>              
                                                                </select>
                                                        </div>
                                                    </div> 

                                                    <!-- Control Telefono  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                            <div class="form-group">
                                                                <label>Telefono</label>
                                                                <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                                        </div>
                                                                        <input type="text" class="form-control" id="txt_phone" name="txt_phone" required data-inputmask="&quot;mask&quot;: &quot;(999) 999-9999&quot;" data-mask="" im-insert="true">
                                                                </div>
                                                            </div> 
                                                    </div> 
                                                         <!-- Control Fecha Admision -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        
                                                        <div class="form-group">
                                                            <label>Fecha de Admision:</label>
                                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                                    <input type="text" required id="txt_fadmin" name="txt_fadmin" class="form-control datetimepicker-input" data-target="#reservationdate">
                                                                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                        </div>
                                                            </div>
                                                        </div>
                                                            
                                                    </div> 

                                                    <!-- Control Email -->
                                                    <div class="col-md-12 col-sm-12 col-12">
                                                        <label>Correo Electronico</label>
                                                            <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                                    </div>
                                                                <input type="email" class="form-control" required id="txt_email" name="txt_email"  placeholder="Correo Electronico">
                                                            </div>
                                                    </div> 

                                                       


                                                    <!-- Control FileUpload  -->                
                                                    <div class="col-md-12 col-sm-12 col-12">
                                                        <div class="form-group">
                                                        <label for="txtFile">Subir Foto</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                            <input type="file" class="custom-file-input" required id="txt_File" name="txt_File">
                                                            <label class="custom-file-label" for="txt_File">Seleccionar</label>
                                                            </div>                    
                                                        </div>
                                                        </div>
                                                    </div>


                                            </div>  <!-- /.fin row -->   
                                                    
                                                </div>  
                                                <!-- /.fin card-body -->

                                            <div class="card-footer">
                                                <button type="submit" id="btn_regist" class="btn btn-success">Registrar Empleado</button>
                                                <button type="reset" class="btn btn-default">Limpiar</button>
                                            </div>

                            </form> <!-- /.fin Form -->
                            <!-- /Fin Formulario -->
                        </div>
                    </div>
                </div>
                <!-- /.card -->
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">  Editar Empleado</a>
                    </li>                    
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                            <!-- Inicio Formulario -->
                            <form role="form" name="frm_prods" id="frm_prods" method="POST" action="productos_crear.php" enctype="multipart/form-data">
                                        <div class="card-body">
                                                    
                                            <div class="row">
                                                <!-- Control Numero Documento  -->
                                                <div class="col-md-12 col-sm-12 col-12">
                                                            <div class="form-group">
                                                                    <label>Buscar por documento</label>
                                                                    <div class="input-group mb-3">
                                                                    <input type="text" class="form-control" id="txt_search" name="txt_search" data-inputmask="&quot;mask&quot;: &quot;9999999999&quot;" data-mask="" im-insert="true">
                                                                        <button class="btn btn-navbar" id="btn_search" name ="btn_search" type="submit">
                                                                            <i class="fas fa-search"></i>
                                                                        </button>
                                                                        
                                                                    </div>
                                                            </div>
                                                        </div> 

                                                <!-- Control Numero Documento  -->
                                                        <div class="col-md-6 col-sm-12 col-12">
                                                            <div class="form-group">
                                                                    <label>Documento</label>
                                                                    <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                                            </div>
                                                                            <input type="text" class="form-control" id="txt_doc" name="txt_doc" data-inputmask="&quot;mask&quot;: &quot;9999999999&quot;" data-mask="" im-insert="true">
                                                                    </div>
                                                            </div>
                                                        </div>  

                                                    <!-- Control Nombre  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                        <label for="txt_Nombre">Nombres</label>
                                                        <input type="text" class="form-control" id="txt_name" name="txt_name" placeholder="Nombres">
                                                        </div> 
                                                    </div>  
                                                    <!-- Control Apellidos  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                            <label for="txt_apellidos">Apellidos</label>
                                                            <input type="text" class="form-control" id="txt_laname" name="txt_laname" placeholder="Apellidos">
                                                        </div> 
                                                    </div> 

                                                        <!-- Control Edad  -->          
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                    <label for="txt_Nombre">Edad</label>
                                                    <input type="number" id="txt_edad" name="txt_edad" class="form-control" value="18" step="1">
                                                    </div>

                                                    <!-- Control Genero  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                           <label>Sexo</label>
                                                                <select class="custom-select">
                                                                    <option></option>
                                                                    <option>Masculino</option>
                                                                    <option>Femenino</option>              
                                                                </select>
                                                        </div>
                                                    </div> 

                                                    <!-- Control Telefono  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                            <div class="form-group">
                                                                <label>Telefono</label>
                                                                <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                                        </div>
                                                                        <input type="text" class="form-control" id="txt_phone" name="txt_phone" data-inputmask="&quot;mask&quot;: &quot;(999) 999-9999&quot;" data-mask="" im-insert="true">
                                                                </div>
                                                            </div> 
                                                    </div> 
                                                        

                                                    <!-- Control Email -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <label>Correo Electronico</label>
                                                            <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                                    </div>
                                                                <input type="email" class="form-control" id="txt_email" name="txt_email"  placeholder="Correo Electronico">
                                                            </div>
                                                    </div> 

                                                        <!-- Control Email -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        
                                                        <div class="form-group">
                                                            <label>Fecha de Admision:</label>
                                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                                    <input type="text" id="txt_fadmin" name="txt_fadmin" class="form-control datetimepicker-input" data-target="#reservationdate">
                                                                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                        </div>
                                                            </div>
                                                        </div>
                                                            
                                                    </div> 


                                                    <!-- Control FileUpload  -->                
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
                                                    
                                                </div>  
                                                <!-- /.fin card-body -->

                                            <div class="card-footer">
                                                <button type="submit" id="btn_regist" class="btn btn-info">Actualizar Empleado</button>
                                                <button type="reset" class="btn btn-default">Limpiar</button>
                                            </div>

                            </form> <!-- /.fin Form -->
                            <!-- /Fin Formulario -->
                        </div>
                    </div>
                </div>
                <!-- /.card -->
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Eliminar Empleado</a>
                    </li>                    
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                            <!-- Inicio Formulario -->
                            <form role="form" name="frm_prods" id="frm_prods" method="POST" action="productos_crear.php" enctype="multipart/form-data">
                                        <div class="card-body">
                                                    
                                            <div class="row">
                                                <!-- Control Numero Documento  -->
                                                <div class="col-md-12 col-sm-12 col-12">
                                                            <div class="form-group">
                                                                    <label>Buscar por documento</label>
                                                                    <div class="input-group mb-3">
                                                                    <input type="text" class="form-control" id="txt_search" name="txt_search" data-inputmask="&quot;mask&quot;: &quot;9999999999&quot;" data-mask="" im-insert="true">
                                                                        <button class="btn btn-navbar" id="btn_search" name ="btn_search" type="submit">
                                                                            <i class="fas fa-search"></i>
                                                                        </button>
                                                                        
                                                                    </div>
                                                            </div>
                                                        </div> 

                                                <!-- Control Numero Documento  -->
                                                        <div class="col-md-6 col-sm-12 col-12">
                                                            <div class="form-group">
                                                                    <label>Documento</label>
                                                                    <div class="input-group">
                                                                            <div class="input-group-prepend">
                                                                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                                                            </div>
                                                                            <input type="text" class="form-control" id="txt_doc" name="txt_doc" data-inputmask="&quot;mask&quot;: &quot;9999999999&quot;" data-mask="" im-insert="true">
                                                                    </div>
                                                            </div>
                                                        </div>  

                                                    <!-- Control Nombre  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                        <label for="txt_Nombre">Nombres</label>
                                                        <input type="text" class="form-control" id="txt_name" name="txt_name" placeholder="Nombres">
                                                        </div> 
                                                    </div>  
                                                    <!-- Control Apellidos  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                            <label for="txt_apellidos">Apellidos</label>
                                                            <input type="text" class="form-control" id="txt_laname" name="txt_laname" placeholder="Apellidos">
                                                        </div> 
                                                    </div> 

                                                        <!-- Control Edad  -->          
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                    <label for="txt_Nombre">Edad</label>
                                                    <input type="number" id="txt_edad" name="txt_edad" class="form-control" value="18" step="1">
                                                    </div>

                                                    <!-- Control Genero  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <div class="form-group">
                                                           <label>Sexo</label>
                                                                <select class="custom-select">
                                                                    <option></option>
                                                                    <option>Masculino</option>
                                                                    <option>Femenino</option>              
                                                                </select>
                                                        </div>
                                                    </div> 

                                                    <!-- Control Telefono  -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                            <div class="form-group">
                                                                <label>Telefono</label>
                                                                <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                                        </div>
                                                                        <input type="text" class="form-control" id="txt_phone" name="txt_phone" data-inputmask="&quot;mask&quot;: &quot;(999) 999-9999&quot;" data-mask="" im-insert="true">
                                                                </div>
                                                            </div> 
                                                    </div> 
                                                        

                                                    <!-- Control Email -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        <label>Correo Electronico</label>
                                                            <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                                    </div>
                                                                <input type="email" class="form-control" id="txt_email" name="txt_email"  placeholder="Correo Electronico">
                                                            </div>
                                                    </div> 

                                                        <!-- Control Email -->
                                                    <div class="col-md-6 col-sm-12 col-12">
                                                        
                                                        <div class="form-group">
                                                            <label>Fecha de Admision:</label>
                                                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                                    <input type="text" id="txt_fadmin" name="txt_fadmin" class="form-control datetimepicker-input" data-target="#reservationdate">
                                                                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                                        </div>
                                                            </div>
                                                        </div>
                                                            
                                                    </div> 


                                                    <!-- Control FileUpload  -->                
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
                                                    
                                                </div>  
                                                <!-- /.fin card-body -->

                                            <div class="card-footer">
                                                <button type="submit" id="btn_regist" class="btn btn-danger">Eliminar Empleado</button>
                                                <button type="reset" class="btn btn-default">Limpiar</button>
                                            </div>

                            </form> <!-- /.fin Form -->
                            <!-- /Fin Formulario -->
                        </div>
                    </div>
                </div>
                <!-- /.card -->
                </div>
            </div>

            
        </div>
    

      


    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <?php 
      include "includes/footer.php";
     ?>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="../templates/AdminLTE-3.0.5/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../templates/AdminLTE-3.0.5/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../templates/AdminLTE-3.0.5/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../templates/AdminLTE-3.0.5/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- AdminLTE App -->
<script src="../templates/AdminLTE-3.0.5/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../templates/AdminLTE-3.0.5/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../templates/AdminLTE-3.0.5/dist/js/demo.js"></script>
<!-- Select2 -->
<script src="../templates/AdminLTE-3.0.5/plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="../templates/AdminLTE-3.0.5/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="../templates/AdminLTE-3.0.5/plugins/moment/moment.min.js"></script>
<script src="../templates/AdminLTE-3.0.5/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<!-- date-range-picker -->
<script src="../templates/AdminLTE-3.0.5/plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="../templates/AdminLTE-3.0.5/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../templates/AdminLTE-3.0.5/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Bootstrap Switch -->
<script src="../templates/AdminLTE-3.0.5/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>


<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('yyyy/mm/dd', { 'placeholder': 'yyyy/mm/dd' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('yyyy/mm/dd', { 'placeholder': 'yyyy/mm/dd' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date range picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });
    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'YYYY/MM/DD hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })
    
    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    });

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

  })
</script>
</body>
</html>