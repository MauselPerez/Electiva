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
      <!-- Menu para las solicitudes -->
      <li class="nav-item">
        <a href="index.php" class="nav-link">
          <i class="nav-icon fas fa-hamburger"></i>
          <p>
            ENTREGAS
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="list_employees.php" class="nav-link">
        <i class="nav-icon fas fa-users"></i>
          <p>
            EMPLEADOS
          </p>
        </a>
      </li>
      <li class="nav-item">
        <a href="list_students.php" class="nav-link">
        <i class="nav-icon fas fa-address-card"></i>
          <p>
            ESTUDIANTES
          </p>
        </a>
      </li>

    </ul>
  </nav>
  <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->