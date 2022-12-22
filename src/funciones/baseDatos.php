<?php
//Esta funcion se encarga de comprobar si hay un nombre repetido en la base de datos
function nombreUsuarioRepetido($nombre): bool
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $resultado = $mysqli->query("select nombre from usuario");
    if ($resultado === false) {
        echo "<p>Se ha producido un error al consultar la base de datos.</p>";
        echo "<p>Este es el error: $mysqli->error.</p>";
        return true;
    } else {
        while (($fila = $resultado->fetch_assoc()) !== null) {
            if ($fila['nombre'] == $nombre) {
                return true;
            }
        }
        $resultado->free();
        return false;
    }

    $mysqli->close();

    return true;
}
//Esta funcion se encarga de registrar a un usuario en la base de datos
function registrarUsuario($nombre, $clave): bool|null
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("insert into usuario (nombre, clave) values (?, ?)");
    if (!$sentencia) {
        $mysqli->close();
        return null;
    }

    // 2. Vinculación (bind)
    $clave = password_hash($clave, PASSWORD_BCRYPT);
    $vinculacion = $sentencia->bind_param("ss", $nombre, $clave);
    if (!$vinculacion) {
        $sentencia->close();
        $mysqli->close();
        return null;
    }
    // 3. Ejecución
    $resultado = $sentencia->execute();

    $sentencia->close();
    $mysqli->close();

    return $resultado;
}
//Esta funcion comprueba si el nombre y la clave coinciden con un usuario en la base de datos
function iniciarSesion($nombre, $clave): bool|null
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $resultado = $mysqli->query("select nombre, clave from usuario where nombre=\"$nombre\"");
    if ($resultado === false) {
        return null;
    } else if (($fila = $resultado->fetch_assoc()) === null) {
        $resultado->free();
        $mysqli->close();
        return false;
    }

    $usuario = password_verify($clave, $fila['clave']);

    $resultado->free();
    $mysqli->close();
    return $usuario;
}
// Esta funcion se encarga de añadir imagenes en la base de datos
function anyadirImagenBaseDatos($nombre, $ruta, $usuario): bool|null
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $resultado = $mysqli->query("select id from usuario where nombre=\"$usuario\"");
    if ($resultado === false) {
        return null;
    } else if (($fila = $resultado->fetch_assoc()) !== null) {
        $usuario = $fila['id'];
    } else {
        return null;
    }

    $sentencia = $mysqli->prepare("insert into imagen (nombre, ruta, usuario) values (?, ?, ?)");
    if (!$sentencia) {
        $mysqli->close();
        return null;
    }

    // 2. Vinculación (bind)
    $vinculacion = $sentencia->bind_param("ssi", $nombre, $ruta, $usuario);
    if (!$vinculacion) {
        $sentencia->close();
        $mysqli->close();
        return null;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();

    $sentencia->close();
    $mysqli->close();

    return $resultado;
}
//Esta funcion se encarga de comprobar si hay una imagen en la base de datos con ese id
function existeImagen(int $id): bool|null
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $resultado = $mysqli->query("select * from imagen where id=$id");
    if ($resultado === false) {
        return null;
    } else if ($resultado->num_rows == 1) {
        $resultado->free();
        $mysqli->close();
        return true;
    }
}
//Esta funcion devuelve la ruta de una imagen
function getRuta(int $id): string|null
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $resultado = $mysqli->query("select ruta from imagen where id=$id");
    if ($resultado === false) {
        return null;
    }

    if (($fila = $resultado->fetch_assoc()) !== null) {
        $resultado->free();
        $mysqli->close();
        return $fila['ruta'];
    }
}
// Esta funcion se encarga del filtrado de imagenes por un nombre y devuelve un array multidimensional con la informacion de estas
function getImagenes(string $nombre): array|null
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    $resultado = $mysqli->query("select i.ruta, i.nombre, u.nombre as usuario
        from imagen i join usuario u
        on i.usuario = u.id
        where i.nombre like '%$nombre%'");
    if ($resultado === false) {
        return [];
    } else {
        $imagenes = [];
        while (($fila = $resultado->fetch_assoc()) !== null) {
            $tempo = [
                'ruta' => $fila['ruta'],
                'nombre' => $fila['nombre'],
                'usuario' => $fila['usuario']
            ];
            $imagenes[] = $tempo;
        }
        $resultado->free();
        $mysqli->close();
        return $imagenes;
    }
}
// Esta se encarga de borrar imagenes de la base de datos
function borrarImagen(int $id): bool|null
{
    //Conexion a la base de datos
    $mysqli = new mysqli("db", "dwes", "dwes", "dwes", 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("delete from imagen where id=?");
    if (!$sentencia) {
        return null;
    }

    // 2. Vinculación (bind)
    $vinculacion = $sentencia->bind_param("i", $id);
    if (!$vinculacion) {
        return null;
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();

    $sentencia->close();
    $mysqli->close();

    return $resultado;
}
