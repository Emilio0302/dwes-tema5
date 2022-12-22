<?php

/**********************************************************************************************************************
 * Este es el script que añade imágenes en la base de datos. En la tabla "imagen" de la base de datos hay que guardar
 * el nombre que viene vía POST, la ruta de la imagen como se indica más abajo, la fecha de la inserción (función
 * UNIX_TIMESTAMP()) y el identificador del usuario que inserta la imagen (el usuario que está logeado en estos
 * momentos).
 * 
 * ¿Cuál es la ruta de la imagen? ¿De dónde sacamos esta ruta? Te lo explico a continuación:
 * - Busca una forma de asignar un nombre que sea único.
 * - La extensión será la de la imagen original, que viene en $_FILES['imagne']['name'].
 * - Las imágenes se subirán a la carpeta llamada "imagenes/" que ves en el proyecto.
 * - En la base de datos guardaremos la ruta relativa en el campo "ruta" de la tabla "imagen".
 * 
 * Así, si llega por POST una imagen PNG y le asignamosel nombre "imagen1", entonces en el campo "ruta" de la tabla
 * "imagen" de la base de datos se guardará el valor "imagenes/imagen1.png".
 * 
 * Como siempre:
 * 
 * - Si no hay POST, entonces tan solo se muestra el formulario.
 * - Si hay POST con errores se muestra el formulario con los errores y manteniendo el nombre en el campo nombre.
 * - Si hay POST y todo es correcto entonces se guarda la imagen en la base de datos para el usuario logeado.
 * 
 * Esta son las validaciones que hay que hacer sobre los datos POST y FILES que llega por el formulario:
 * - En el nombre debe tener algo (mb_strlen > 0).
 * - La imagen tiene que ser o PNG o JPEG (JPG). Usa FileInfo para verificarlo.
 * 
 * NO VAMOS A CONTROLAR SI YA EXISTE UNA IMAGEN CON ESE NOMBRE. SI EXISTE, SE SOBREESCRIBIRÁ Y YA ESTÁ.
 * 
 * A ESTE SCRIPT SOLO SE PUEDE ACCEDER SI HAY UN USARIO LOGEADO.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que desarrollar toda la lógica de este script.
 */
session_start();

if (!isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

require 'funciones/baseDatos.php';

$usuario = htmlspecialchars($_SESSION['usuario']);

if ($_POST) {
    if (isset($_POST['nombre'])) {
        $nombre = htmlspecialchars(trim($_POST['nombre']));

        if (strlen($nombre) > 0) {
            $nombreVacio = false;
        } else {
            $nombreVacio = true;
        }
    } 

    if ( $_FILES && isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] === UPLOAD_ERR_OK &&
        $_FILES['imagen']['size'] > 0) {

        $permitido = ['image/png', 'image/jpeg'];
        $extensionValida = in_array(finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES["imagen"]["tmp_name"]), $permitido);
        $imagen = $extensionValida;
    } else {
        $imagenVacia = true;
        $imagen = false;
    }

    $todoValido = !$nombreVacio && $imagen ? true : false;
    
    if ($todoValido) {
        $nombreImagen = $nombre . '.' . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $ruta = "imagenes/$nombreImagen";
        $moverFichero = move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
        $subir = anyadirImagenBaseDatos($nombre, $ruta, $usuario);

        if ($moverFichero && $subir) {
            echo "<p>La imagen se ha subido correctamente <a href='index.php'>Volver al inicio</a></p>";
            exit();
        }
    }
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: añadir el menú de navegación.
 * - TODO: añadir en el campo del nombre el valor del mismo cuando haya errores en el envío para mantener el nombre
 *         que el usuario introdujo.
 * - TODO: añadir los errores que se produzcan cuando se envíe el formulario debajo de los campos.
 */
?>
<h1>Galería de imágenes</h1>

<ul>
    <li><a href="index.php">Home</a></li>
    <li><strong>Añadir imagen</strong></li>
    <li><a href="filter.php">Filtrar imágenes</a></li>
    <li><a href="logout.php">Cerrar sesión (<?= $usuario ?>)</a></li>
</ul>

<form method="post" enctype="multipart/form-data">
    <p>
        <label for="nombre">Nombre de la imagen</label>
        <input type="text" name="nombre" id="nombre" 
        value="<?= $_POST && isset($nombre) ? $nombre : "" ?>">
    </p>
    <?php
    if (isset($nombreVacio) && $nombreVacio) {
        echo "<p>ERROR: Este campo no puede estar vacío</p>";
    }
    ?>
    <p>
        <label for="imagen">Imagen</label>
        <input type="file" name="imagen" id="imagen">
    </p>
    <?php
    if (isset($imagenVacia) && $imagenVacia) {
        echo "<p>ERROR: Este campo no puede estar vacío</p>";
    }
    if (isset($extensionValida) && !$extensionValida) {
        echo "<p>ERROR: Extensión incorrecta</p>";
    }
    ?>
    <p>
        <input type="submit" value="Añadir">
    </p>
</form>