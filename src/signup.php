<?php
/*********************************************************************************************************************
 * Este script realiza el registro del usuario vía el POST del formulario que hay debajo, en la vista.
 * 
 * Cuando llegue POST hay que validarlo y si todo fue bien insertar en la base de datos el usuario.
 * 
 * Requisitos del POST:
 * - El nombre de usuario no tiene que estar vacío y NO PUEDE EXISTIR UN USUARIO CON ESE NOMBRE EN LA BASE DE DATOS.
 * - La contraseña tiene que ser, al menos, de 8 caracteres.
 * - Las contraseñas tiene que coincidir.
 * 
 * La contraseña la tienes que guardar en la base de datos cifrada mediante el algoritmo BCRYPT.
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
        if (ctype_alnum($nombre)) {
            $caracteresValidos = true;
        } else {
            $caracteresValidos = false;
        }
        if (nombreUsuarioRepetido($nombre)) {
            $nombreRepetido = true;
        } else {
            $nombreRepetido = false;
        }
    }
    if (isset($_POST['clave'])) {
        $clave = htmlspecialchars(trim($_POST['clave']));
        if (strlen($clave) >= 8) {
            $claveValida = true;
        } else {
            $claveValida = false;
        }
    }
    if (isset($_POST['repite_clave'])) {
        $claveRepetida = htmlspecialchars(trim($_POST['repite_clave']));
        if ($clave === $claveRepetida) {
            $claveRepetidaValida = true;
        } else {
            $claveRepetidaValida = false;
        }
    }
    $todoValido = !$nombreVacio && !$nombreRepetido && $caracteresValidos && $claveValida && $claveRepetidaValida ? true : false;
    if ($todoValido) {
        $signUp = registrarUsuario($nombre, $clave);
        if ($signUp) {
            echo "<p>Te has registrado correctamente <a href='index.php'>Volver al inicio</a></p>";
        } else {
            echo "<p>No se ha podido registrar correctamente <a href='index.php'>Volver al inicio</a></p>";
        }
        exit();
    }
}


/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: los errores que se produzcan tienen que aparecer debajo de los campos.
 * - TODO: cuando hay errores en el formulario se debe mantener el valor del nombre de usuario en el campo
 *         correspondiente.
 */
?>
<h1>Galería de imágenes</h1>

<h2>Regístrate</h2>
<ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="filter.php">Filtrar imágenes</a></li>
    <li><strong>Regístrate</strong></li>
    <li><a href="login.php">Iniciar sesión</a></li>
</ul>
    <form action="signup.php" method="post">
        <p>
            <label for="nombre">Nombre de usuario</label>
            <input type="text" name="nombre" id="nombre" value="<?= $_POST && isset($nombre) ? $nombre : "" ?>">
        </p>
        <?php
        if (isset($nombreVacio) && $nombreVacio) {
            echo "<p>ERROR: Este campo no puede estar vacio</p>";
        }
        if (isset($caracteresValidos) && !$caracteresValidos) {
            echo "<p>ERROR: El usuario debe contener caracteres alfanumericos</p>";
        }
        if (isset($nombreRepetido) && $nombreRepetido) {
            echo "<p>ERROR: Nombre de usuario no disponible</p>";
        }
        ?>
        <p>
            <label for="clave">Contraseña</label>
            <input type="password" name="clave" id="clave">
        </p>
        <?php
        if (isset($claveValida) && !$claveValida) {
            echo "<p>ERROR: La contraseña debe contener minimo 8 caracteres</p>";
        }
        ?>
        <p>
            <label for="repite_clave">Repite la contraseña</label>
            <input type="password" name="repite_clave" id="repite_clave">
        </p>
        <?php
        if (isset($claveRepetidaValida) && !$claveRepetidaValida) {
            echo "<p>ERROR: No coinciden las contraseñas</p>";
        }
        ?>
        <p>
            <input type="submit" value="Enviar">
        </p>
    </form>