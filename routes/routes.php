
<?php

// Incluimos los Controllers
require_once __DIR__ . "/../controllers/UsuarioController.php";
require_once __DIR__ . "/../controllers/LoginController.php";

// Obtenemos el método HTTP
$metodo = $_SERVER["REQUEST_METHOD"];

// Obtenemos la URL
$uri = trim($_SERVER["REQUEST_URI"], "/");

// Dividimos la URL en partes
$partes = explode("/", $uri);

// Obtenemos el recurso
$recurso = $partes[count($partes) - 2] ?? null;

// Obtenemos el último elemento de la URL
$idOParametro = $partes[count($partes) - 1] ?? null;

// Identificamos la ruta completa para rutas con dos partes
$rutaCompleta = implode("/", array_slice($partes, -2));

// Si el último elemento no es numérico,
// significa que es el nombre del recurso
if (!is_numeric($idOParametro)) {
    $recurso = $idOParametro;
    $idOParametro = null;
}

// =========================================
// SUBIR FOTO DE PERFIL
// =========================================

if ($rutaCompleta === "perfil/foto" && $metodo === "POST") {

    $controller = new UsuarioController();
    $controller->subirFoto();

}

// =========================================
// RUTAS DE PROYECTOS
// =========================================

elseif ($recurso === "proyectos") {

    $controller = new ProyectosController();

    if ($metodo === "GET" && $idOParametro === null) {

        $controller->listar();

    } elseif ($metodo === "GET") {

        $controller->verUno($idOParametro);

    } elseif ($metodo === "POST") {

        $datos = json_decode(file_get_contents("php://input"), true);

        $controller->crear($datos);

    } elseif ($metodo === "PUT") {

        $datos = json_decode(file_get_contents("php://input"), true);

        $controller->editar($idOParametro, $datos);

    } elseif ($metodo === "DELETE") {

        $controller->eliminar($idOParametro);

    }

// =========================================
// REGISTRO DE USUARIOS
// =========================================

} elseif ($recurso === "usuarios" && $metodo === "POST") {

    $controller = new UsuarioController();

    $datos = json_decode(file_get_contents("php://input"), true);

    if (!is_array($datos)) {
        Response::json(["error" => "JSON inválido"], 400);
        exit;
    }

    $controller->registro($datos);

// =========================================
// INICIAR SESIÓN
// =========================================

} elseif ($recurso === "login" && $metodo === "POST") {

    $datos = json_decode(file_get_contents("php://input"), true);

    $controller = new LoginController();
    $controller->login($datos);

// =========================================
// CERRAR SESIÓN
// =========================================

} elseif ($recurso === "cerrar-sesion" && $metodo === "POST") {

    $controller = new LoginController();
    $controller->cerrarSesion();

// =========================================
// VERIFICAR SESIÓN
// =========================================

} elseif ($recurso === "verificar-sesion" && $metodo === "GET") {

    $controller = new LoginController();
    $controller->verificarSesion();

// =========================================
// OBTENER PERFIL
// =========================================

} elseif ($recurso === "perfil" && $metodo === "GET") {

    $controller = new UsuarioController();
    $controller->perfil();

// =========================================
// ACTUALIZAR PERFIL
// =========================================

} elseif ($recurso === "perfil" && $metodo === "PUT") {

    $datos = json_decode(file_get_contents("php://input"), true);

    if (!is_array($datos)) {
        Response::json(["error" => "JSON inválido"], 400);
        exit;
    }

    $controller = new UsuarioController();
    $controller->actualizarPerfil($datos);

// =========================================
// VERIFICAR DNI
// =========================================

} elseif ($recurso === "verificar-dni" && $metodo === "POST") {

    $datos = json_decode(file_get_contents("php://input"), true);

    $controller = new UsuarioController();
    $controller->verificarDni($datos);

// =========================================
// RUTA NO ENCONTRADA
// =========================================

} else {

    Response::json(["error" => "Ruta no válida"], 404);

}

?>
