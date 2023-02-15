<?php

require 'includes/config/database.php';
$db = conectarDB();
include 'includes/funciones.php';

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
    $cantidadDisponible = floatval($resultado['cantidad']);

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

    if ( empty($errores) ) {
        /**
         * Valores para $tipo:
         * 1: Ingreso
         * 2: Egreso
         */
        if ( $tipo === 2 ) {
            // Valida que la cantidad no sea mayor a la cantidad disponible
            if ($cantidad < $cantidadDisponible) {
                $query = "INSERT INTO gastos (fecha, concepto, cantidad, tipo) VALUES (
                    '$fecha', '$concepto', $cantidad, $tipo
                )";

                $resultado = mysqli_query($db, $query);

                if ( $resultado ) {
                    // Actualizar DB en tabla disponible
                    $actualizarDisponible = $cantidadDisponible - $cantidad;
                    $query = "UPDATE disponible SET cantidad = ${actualizarDisponible} WHERE id=1";

                    $resultado = mysqli_query($db, $query);

                    if ($resultado) {
                        header( 'Location: /?gastoCreado=ok' );
                    } else {
                        // Si no se pudo actualizar disponible elimina el registro (ultimo)
                        $query = "SELECT MAX(id) AS ultimoId FROM gastos";
                        $resultado = mysqli_fetch_assoc( mysqli_query($db, $query) );
                        $ultimoID = intval($resultado['ultimoId']);

                        if ($resultado) {
                            $query = "DELETE FROM gastos WHERE id=${ultimoID}";
                            $resultado = mysqli_query($db, $query);

                            header( 'Location: /?gastoCreado=error' );
                        }
                    }
                } else {
                    header( 'Location: /?gastoCreado=error' );
                }
            } else {
                $errores[] = 'No puedes agregar un gasto mayor a la cantidad disponible';
            }
        } else {
            $query = "INSERT INTO gastos (fecha, concepto, cantidad, tipo) VALUES (
                '$fecha', '$concepto', $cantidad, $tipo
            )";

            $resultado = mysqli_query($db, $query);

            if ( $resultado ) {
                // Actualizar DB en tabla disponible
                $actualizarDisponible = $cantidadDisponible + $cantidad;
                $query = "UPDATE disponible SET cantidad = ${actualizarDisponible} WHERE id=1";

                $resultado = mysqli_query($db, $query);

                if ($resultado) {
                    header( 'Location: /?gastoCreado=ok' );
                } else {
                    // Si no se pudo actualizar disponible elimina el registro (ultimo)
                    $query = "SELECT MAX(id) AS ultimoId FROM gastos";
                    $resultado = mysqli_fetch_assoc( mysqli_query($db, $query) );
                    $ultimoID = intval($resultado['ultimoId']);

                    if ($resultado) {
                        $query = "DELETE FROM gastos WHERE id=${ultimoID}";
                        $resultado = mysqli_query($db, $query);
                        
                        header( 'Location: /?gastoCreado=error' );
                    }
                }
            } else {
                header( 'Location: /?gastoCreado=error' );
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
    <title>Agregar registro</title>
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
            <p class="dinero">Crear registro</p>
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
        <form action="crear.php" class="form_registro" method="post">
            <div class="form__col">
                <div class="form__label">
                    <label for="fecha">Fecha</label>
                </div>
                <div class="form__input">
                    <input type="date" name="fecha" id="fecha" required>
                </div>
                
            </div>

            <div class="form__col">
                <div class="form__label">
                   <label for="concepto">Concepto</label> 
                </div>
                <div class="form__input">
                    <input type="text" name="concepto" id="concepto" placeholder="Spotify" required>
                </div>
            </div>

            <div class="form__col">
                <div class="form__label">
                    <label for="cantidad">Cantidad</label>
                </div>
                <div class="form__input form__cantidad">
                    <p class="signo_cantidad">$</p>
                    <input type="text" name="cantidad" id="cantidad" required>
                </div>
            </div>

            <div class="form__col">
                <div class="form__label">
                    <label for="tipo">Tipo</label>
                </div>
                <div class="form__input">
                    <select name="tipo" id="tipo" required>
                        <option value="" selected disabled>Seleccionar...</option>
                        <option value="1">Ingreso</option>
                        <option value="2">Egreso</option>
                    </select>
                </div>
            </div>

            <div class="form__col">
                <input class="btn" type="submit" value="Guardar">
            </div>
        </form>
    </main>

<?php incluirTemplate('footer'); ?>