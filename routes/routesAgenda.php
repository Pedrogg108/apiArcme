<?php
// routes/routesAgenda.php
//este archivo recibe los pedidos de angular mira que quiere
//  y lo manda a la funcion que corresponde al controlador



//trae el controlador 
require_once __DIR__ . "/../controllers/AgendaController.php";

// aca se guarda el tipo de pedido( GET, POST, PUT o DELETE)
$metodo = $_SERVER['REQUEST_METHOD'];

//  Partimos la dirección
// Ejemplo: /apiArcme/public/index.php/agenda/5  pasa a ser [apiArcme, public, index.php, agenda, 5]
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$partes = explode('/', trim($ruta, '/'));

//  buscamos  "agenda"si  no esta, este archivo no hace nada
$posicion = array_search('agenda', $partes);
if ($posicion === false) {
    return;
}

// Lo que viene después de "agenda" es sacar el id (puede no tener id)
$id = $partes[$posicion + 1] ?? null;

// lee los datos que manda Angular en formato json y lo convierte en un arreglo php
$datos = json_decode(file_get_contents("php://input"), true);

//  Crea un objeto de la clase EventosController, que es la que tiene
//  las funciones (listar, crear, editar,eliminar)se guarda en $controlador para poder usarlo.
$controlador = new EventosController();

//  Según el tipo de pedido y si hay id, ejecutamos la función que corresponde
if ($metodo === "GET" && $id === null) {
    $controlador->listar();          
} elseif ($metodo === "GET" && $id !== null) {
    $controlador->verUno($id);        
} elseif ($metodo === "POST" && $id === null) {
    $controlador->crear($datos);       
} elseif ($metodo === "PUT" && $id !== null) {
    $controlador->editar($id, $datos); 
} elseif ($metodo === "DELETE" && $id !== null) {
    $controlador->eliminar($id);       
} else {
    Response::json(["error" => "Ruta no válida"], 404);
}

// Cortamos aca para que el archivo principal no siga y no responda dos veces
//tucumana gonzalez
exit;