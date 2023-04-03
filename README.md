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

Actualicemos la cantidad disponible en nuestro presupuesto.
Para eso damos clic en el botón "Modificar cantidad". Debemos colocar la cantidad disponible y a continuación dar clic en "Guardar".

![Modificar presupuesto](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/02.png)

Ahora podemos crear un nuevo registro. Para ello, damos clic en el botón "Agregar registro" en la página de inicio, que nos llevará a la siguiente página. Colocamos los respectivos datos y damos clic en "Guardar".

![Agregando egreso](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/03.png)

Podremos ver una confirmación, como se muestra en la siguiente imagen.

![Confirmación de registro](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/04.png)

Asimismo, podemos editar nuestros registros. En la siguiente imagen modificamos la cantidad, de $755 a $180.

![Modificando registro](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/05.png)

Si agregamos más regitros los veremos listados en la página inicial.

![Listado de registros](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/06.png)

Para eliminar un registro, damos clic en el ícono del bote de basura, lo cual generará una confirmación.

![Confirmación para eliminar registros](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/07.png)

A continuación, podemos ver la confirmación de que el registro ha sido eliminado. También podemos observar que la cantidad disponible se ha actualizado.

![Registro eliminado](https://raw.githubusercontent.com/josemcj/ControlGastosPHP/main/assets/screenshots/08.png)