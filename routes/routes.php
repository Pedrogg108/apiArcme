<?php

require_once __DIR__ . "/../controllers/ProyectosController.php";
require_once __DIR__ . "/../utils/response.php";

$metodo = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$partes = explode("/", trim($uri, "/"));
$indice = null;

foreach ($partes as $i => $parte) {
    if ($parte === "proyectos" || $parte === "catalogo") {
        $indice = $i;
        break;
    }
}

if ($indice === null) {
    Response::json(["error" => "Ruta no válida"], 404);
    exit;
}

$ruta = array_slice($partes, $indice);
$controller = new ProyectosController();

if ($ruta === ["catalogo"] && $metodo === "GET") {
    $controller->listarCatalogo();
    exit;
}

if ($ruta[0] === "proyectos") {

    if (count($ruta) === 1 && $metodo === "GET") {
        $controller->listar();
        exit;
    }

    if (count($ruta) === 1 && $metodo === "POST") {
        $datos = json_decode(file_get_contents("php://input"), true);
        if (!is_array($datos)) {
            Response::json(["error" => "El cuerpo debe contener un JSON válido"], 400);
            exit;
        }
        $controller->crear($datos);
        exit;
    }

    // GET /proyectos/cliente/{id_cliente}
    if (
        count($ruta) === 3 &&
        $ruta[1] === "cliente" &&
        ctype_digit($ruta[2]) &&
        $metodo === "GET"
    ) {
        $controller->listarPorCliente((int) $ruta[2]);
        exit;
    }

    if (!isset($ruta[1]) || !ctype_digit($ruta[1])) {
        Response::json(["error" => "ID de proyecto no válido"], 400);
        exit;
    }

    $id = (int) $ruta[1];

    if (count($ruta) === 2 && $metodo === "GET") {
        $controller->verUno($id);
        exit;
    }

    if (count($ruta) === 2 && $metodo === "PUT") {
        $datos = json_decode(file_get_contents("php://input"), true);
        if (!is_array($datos)) {
            Response::json(["error" => "El cuerpo debe contener un JSON válido"], 400);
            exit;
        }
        $controller->editar($id, $datos);
        exit;
    }

    if (count($ruta) === 2 && $metodo === "DELETE") {
        $controller->eliminar($id);
        exit;
    }

    if (
        count($ruta) === 3 &&
        $ruta[2] === "catalogo" &&
        $metodo === "PATCH"
    ) {
        $controller->cambiarVisibilidad($id);
        exit;
    }
}

Response::json(["error" => "Ruta o método HTTP no válido"], 404);