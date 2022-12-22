<?php
/**********************************************************************************************************************
 * Este script tan solo tiene que destruir la sesión y volver a la página principal.
 * 
 * UN USUARIO NO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */
session_start();

if (!isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

echo "<p>Ha cerrado sesion correctamente <a href='index.php'>Volver al inicio</a></p>";
session_destroy();