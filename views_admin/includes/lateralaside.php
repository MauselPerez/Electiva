<!-- Brand Logo -->
<a href="index.php" class="brand-link">
  <img src="../imgs/logo.JPG" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
  <span class="brand-text font-weight-light">INFO<b>APS</b></span>
</a>

<!-- Sidebar -->
<div class="sidebar ">
  <!-- Sidebar user (optional) -->
  <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    <div class="image">
<?php  
      $name_image = basename($_SESSION['img']);
?>
      <img src="../imgs/usuarios/<?=$name_image?>" class="img-circle elevation-2" alt="Usuario">
    </div>
    <div class="info">
      <a href="#" class="d-block"><?= $_SESSION['name']?></a>
    </div>
  </div>

  <!-- Sidebar Menu -->
  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

      <!-- MENU MEDICINAS -->
      <!--<li class="nav-item has-treeview">
        <a href="#" class="nav-link ">
          <i class="nav-icon fas fa-notes-medical"></i> 
          <p>
            Empleados
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="create_employees.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Crear Empleado</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="list_employees.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Listado de Empleados</p>
            </a>
          </li>
        </ul>
      </li> -->

      <!-- MENU CON ENFERMOS -->
      <!--<li class="nav-item has-treeview">
        <a href="#" class="nav-link ">
          <i class="nav-icon fas fa-paw"></i>
          <p>
            Marcaciones
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="../views\markings\index.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Listado</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="clientes_listado.php" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Nuevo Enfermo</p>
            </a>
          </li>
        </ul>
      </li> -->

      <!-- Menu para las solicitudes -->
      <li class="nav-item">
        <a href="index.php" class="nav-link">
          <i class="nav-icon fas fa-sign-out-alt"></i>
          <p>
            MARCACIONES
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
          <p>
            EMPLEADOS
          </p>
        </a>
      </li>

    </ul>
  </nav>
  <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->