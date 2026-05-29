<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../templates/login.php');
    exit();
}

require_once '../controllers/organizational_units_controller.php';

$controller = new OrganizationalUnitsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create') {
    $controller->create($_POST);
    header('Location: organizational_units.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'update') {
    $controller->update($_POST['id'], $_POST);
    header('Location: organizational_units.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
    header('Location: organizational_units.php');
}

$units = $controller->index();
$unitTypes = $controller->getAllUnitTypes();
$parentCandidates = $controller->getParentCandidates();
$title = 'Organigrama';
ob_start();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<style>
    .module-page { padding: 18px 8px 28px 0; }
    .module-hero {
        background: linear-gradient(135deg, #28313b 0%, #485461 60%, #5f7a96 100%);
        border-radius: 20px;
        box-shadow: 0 18px 42px rgba(28, 38, 49, 0.18);
        color: #fff;
        margin: 10px 0 18px;
        overflow: hidden;
        position: relative;
    }
    .module-hero::after {
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 36%);
        content: "";
        inset: 0;
        pointer-events: none;
        position: absolute;
    }
    .module-hero-body { padding: 24px 26px; position: relative; z-index: 1; }
    .module-title { font-size: 1.85rem; font-weight: 700; margin-bottom: 8px; }
    .module-text { color: rgba(255, 255, 255, 0.86); margin-bottom: 0; max-width: 760px; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; margin-top: 14px; }
    .hero-btn { border-radius: 999px; font-weight: 700; padding: 10px 16px; }
    .panel-card {
        background: #fff;
        border: 1px solid #e7edf3;
        border-radius: 20px;
        box-shadow: 0 14px 34px rgba(17, 24, 39, 0.06);
        margin-bottom: 18px;
        overflow: hidden;
    }
    .panel-header {
        align-items: center;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: space-between;
        padding: 18px 20px 14px;
    }
    .panel-title { color: #28313b; font-size: 1.08rem; font-weight: 700; margin: 0; }
    .panel-subtitle { color: #6c7a89; font-size: 0.9rem; margin: 4px 0 0; }
    .panel-body { padding: 18px 20px 20px; }
    .table-shell { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-shell table { min-width: 1040px; }
    .org-visual-wrap {
        background: linear-gradient(180deg, #f8fbff 0%, #f2f7fb 100%);
        border: 1px solid #dbe7f3;
        border-radius: 16px;
        padding: 16px;
    }
    .org-root-list,
    .org-root-list ul {
        list-style: none;
        margin: 0;
        padding-left: 0;
    }
    .org-root-list ul {
        border-left: 1px dashed #b7c9dc;
        margin-left: 20px;
        padding-left: 18px;
    }
    .org-root-list li {
        margin: 10px 0;
        position: relative;
    }
    .org-root-list li::before {
        background: #b7c9dc;
        content: "";
        height: 1px;
        left: -18px;
        position: absolute;
        top: 20px;
        width: 18px;
    }
    .org-root-list > li::before {
        display: none;
    }
    .org-node {
        align-items: center;
        background: #fff;
        border: 1px solid #d2e1f0;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(18, 32, 53, 0.06);
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px 12px;
    }
    .org-node.inactive {
        opacity: 0.62;
    }
    .org-name {
        color: #1f2d3d;
        font-weight: 700;
    }
    .org-type {
        background: #e8f1fb;
        border-radius: 999px;
        color: #265d93;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 4px 10px;
        text-transform: uppercase;
    }
    .org-status {
        border-radius: 999px;
        font-size: 0.73rem;
        font-weight: 700;
        margin-left: auto;
        padding: 4px 10px;
    }
    .org-status.active {
        background: #d6f5e0;
        color: #17653f;
    }
    .org-status.inactive {
        background: #eceff3;
        color: #4e5968;
    }
    .org-empty {
        color: #6b7b8c;
        font-style: italic;
        margin: 0;
    }
    @media (max-width: 575.98px) {
        .module-hero-body { padding: 22px 18px; }
        .module-title { font-size: 1.45rem; }
        .hero-actions { justify-content: flex-start; }
        .table-shell table { min-width: 900px; }
    }
</style>

<div class="container-fluid module-page">
    <div class="module-hero">
        <div class="row align-items-center no-gutters module-hero-body">
            <div class="col-lg-8">
                <div class="module-title">Organigrama institucional</div>
                <p class="module-text">Administra unidades organizacionales, tipos y jerarquía para que todo el sistema escale con la estructura real de la universidad.</p>
            </div>
            <div class="col-lg-4">
                <div class="hero-actions">
                    <button type="button" class="btn btn-success hero-btn" data-toggle="modal" data-target="#new_unit">
                        <i class="fas fa-plus mr-1"></i> Nueva unidad
                    </button>
                    <button class="btn btn-warning hero-btn" id="return">
                        <i class="fas fa-arrow-left mr-1"></i> Regresar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Listado de unidades</h3>
                <p class="panel-subtitle">Puedes gestionar padre, tipo, estado y nombre de cada nodo del árbol.</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-shell">
                <table id="table" class="display table table-bordered" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Unidad</th>
                            <th>Tipo</th>
                            <th>Unidad padre</th>
                            <th>Estado</th>
                            <th style="width: 7%;"></th>
                            <th style="width: 7%;"></th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($units as $unit) { ?>
                        <tr>
                            <td><?= htmlspecialchars($unit['id']) ?></td>
                            <td><?= htmlspecialchars($unit['name']) ?></td>
                            <td><?= htmlspecialchars($unit['unit_type']) ?></td>
                            <td><?= htmlspecialchars($unit['parent_name'] ?? 'RAIZ') ?></td>
                            <td class="text-center">
<?php if ((int) $unit['is_active'] === 1) { ?>
                                <span class="badge badge-success" style="padding:8px;">Activo</span>
<?php } else { ?>
                                <span class="badge badge-secondary" style="padding:8px;">Inactivo</span>
<?php } ?>
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-primary btn-sm"
                                        onclick="show_edit(this)"
                                        data-id="<?= htmlspecialchars($unit['id']) ?>"
                                        data-name="<?= htmlspecialchars($unit['name']) ?>"
                                        data-type-id="<?= htmlspecialchars($unit['organizational_unit_type_id']) ?>"
                                        data-parent-id="<?= htmlspecialchars($unit['parent_id']) ?>"
                                        data-is-active="<?= htmlspecialchars($unit['is_active']) ?>">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="delete_unit(<?= (int) $unit['id'] ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
<?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Ilustración gráfica del organigrama</h3>
                <p class="panel-subtitle">Representación jerárquica de las unidades organizacionales (padre-hijo).</p>
            </div>
        </div>
        <div class="panel-body">
            <div class="org-visual-wrap" id="org_chart_container">
                <p class="org-empty">Cargando organigrama...</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="new_unit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="organizational_units.php?action=create" method="POST" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva unidad organizacional</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="organizational_unit_type_id">Tipo</label>
                        <select class="form-control" id="organizational_unit_type_id" name="organizational_unit_type_id" required>
                            <option value="">Seleccione un tipo</option>
<?php foreach ($unitTypes as $type) { ?>
                            <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['name']) ?></option>
<?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parent_id">Padre</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">RAIZ</option>
<?php foreach ($parentCandidates as $parent) { ?>
                            <option value="<?= htmlspecialchars($parent['id']) ?>"><?= htmlspecialchars($parent['name']) ?></option>
<?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_unit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="organizational_units.php?action=update" method="POST" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title">Editar unidad organizacional</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label for="name_edit">Nombre</label>
                        <input type="text" class="form-control" id="name_edit" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="organizational_unit_type_id_edit">Tipo</label>
                        <select class="form-control" id="organizational_unit_type_id_edit" name="organizational_unit_type_id" required>
                            <option value="">Seleccione un tipo</option>
<?php foreach ($unitTypes as $type) { ?>
                            <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['name']) ?></option>
<?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parent_id_edit">Padre</label>
                        <select class="form-control" id="parent_id_edit" name="parent_id">
                            <option value="">RAIZ</option>
<?php foreach ($parentCandidates as $parent) { ?>
                            <option value="<?= htmlspecialchars($parent['id']) ?>"><?= htmlspecialchars($parent['name']) ?></option>
<?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_active_edit">Estado</label>
                        <select class="form-control" id="is_active_edit" name="is_active" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var orgUnits = <?= json_encode($units, JSON_UNESCAPED_UNICODE) ?>;
        var dataTableEs = {
            decimal: "",
            emptyTable: "No hay unidades organizacionales registradas",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            lengthMenu: "Mostrar _MENU_ registros",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron coincidencias",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        };

        $('#table').DataTable({
            language: dataTableEs
        });

        $('#return').click(function() {
            window.location.href = 'snacks.php';
        });

        drawOrgChart(orgUnits);

        var message = "<?= $_SESSION['message'] ?? '' ?>";
        var messageType = "<?= $_SESSION['message_type'] ?? '' ?>";

        if (message && messageType && typeof toastr === 'object' && typeof toastr[messageType] === 'function') {
            toastr[messageType](message);
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['message_type']); ?>
        }
    });

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function drawOrgChart(units) {
        var container = document.getElementById('org_chart_container');
        if (!container) {
            return;
        }

        if (!Array.isArray(units) || units.length === 0) {
            container.innerHTML = '<p class="org-empty">No hay unidades organizacionales registradas.</p>';
            return;
        }

        var byId = {};
        var roots = [];

        units.forEach(function(unit) {
            byId[String(unit.id)] = {
                raw: unit,
                children: []
            };
        });

        units.forEach(function(unit) {
            var id = String(unit.id);
            var parentId = unit.parent_id === null || unit.parent_id === '' ? null : String(unit.parent_id);

            if (parentId && byId[parentId]) {
                byId[parentId].children.push(byId[id]);
            } else {
                roots.push(byId[id]);
            }
        });

        function sortNodes(nodes) {
            nodes.sort(function(a, b) {
                var nameA = String(a.raw.name || '');
                var nameB = String(b.raw.name || '');
                return nameA.localeCompare(nameB, 'es', { sensitivity: 'base' });
            });

            nodes.forEach(function(node) {
                if (node.children.length > 0) {
                    sortNodes(node.children);
                }
            });
        }

        sortNodes(roots);

        function renderList(nodes) {
            if (!nodes || nodes.length === 0) {
                return '';
            }

            var html = '<ul class="org-root-list">';
            nodes.forEach(function(node) {
                var isActive = Number(node.raw.is_active) === 1;
                var typeText = escapeHtml(node.raw.unit_type || 'SIN TIPO');
                html += '<li>';
                html += '<div class="org-node ' + (isActive ? '' : 'inactive') + '">';
                html += '<span class="org-name">' + escapeHtml(node.raw.name || '') + '</span>';
                html += '<span class="org-type">' + typeText + '</span>';
                html += '<span class="org-status ' + (isActive ? 'active' : 'inactive') + '">' + (isActive ? 'Activo' : 'Inactivo') + '</span>';
                html += '</div>';
                html += renderList(node.children);
                html += '</li>';
            });
            html += '</ul>';
            return html;
        }

        container.innerHTML = renderList(roots);
    }

    function show_edit(element) {
        var id = $(element).data('id');
        var name = $(element).data('name');
        var typeId = $(element).data('type-id');
        var parentId = $(element).data('parent-id');
        var isActive = $(element).data('is-active');

        $('#id').val(id);
        $('#name_edit').val(name);
        $('#organizational_unit_type_id_edit').val(typeId);
        $('#parent_id_edit').val(parentId ? parentId : '');
        $('#is_active_edit').val(isActive);
        $('#edit_unit').modal('show');
    }

    function delete_unit(id) {
        if (confirm('¿Está seguro de desactivar esta unidad organizacional?')) {
            window.location.href = 'organizational_units.php?action=delete&id=' + id;
        }
    }
</script>
<?php
$content = ob_get_clean();
include '../templates/base_modules.php';
?>