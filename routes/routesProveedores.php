
<?php
require_once __DIR__ . "/../controllers/ProveedoresController.php";

$metodo = $_SERVER["REQUEST_METHOD"];

// Limpiamos la URI obteniendo la ruta relativa después de public/
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace($scriptName, '', $requestUri);
$path = trim($path, '/');

// Si la URL pasa por index.php, lo removemos del path
$path = preg_replace('/^index\.php\/?/', '', $path);

$partes = explode('/', $path);
$recurso = $partes[0] ?? null;
$idOParametro = $partes[1] ?? null;

// Si la ruta pertenece a proveedores
if ($recurso === "proveedores") {

    if (ob_get_length()) {
        ob_clean();
    }

    $controller = new ProveedoresController();

    if ($metodo === "GET" && $idOParametro === null) {
        $controller->listarProveedores();
    } elseif ($metodo === "GET") {
        $controller->verUnProveedor($idOParametro);
    } elseif ($metodo === "POST") {
        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->crearProveedor($datos);
    } elseif ($metodo === "PUT") {
        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->editarProveedor($idOParametro, $datos);
    } elseif ($metodo === "DELETE") {
        $controller->eliminarProveedor($idOParametro);
    }

    exit;
}
?>
