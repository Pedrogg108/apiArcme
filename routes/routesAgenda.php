<?php
// routes/routesAgenda.php
// Acá se decide qué función del controlador se ejecuta con cada pedido a /agenda.


require_once __DIR__ . "/../controllers/AgendaController.php";

// 1. Vemos qué tipo de pedido llegó: GET, POST, PUT o DELETE
$metodo = $_SERVER['REQUEST_METHOD'];

// 2. Partimos la dirección en pedacitos.
// Ejemplo: /apiArcme/public/index.php/agenda/5 -> [apiArcme, public, index.php, agenda, 5]
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$partes = explode('/', trim($ruta, '/'));

// 3. Buscamos la palabra "agenda". Si no está, este archivo no hace nada
$posicion = array_search('agenda', $partes);
if ($posicion === false) {
    return;
}

// 4. Lo que viene después de "agenda" es el id (puede no haber)
$id = $partes[$posicion + 1] ?? null;

// 5. Los datos que manda Angular (el JSON del evento)
$datos = json_decode(file_get_contents("php://input"), true);

// 6. Creamos el controlador
$controlador = new EventosController();

// 7. Según el tipo de pedido y si hay id, ejecutamos la función que corresponde
if ($metodo === "GET" && $id === null) {
    $controlador->listar();            // GET /agenda
} elseif ($metodo === "GET" && $id !== null) {
    $controlador->verUno($id);         // GET /agenda/5
} elseif ($metodo === "POST" && $id === null) {
    $controlador->crear($datos);       // POST /agenda
} elseif ($metodo === "PUT" && $id !== null) {
    $controlador->editar($id, $datos); // PUT /agenda/5
} elseif ($metodo === "DELETE" && $id !== null) {
    $controlador->eliminar($id);       // DELETE /agenda/5
} else {
    Response::json(["error" => "Ruta no válida"], 404);
}

// 8. Cortamos acá para que el archivo principal no siga y no responda dos veces
//tucumana gonzalez
exit;