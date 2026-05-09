<?php
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


