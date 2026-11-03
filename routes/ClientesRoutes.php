<?php 

// routes/Routes.php
// Incluimos el Controller de Clientes para que
// las rutas indiquen qué método del Controller
// se debe ejecutar según la solicitud recibida.
require_once __DIR__ . "/../controllers/ClientesController.php";

// Obtenemos el método HTTP utilizado en la solicitud.
// Puede ser GET, POST, PUT o DELETE.
$metodo = $_SERVER["REQUEST_METHOD"];

// Obtenemos la URL que fue solicitada.
// trim() elimina las barras "/" al principio y al final.
$uri = trim($_SERVER["REQUEST_URI"], "/");

// Dividimos la URL en diferentes partes utilizando "/" como separador.
$partes = explode("/", $uri);

$recurso = $partes[count($partes) - 2] ?? null;

// Obtenemos el último elemento de la URL.
$idOParametro = $partes[count($partes) - 1] ?? null;

// Si idOParametro no es numérico, corresponde al nombre del recurso.
if (!is_numeric($idOParametro)) {
    $recurso = $idOParametro;
    $idOParametro = null;
}

// Comprobamos si el recurso solicitado corresponde a "clientes".
if ($recurso === "clientes") {
    
    $controller = new ClientesController();

    // Si el método HTTP es GET y no se recibió un ID: listar todos
    if ($metodo === "GET" && $idOParametro === null) {
        $controller->listarClientes();

    // Si el método HTTP es GET y existe un ID: ver un cliente específico
    } elseif ($metodo === "GET") {     
        $controller->verUnCliente($idOParametro);

    // Si el método HTTP es POST: crear un nuevo cliente
    } elseif ($metodo === "POST") {
        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->crearCliente($datos);

    // Si el método HTTP es PUT: modificar un cliente
    } elseif ($metodo === "PUT") {
        $datos = json_decode(file_get_contents("php://input"), true);
        $controller->editarCliente($idOParametro, $datos);

    // Si el método HTTP es DELETE: eliminar un cliente
    } elseif ($metodo === "DELETE") {
        $controller->eliminarCliente($idOParametro);
    }

} else {
    // Si el recurso no coincide, devolvemos error 404
    Response::json(["error" => "Ruta no válida"], 404);
}

?>