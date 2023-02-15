<?php

require 'app.php';

/**
 * Muestra en pantalla un dato formateado
 *
 * @param any $dato Variable a debuggear
 * @return void
 */
function debug($dato) {
    echo "<pre>";
    var_dump($dato);
    echo "</pre>";
    exit;
}

/**
 * Incluye algun template buscandolo en la carpeta correspondiente
 *
 * @param string $nombre Nombre del template
 * @return void
 */
function incluirTemplate(string $nombre) {
    include TEMPLATES_URL . "/${nombre}.php";
}