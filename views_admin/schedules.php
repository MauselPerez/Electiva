<?php
// Define el título de la página
$title = "Planificaciones";
// Contenido específico de la página
ob_start();
?>
<style>
    #title {
        background-color: white;
        border: 2px solid gray;
        border-radius: 10px;
        margin-top: 5px;
    }
</style>
<div class="row">
    <div class="col-md-12" id="title">
        <h3 class="page-header" style="padding-top: 5px;">
            Cronograma de entregas 
            <button class="btn-sm btn-primary" style="float: right; margin-top:3px;" id="return">
                <i class="fas fa-arrow-left"></i>  Regresar
            </button>
            <button type="button" class="btn-sm btn-primary" data-toggle="modal" data-target="#new_schedule" style="float: right; margin-top:3px; margin-right:5px;">
                <i class="fas fa-save"></i>  Nueva Planificacion
            </button>
        </h3>
        
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha de inicio</th>
                    <th>Fecha final</th>
                    <th>Día de entrega</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>2024-02-05</td>
                    <td>2024-06-28</td>
                    <td>2024-02-09</td>
                    <td style="background-color:green; color: white; text-align: center;">Ejecutado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>2024-02-05</td>
                    <td>2024-06-28</td>
                    <td>2024-02-10</td>
                    <td style="background-color:green; color: white; text-align: center;">Ejecutado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>2024-02-05</td>
                    <td>2024-06-28</td>
                    <td>2024-02-11</td>
                    <td style="background-color:red; color: white; text-align: center;">No Ejecutado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>2024-02-05</td>
                    <td>2024-06-28</td>
                    <td>2024-02-12</td>
                    <td style="background-color:green; color: white; text-align: center;">Ejecutado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha de inicio</th>
                    <th>Fecha final</th>
                    <th>Día de entrega</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal" id="new_schedule" tabindex="-1" role="dialog" aria-labelledby="DateRangeModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="DateRangeModalLabel">Seleccionar rango de fechas</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="star_date">Fecha inicio</label>
                        <input type="date" class="form-control" id="star_date" name="star_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">Fecha fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-primary">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
        $('#table').DataTable();

        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });
    });
</script>
<?php
// Captura el contenido en una variable
$content = ob_get_clean();

// Incluye la plantilla base
include '../templates/base_modules.php';
?>
