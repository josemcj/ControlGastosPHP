<?php

// Base de datos
require './includes/config/database.php';
$db = conectarDB();
include 'includes/funciones.php';

// Dinero disponible
$queryDisponible = "SELECT * FROM disponible";
$dineroDisponible = mysqli_fetch_assoc( mysqli_query($db, $queryDisponible) );

// Arreglo de errores
$errores = [];

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $cantidad = floatval( mysqli_real_escape_string($db, str_replace(',', '', $_POST['cantidad']) ) );

    if ($cantidad >= 0) {
        $query = "UPDATE disponible SET cantidad = ${cantidad} WHERE id=1";
        $resultado = mysqli_query($db, $query);

        if ($resultado) {
            header( 'Location: /?modCantidad=ok' );
        } else {
            header( 'Location: /?modCantidad=error' );
        }
    } else {
        $errores[] = 'La cantidad no puede ser menor a $0';
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
            <p class="dinero">Cantidad disponible</p>
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
        <p class="p-cantidad-disponible">Actualmente tienes <span>$<?php echo number_format($dineroDisponible['cantidad'], 2); ?></span>. Ingresa la nueva cantidad disponible.</p>
        <form action="cantidad.php" class="form_registro form-cantidad" method="post">
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
                <input class="btn" type="submit" value="Guardar">
            </div>
        </form>
    </main>

<?php incluirTemplate('footer'); ?>