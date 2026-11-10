<?php
require_once __DIR__ . "/../controllers/ClientesController.php";

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

// Si la ruta pertenece a clientes, limpiamos la salida previa de otros archivos
if ($recurso === "clientes") {
    // Si algún archivo previo (como routes.php) imprimió un error, borramos la salida previa
    if (ob_get_length()) {
        ob_clean();
    }

    $controller = new ClientesController();

    if ($metodo === "GET" && $idOParametro === null) {
        $controller->listarClientes();
    } elseif ($metodo === "GET") {     
        $controller->verUnCliente($idOParametro);
    } elseif ($metodo === "POST") {
        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->crearCliente($datos);
    } elseif ($metodo === "PUT") {
        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->editarCliente($idOParametro, $datos);
    } elseif ($metodo === "DELETE") {
        $controller->eliminarCliente($idOParametro);
    }
    
    // Finalizamos la ejecución para evitar que se sigan procesando otros scripts
    exit;
}
?>