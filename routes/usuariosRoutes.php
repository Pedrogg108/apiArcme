
<?php

require_once __DIR__ . "/../controllers/UsuarioController.php";
require_once __DIR__ . "/../controllers/LoginController.php";
require_once __DIR__ . "/../utils/response.php";

// Obtener el método HTTP
$metodo = $_SERVER["REQUEST_METHOD"];

// Obtener la ruta sin los parámetros de consulta
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Separar la URL en segmentos
$partes = explode("/", trim($uri, "/"));

// Buscar dónde comienza el recurso
$indice = null;

$recursos = [
    "usuarios",
    "login",
    "cerrar-sesion",
    "verificar-sesion",
    "perfil",
    "verificar-dni"
];

foreach ($partes as $i => $parte) {
    if (in_array($parte, $recursos, true)) {
        $indice = $i;
        break;
    }
}

// Comprobar que la ruta sea válida
if ($indice === null) {
    Response::json([
        "error" => "Ruta no válida"
    ], 404);
    exit;
}

// Obtener los segmentos de la ruta
$ruta = array_slice($partes, $indice);

// Función para leer y validar JSON
function obtenerDatosJSON(): array {

    $datos = json_decode(file_get_contents("php://input"), true);

    if (!is_array($datos)) {
        Response::json([
            "error" => "JSON inválido"
        ], 400);
        exit;
    }

    return $datos;
}

// =========================================
// REGISTRO DE USUARIOS
// =========================================

// POST /usuarios
if ($ruta === ["usuarios"] && $metodo === "POST") {

    $controller = new UsuarioController();
    $controller->registro(obtenerDatosJSON());
    exit;
}

// =========================================
// INICIAR SESIÓN
// =========================================

// POST /login
if ($ruta === ["login"] && $metodo === "POST") {

    $controller = new LoginController();
    $controller->login(obtenerDatosJSON());
    exit;
}

// =========================================
// CERRAR SESIÓN
// =========================================

// POST /cerrar-sesion
if ($ruta === ["cerrar-sesion"] && $metodo === "POST") {

    $controller = new LoginController();
    $controller->cerrarSesion();
    exit;
}

// =========================================
// VERIFICAR SESIÓN
// =========================================

// GET /verificar-sesion
if ($ruta === ["verificar-sesion"] && $metodo === "GET") {

    $controller = new LoginController();
    $controller->verificarSesion();
    exit;
}

// =========================================
// OBTENER PERFIL
// =========================================

// GET /perfil
if ($ruta === ["perfil"] && $metodo === "GET") {

    $controller = new UsuarioController();
    $controller->perfil();
    exit;
}

// =========================================
// ACTUALIZAR PERFIL
// =========================================

// PUT /perfil
if ($ruta === ["perfil"] && $metodo === "PUT") {

    $controller = new UsuarioController();
    $controller->actualizarPerfil(obtenerDatosJSON());
    exit;
}

// =========================================
// SUBIR FOTO DE PERFIL
// =========================================

// POST /perfil/foto
if ($ruta === ["perfil", "foto"] && $metodo === "POST") {

    $controller = new UsuarioController();
    $controller->subirFoto();
    exit;
}

// =========================================
// VERIFICAR DNI
// =========================================

// POST /verificar-dni
if ($ruta === ["verificar-dni"] && $metodo === "POST") {

    $controller = new UsuarioController();
    $controller->verificarDni(obtenerDatosJSON());
    exit;
}

// =========================================
// RUTA O MÉTODO NO RECONOCIDO
// =========================================

Response::json([
    "error" => "Ruta o método HTTP no válido"
], 404);

