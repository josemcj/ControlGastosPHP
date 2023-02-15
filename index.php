<?php

include 'includes/funciones.php';

// Recibe el resultado de la creacion de gastos
$resultadoBD = $_GET['gastoCreado'] ?? null;
$resultadoEliminar = $_GET['eliminar'] ?? null;
$resultadoActualizar = $_GET['gastoActualizado'] ?? null;
$resultadoModCantidad = $_GET['modCantidad'] ?? null;

// Base de datos
require './includes/config/database.php';
$db = conectarDB();

/**
 * MOSTRAR REGISTROS
 */
$query = "SELECT * FROM gastos ORDER BY id DESC";
$resultado = mysqli_query($db, $query);

// Dinero disponible
$queryDisponible = "SELECT * FROM disponible";
$dineroDisponible = mysqli_fetch_assoc( mysqli_query($db, $queryDisponible) );

/**
 * ELIMINAR REGISTROS
 */
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);
    $tipoEliminar = $_POST['tipo'];
    $tipoEliminar = filter_var($tipoEliminar, FILTER_VALIDATE_INT);
    $cantidadEliminar = $_POST['cantidad'];
    $cantidadEliminar = filter_var($cantidadEliminar, FILTER_VALIDATE_FLOAT);

    if ($id && $tipoEliminar && $cantidadEliminar) {
        // Actualizar tabla de dinero disponible antes de eliminar
        if ($tipoEliminar === 1) {
            $dineroDisponibleAD = $dineroDisponible['cantidad'] - $cantidadEliminar;
        } elseif ($tipoEliminar === 2) {
            $dineroDisponibleAD = $dineroDisponible['cantidad'] + $cantidadEliminar;
        }

        $query = "UPDATE disponible SET cantidad = ${dineroDisponibleAD} WHERE id=1";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            $query = "DELETE FROM gastos WHERE id=${id}";
            $resultado = mysqli_query($db, $query);

            if ($resultado) {
                header('Location: /?eliminar=ok');
            } else {
                // Si no se puede eliminar el registro
                $dineroDisponibleADError = floatval($dineroDisponible['cantidad']);
                $query = "UPDATE disponible SET cantidad = ${dineroDisponibleADError} WHERE id=1";
                $resultado = mysqli_query($db, $query);

                if ($resultado) {
                    header('Location: /?eliminar=error');
                }
            }
        } else {
            header('Location: /?eliminar=error');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es-MX">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de gastos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="preload" href="/assets/css/app.css" as="style">
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://kit.fontawesome.com/a25d0be30f.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <div class="contenedor">
            <p class="disponible">Disponible</p>
            <p class="dinero">$<?php echo number_format($dineroDisponible['cantidad'], 2); ?></p>
            
            <div class="conjunto-botones">
                <a href="/cantidad.php" class="btn-subrayado">Modificar cantidad<i class="fa-solid fa-pen-to-square"></i></a>
                <a href="/crear.php" class="btn">Agregar registro</a>
            </div>
        </div>
    </header>

    <!-- Mensajes -->
    <div class="contenedor mensajesGET">
        <!-- Registro creado -->
        <?php if ($resultadoBD == 'ok'): ?>
            <p class="alerta exito">Registro creado correctamente</p>
        <?php elseif ($resultadoBD == 'error'): ?>
            <p class="alerta error">No se pudo crear el registro</p>
        <?php endif; ?>

        <!-- Registro eliminado -->
        <?php if ($resultadoEliminar == 'ok'): ?>
            <p class="alerta exito">Registro eliminado correctamente</p>
        <?php elseif ($resultadoEliminar == 'error'): ?>
            <p class="alerta error">No se pudo eliminar el registro</p>
        <?php endif; ?>

        <!-- Registro actualizado -->
        <?php if ($resultadoActualizar == 'ok'): ?>
            <p class="alerta exito">Registro actualizado correctamente</p>
        <?php elseif ($resultadoActualizar == 'error'): ?>
            <p class="alerta error">No se pudo actualizar el registro</p>
        <?php endif; ?>

        <!-- Cantidad modificada -->
        <?php if ($resultadoModCantidad == 'ok'): ?>
            <p class="alerta exito">Cantidad disponible actualizada correctamente</p>
        <?php elseif ($resultadoModCantidad == 'error'): ?>
            <p class="alerta error">No se pudo actualizar la cantidad disponible</p>
        <?php endif; ?>
    </div>

    <div class="contenedor">
        <table>
            <thead>
                <tr>
                    <th class="t-center">Fecha</th>
                    <th class="tc-50">Concepto</th>
                    <th class="t-center">Cantidad</th>
                    <th class="t-center">Acción</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($resultado->num_rows >= 1): ?>
                <?php while ( $registro = mysqli_fetch_assoc($resultado) ): ?>
                <tr>
                    <td class="t-center"><?php 
                        // Formato a la fecha
                        $fecha = DateTime::createFromFormat('Y-m-d', $registro['fecha']);
                        echo $fecha->format('j M Y');
                    ?></td>
                    <td><?php echo $registro['concepto']; ?></td>
                    <?php if ($registro['tipo'] == 1): ?>
                        <td class="t-center ingreso">+$<?php echo number_format($registro['cantidad'], 2); ?></td>
                    <?php elseif ($registro['tipo'] == 2): ?>
                        <td class="t-center egreso">-$<?php echo number_format($registro['cantidad'], 2); ?></td>
                    <?php endif; ?>
                    <td class="t-center">
                        <!-- Editar -->
                        <a href="/editar.php?id=<?php echo $registro['id']; ?>">
                            <i class="icono icono-editar fa-solid fa-pen-to-square"></i>
                        </a>
                        <!-- Eliminar -->
                        <form method="post" id="eliminarRegistro">
                            <input type="hidden" name="id" value="<?php echo $registro['id']; ?>">
                            <input type="hidden" name="tipo" value="<?php echo $registro['tipo']; ?>">
                            <input type="hidden" name="cantidad" value="<?php echo $registro['cantidad']; ?>">
                            <button class="btn-eliminar" type="submit">
                                <i class="icono icono-borrar fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-registros">No hay registros.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php incluirTemplate('footer'); ?>