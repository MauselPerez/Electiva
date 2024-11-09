<?php
// Define el título de la página
$title = "Reparto de meriendas";
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
            Entrega de Meriendas 
            <button href="snacks.php" class="btn-sm btn-primary" style="float: right; margin-top:3px;" id="return">
                <i class="fas fa-arrow-left"></i>  Regresar
            </button>
            <button type="button" class="btn-sm btn-primary" data-toggle="modal" data-target="#new_delivery" style="float: right; margin-top:3px; margin-right: 5px;">
                <i class="fas fa-save"></i>  Nueva Entrega
            </button>
        </h3>
        
    </div>
    <div class="col-md-12" id="title" style="padding-top: 20px;">
        <table id="table" class="display table table-bordered" style="width:100%">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Fecha de entrega</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Carlos Sanchez</td>
                    <td>2024-02-09</td>
                    <td style="background-color:green; color: white; text-align: center;">Entregado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Andres Sanchez</td>
                    <td>2024-02-09</td>
                    <td style="background-color:green; color: white; text-align: center;">Entregado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Carlos Sanchez</td>
                    <td>2024-02-10</td>
                    <td style="background-color:green; color: white; text-align: center;">Entregado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Andres Sanchez</td>
                    <td>2024-02-10</td>
                    <td style="background-color:green; color: white; text-align: center;">Entregado</td>
                    <td>
                        <a href="#" class="btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            </tbody>
            <tfoot class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Estudiante</th>
                    <th>Fecha de entrega</th>
                    <th>Estado</th>
                    <th style="width: 7%;"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal" id="new_delivery" tabindex="-1" role="dialog" aria-labelledby="DateRangeModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="DateRangeModalLabel">Registrar entrega de meriendas</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="POST">
				<div class="modal-body">
                    <div class="form-group">
                        <label for="student">Estudiante</label>
                        <select class="form-control" id="student" name="student">
                            <option value="1">Carlos Sanchez</option>
                            <option value="2">Andres Sanchez</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Fecha de entrega</label>
                        <select name="" id="" class="form-control">
                            <option value="2024-02-09">2024-02-09</option>
                            <option value="2024-02-10">2024-02-10</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="state">Estado</label>
                        <select class="form-control" id="state" name="state">
                            <option value="1">Entregado</option>
                            <option value="2">No entregado</option>
                        </select>
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
        $('#table').DataTable({
            "order": [[ 2, "desc" ]]
        });

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
