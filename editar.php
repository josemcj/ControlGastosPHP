<?php

require 'includes/config/database.php';
$db = conectarDB();
include 'includes/funciones.php';

$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

// Redireccionar si $id no es INT
if (!$id) {
    header('Location: /');
}

/**
 * CONSULTAR ID PASADO POR $_GET
 */
$query = "SELECT * FROM gastos WHERE id=${id}";
$resultado = mysqli_query($db, $query);

$registro = mysqli_fetch_assoc($resultado);

// Consulta tipo de gasto
$query = "SELECT * FROM tipo";
$resultadoTipo = mysqli_query($db, $query);

$fechaAnterior = $registro['fecha'];
$conceptoAnterior = $registro['concepto'];
$cantidadAnterior = $registro['cantidad'];
$tipoRegistro = intval( $registro['tipo'] );

// Arreglo de errores
$errores = [];

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $fecha = mysqli_real_escape_string($db, $_POST['fecha']);
    $concepto = mysqli_real_escape_string($db, $_POST['concepto']);
    $cantidad = floatval( mysqli_real_escape_string($db, str_replace(',', '', $_POST['cantidad']) ) );
    $tipo = intval( mysqli_real_escape_string($db, $_POST['tipo']) );

    // Obtienen la cantidad de dinero disponible
    $query = "SELECT * FROM disponible";
    $resultado = mysqli_fetch_assoc( mysqli_query($db, $query) );

    if ( $tipoRegistro === 2 ) {
        $cantidadDisponible = floatval($resultado['cantidad']) + $cantidadAnterior;
    } elseif ( $tipoRegistro === 1 ) {
        $cantidadDisponible = floatval($resultado['cantidad']) - $cantidadAnterior;
    }

    /**
     * VALIDACION DE CAMPOS
     */
    if ( !$fecha ) {
        $errores[] = 'Debes agregar una fecha';
    }

    if ( !$concepto ) {
        $errores[] = 'Debes agregar el concepto del gasto';
    }

    if ( !$cantidad || $cantidad <= 0 ) {
        $errores[] = 'La cantidad no puede estar vacía ni ser igual o menor a $0';
    }

    if ( !$tipo ) {
        $errores[] = 'Elige un tipo de registo: ingreso o egreso';
    }

    // Si el arreglo errores esta vacio, realizar actualizacion
    if ( empty($errores) ) {
        /**
         * Valores para $tipo:
         * 1: Ingreso
         * 2: Egreso
         */
        if ( $tipo === 2 ) {
            // Valida que la cantidad no sea mayor a la cantidad disponible
            if ($cantidad < $cantidadDisponible) {
                $query = "UPDATE gastos SET fecha = '${fecha}', concepto = '${concepto}', cantidad = ${cantidad}, tipo = ${tipo} WHERE id = ${id}";

                $resultado = mysqli_query($db, $query);

                if ( $resultado ) {
                    // Actualizar DB en tabla disponible
                    $actualizarDisponible = $cantidadDisponible - $cantidad;
                    $query = "UPDATE disponible SET cantidad = ${actualizarDisponible} WHERE id=1";

                    $resultado = mysqli_query($db, $query);

                    if ($resultado) {
                        header( 'Location: /?gastoActualizado=ok' );
                    } else {
                        // Si no se pudo actualizar disponible actualiza el registro con la info anterior
                        $query = "UPDATE gastos SET fecha = '${fechaAnterior}', concepto = '${conceptoAnterior}', cantidad = ${cantidadAnterior}, tipo = ${tipoRegistro} WHERE id = ${id}";

                        $resultado = mysqli_query($db, $query);

                        header( 'Location: /?gastoActualizado=error' );
                    }
                } else {
                    header( 'Location: /?gastoActualizado=error' );
                }
            } else {
                $errores[] = 'No puedes agregar un gasto mayor a la cantidad disponible';
            }
        } else {
            $query = "UPDATE gastos SET fecha = '${fecha}', concepto = '${concepto}', cantidad = ${cantidad}, tipo = ${tipo} WHERE id = ${id}";

            $resultado = mysqli_query($db, $query);

            if ( $resultado ) {
                // Actualizar DB en tabla disponible
                $actualizarDisponible = $cantidadDisponible + $cantidad;
                $query = "UPDATE disponible SET cantidad = ${actualizarDisponible} WHERE id=1";

                $resultado = mysqli_query($db, $query);

                if ($resultado) {
                    header( 'Location: /?gastoActualizado=ok' );
                } else {
                    // Si no se pudo actualizar disponible actualiza el registro con la info anterior
                    $query = "UPDATE gastos SET fecha = '${fechaAnterior}', concepto = '${conceptoAnterior}', cantidad = ${cantidadAnterior}, tipo = ${tipoRegistro} WHERE id = ${id}";
                    
                    $resultado = mysqli_query($db, $query);

                    header( 'Location: /?gastoActualizado=error' );
                }
            } else {
                header( 'Location: /?gastoActualizado=error' );
            }
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
    <title>Editar registro</title>
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
            <p class="dinero">Editar registro</p>
        </div>
    </header>

    <!-- Errores -->
    <?php foreach ($errores as $error): ?>
        <div class="contenedor mensajesGET">
            <p class="alerta error">
                <?php echo $error; ?>
            </p>
        </div>
    <?php endforeach; ?>

    <main class="contenedor centrar-flex">
        <form class="form_registro" method="post">
            <div class="form__col">
                <div class="form__label">
                    <label for="fecha">Fecha</label>
                </div>
                <div class="form__input">
                    <input type="date" name="fecha" id="fecha" value="<?php echo $fechaAnterior; ?>" required>
                </div>
                
            </div>

            <div class="form__col">
                <div class="form__label">
                   <label for="concepto">Concepto</label> 
                </div>
                <div class="form__input">
                    <input type="text" name="concepto" id="concepto" value="<?php echo $conceptoAnterior; ?>" placeholder="Spotify" required>
                </div>
            </div>

            <div class="form__col">
                <div class="form__label">
                    <label for="cantidad">Cantidad</label>
                </div>
                <div class="form__input form__cantidad">
                    <p class="signo_cantidad">$</p>
                    <input type="text" name="cantidad" id="cantidad" value="<?php echo $cantidadAnterior; ?>" required>
                </div>
            </div>

            <div class="form__col">
                <div class="form__label">
                    <label for="tipo">Tipo</label>
                </div>
                <div class="form__input">
                    <select name="tipo" id="tipo" required>
                        <option value="" selected disabled>Seleccionar...</option>
                        <?php while ($row = mysqli_fetch_assoc($resultadoTipo)): ?>
                            <option
                                <?php echo $tipoRegistro == $row['id'] ? 'selected' : ''; ?> 
                                value="<?php echo $row['id']; ?>"
                            >
                            <?php echo $row['tipo']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form_col">
                <input class="btn" type="submit" value="Actualizar">
            </div>
        </form>
    </main>

<?php incluirTemplate('footer'); ?>