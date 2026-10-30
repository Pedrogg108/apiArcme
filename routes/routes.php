
<?php

require_once __DIR__ . "/../controllers/ProyectosController.php";
require_once __DIR__ . "/../utils/response.php";

// Obtener el método HTTP.
$metodo = $_SERVER["REQUEST_METHOD"];

// Obtener la ruta sin los parámetros de consulta.
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Separar la URL en segmentos.
$partes = explode("/", trim($uri, "/"));

// Buscar dónde comienza el recurso.
$indice = null;

foreach ($partes as $i => $parte) {
    if ($parte === "proyectos" || $parte === "catalogo") {
        $indice = $i;
        break;
    }
}

// Comprobar que la ruta sea válida.
if ($indice === null) {
    Response::json([
        "error" => "Ruta no válida"
    ], 404);
    exit;
}

// Obtener los segmentos del recurso.
$ruta = array_slice($partes, $indice);

$controller = new ProyectosController();


// GET /catalogo
if ($ruta === ["catalogo"] && $metodo === "GET") {
    $controller->listarCatalogo();
    exit;
}


// Rutas de proyectos.
if ($ruta[0] === "proyectos") {

    // GET /proyectos
    if (count($ruta) === 1 && $metodo === "GET") {
        $controller->listar();
        exit;
    }

    // POST /proyectos
    if (count($ruta) === 1 && $metodo === "POST") {

        $contentType = $_SERVER["CONTENT_TYPE"] ?? "";

        if (
            stripos($contentType, "multipart/form-data") !== false
        ) {
            // Recibir campos y archivos enviados con FormData.
            $datos = $_POST;
            $archivos = $_FILES;

        } else {
            // Mantener compatibilidad con JSON.
            $datos = json_decode(
                file_get_contents("php://input"),
                true
            );

            $archivos = [];
        }

        if (!is_array($datos)) {
            Response::json([
                "error" => "El cuerpo de la petición no es válido"
            ], 400);
            exit;
        }

        $controller->crear($datos, $archivos);
        exit;
    }

    // Comprobar el ID para las rutas siguientes.
    if (
        !isset($ruta[1]) ||
        !ctype_digit($ruta[1])
    ) {
        Response::json([
            "error" => "ID de proyecto no válido"
        ], 400);
        exit;
    }

    $id = (int) $ruta[1];

    // GET /proyectos/{id}
    if (count($ruta) === 2 && $metodo === "GET") {
        $controller->verUno($id);
        exit;
    }

    // PUT /proyectos/{id}
    if (count($ruta) === 2 && $metodo === "PUT") {

        $datos = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!is_array($datos)) {
            Response::json([
                "error" => "El cuerpo debe contener un JSON válido"
            ], 400);
            exit;
        }

        $controller->editar($id, $datos);
        exit;
    }

    // DELETE /proyectos/{id}
    if (count($ruta) === 2 && $metodo === "DELETE") {
        $controller->eliminar($id);
        exit;
    }

    // PATCH /proyectos/{id}/catalogo
    if (
        count($ruta) === 3 &&
        $ruta[2] === "catalogo" &&
        $metodo === "PATCH"
    ) {
        $datos = json_decode(
            file_get_contents("php://input"),
            true
        );

        if (!is_array($datos)) {
            Response::json([
                "error" => "El cuerpo debe contener un JSON válido"
            ], 400);
            exit;
        }

        $controller->cambiarVisibilidad($id, $datos);
        exit;
    }
}

// Ruta o método no reconocido.
Response::json([
    "error" => "Ruta o método HTTP no válido"
], 404);