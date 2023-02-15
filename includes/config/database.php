<?php

/**
 * Conexion a la base de datos
 *
 * @return mysqli Conexion a la base de datos
 */
function conectarDB() : mysqli {
    $db = mysqli_connect('localhost', 'root', 'Jose123#', 'gastos');

    if (!$db) {
        echo "No se pudo conectar.";
        exit; // Evita que el demas codigo de ejecute
    }

    return $db;
}