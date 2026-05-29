<?php
function getCurrentRoleNames() {
    $roleNames = [];

    if (!empty($_SESSION['role_names'])) {
        foreach (explode(',', (string) $_SESSION['role_names']) as $name) {
            $name = strtoupper(trim($name));
            if ($name !== '') {
                $roleNames[] = $name;
            }
        }
    }

    if (!empty($_SESSION['role_name'])) {
        $singleRole = strtoupper(trim((string) $_SESSION['role_name']));
        if ($singleRole !== '') {
            $roleNames[] = $singleRole;
        }
    }

    return array_values(array_unique($roleNames));
}

function userHasAnyRole($requiredRoles) {
    $currentRoles = getCurrentRoleNames();
    if (empty($currentRoles)) {
        return false;
    }

    foreach ($requiredRoles as $role) {
        if (in_array(strtoupper($role), $currentRoles, true)) {
            return true;
        }
    }

    return false;
}

function canAccessModule($moduleKey) {
    if (userHasAnyRole(['ADMINISTRADOR', 'ADMIN_MODULO'])) {
        return true;
    }

    $modulePermissions = [
        'students' => ['GESTOR_BENEFICIARIOS', 'OPERADOR_ENTREGAS'],
        'academic_programs' => ['GESTOR_BENEFICIARIOS', 'PLANIFICADOR'],
        'schedules' => ['PLANIFICADOR', 'AUDITOR'],
        'delivery' => ['OPERADOR_ENTREGAS', 'AUDITOR'],
        'reports' => ['VISOR_REPORTES', 'PLANIFICADOR', 'AUDITOR'],
        'organizational_units' => ['ADMIN_MODULO'],
        'roles' => ['ADMIN_MODULO'],
        'profile_types' => ['ADMIN_MODULO'],
        'users' => ['ADMIN_MODULO']
    ];

    if (!isset($modulePermissions[$moduleKey])) {
        return false;
    }

    return userHasAnyRole($modulePermissions[$moduleKey]);
}

function card($rutaImagen, $nombreImagen, $nombreCarta) {
    $html = '<div class="col-md-4 col-sm-6 col-lg-3 col-xs-12 pt-3" style="text-align: center;">';
    $html .= '<a href="' . $rutaImagen . '" style="color: black; text-decoration: none;">';
    $html .= '<div class="overview-item overview-item--c5" style="background-color: white;">';
    $html .= '<div class="overview__inner">';
    $html .= '<div class="overview-box clearfix pt-2 pb-3">';
    $html .= '<img style="max-width: 60%; height: auto; display: block; margin: 0 auto;" src="'.'../imgs/icons/' . $nombreImagen . '" class="mx-auto d-block">';
    $html .= '<b style="font-size: 1.2em;">' . $nombreCarta . '</b>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</a>';
    $html .= '</div>';

    return $html;
}


