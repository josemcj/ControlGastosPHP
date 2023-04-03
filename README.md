# App de control de gastos con PHP y MySQL

Permite registrar una cantidad específica de dinero y apartir de ahi crear, editar y eliminar gastos.

## Configuración inicial del proyecto

1. Importa la base de datos desde el archivo **control_gastos.sql**.
2. Accede a la carpeta **includes/config** y edita el archivo **database.php** con tus respectivos datos de conexión a la base de datos (host, usuario, contraseña y base de datos).
3. Situate en la carpeta raíz del proyecto y abre la terminal. Ejecuta el comando `npm i`.
4. Ejecuta el servidor web con XAMPP o el servidor de desarrollo de PHP. Para este último, abre la terminal y ejecuta el comando `php -S localhost:<PUERTO>`.

## ¿Cómo funciona el proyecto?

La aplicación permite crear gastos e ingresos y sumarlos o restarlos (según sea el caso) a una cantidad definida por el usuario.
Veamos su funcionamiento.

En la siguiente imagen podemos ver la página de inicio de la aplicación. Tenemos dos botones, uno para establecer una cantidad y otro para agregar un ingreso o egreso.
Debajo veremos la lista de ingresos y egresos.
![Pantalla inicial de la aplicación web](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/01.png)