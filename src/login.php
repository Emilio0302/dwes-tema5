<?php

/**********************************************************************************************************************
 * Este programa, a través del formulario que tienes que hacer debajo, en el área de la vista, realiza el inicio de
 * sesión del usuario verificando que ese usuario con esa contraseña existe en la base de datos.
 * 
 * Para mantener iniciada la sesión dentrás que usar la $_SESSION de PHP.
 * 
 * En el formulario se deben indicar los errores ("Usuario y/o contraseña no válido") cuando corresponda.
 * 
 * Dicho formulario enviará los datos por POST.
 * 
 * Cuando el usuario se haya logeado correctamente y hayas iniciado la sesión, redirige al usuario a la
 * página principal.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
session_start();

if (isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

require 'funciones/baseDatos.php';

if ($_POST) {
    if (isset($_POST['nombre'])) {
        $nombre = htmlspecialchars(trim($_POST['nombre']));

        if ($nombre == "") {
            $nombreVacio = true;
        } else {
            $nombreVacio = false;
        }
    } 

    if (isset($_POST['clave'])) {
        $clave = htmlspecialchars(trim($_POST['clave']));

        if (strlen($clave) > 0) {
            $claveValida = true;
        } else {
            $claveValida = false;
        }
    }

    $todoValido = !$nombreVacio && $claveValida ? true : false;

    if ($todoValido) {
        $login = iniciarSesion($nombre, $clave);
        if ($login) {
            $_SESSION['usuario'] = $nombre;
            echo "<p>Te has logeado correctamente<a href='index.php'>Volver al inicio</a></p> ";
            exit();
        }
    }
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: añadir el menú.
 * - TODO: formulario con nombre de usuario y contraseña.
 */
?>

<h1>Iniciar sesión</h1>

<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="filter.php">Filtrar imágenes</a></li>
    <li><strong>Inicia sesion</strong></li>
    <li><a href="signup.php">Regístrate</a></li>
</ul>

<form action="login.php" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre" 
        value="<?php echo isset($nombre) ? $nombre : ""; ?>">
    </p>
    <?php
     if (isset($nombreVacio) && $nombreVacio) {
        echo "<p>ERROR: Este campo no puede estar vacio</p>";
    }
    ?>
    <p>
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave">
    </p>
    <?php
    if (isset($login) && !$login) {
        echo "<p>ERROR: Usuario y/o contraseña incorrrectas</p>";
    }
    ?>
    <input type="submit" value="Iniciar sesión">
</form>