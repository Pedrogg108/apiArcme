
<?php

require_once __DIR__ . "/../controllers/ClientesController.php";

$metodo = $_SERVER["REQUEST_METHOD"];

// Obtener la ruta solicitada
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$path = str_replace($scriptName, '', $requestUri);
$path = trim($path, '/');

// Eliminar index.php si aparece en la ruta
$path = preg_replace('/^index\.php\/?/', '', $path);

$partes = explode('/', $path);

$recurso = $partes[0] ?? null;
$idOParametro = $partes[1] ?? null;

// Comprobar si la ruta pertenece a clientes
if ($recurso === "clientes") {

    $controller = new ClientesController();

    if ($metodo === "GET" && $idOParametro === null) {

        $controller->listarClientes();

    } elseif ($metodo === "GET" && $idOParametro !== null) {

        $controller->verUnCliente($idOParametro);

    } elseif ($metodo === "POST" && $idOParametro === null) {

        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->crearCliente($datos);

    } elseif ($metodo === "PUT" && $idOParametro !== null) {

        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->editarCliente($idOParametro, $datos);

    } elseif ($metodo === "DELETE" && $idOParametro !== null) {

        $controller->eliminarCliente($idOParametro);

    } else {

        Response::json(["error" => "Método o ruta no permitidos"], 405);

    }

    exit;

    if ($posicion === false) {
    return;
}
}

?>
