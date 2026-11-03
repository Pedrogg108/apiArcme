<?php 

// routes/Routes.php
// Incluimos el Controller de"proyectos porque
// las rutas deberán indicar qué método del Controller
// se debe ejecutar según la solicitud recibida.
require_once __DIR__ . "/../controllers/ClientesController.php";

// Obtenemos el método HTTP utilizado en la solicitud.
// Puede ser GET, POST, PUT o DELETE.
$metodo = $_SERVER["REQUEST_METHOD"];
// Obtenemos la URL que fue solicitada.
// trim() elimina las barras "/" que aparecen
// al principio y al final de la URL.
$uri = trim($_SERVER["REQUEST_URI"], "/");
// Dividimos la URL en diferentes partes utilizando "/"
// como separador.

$partes = explode("/", $uri);

$recurso = $partes[count($partes) - 2] ?? null;

// Obtenemos el último elemento de la URL.
// En"proyectos/3, obtenemos "3".
// Si no existe, se utiliza null.
$idOParametro = $partes[count($partes) - 1] ?? null;

// Si idOParametro no es numérico,
// significa que probablemente no tenemos un ID.
// En ese caso, el último elemento corresponde al nombre
// del recurso.
if (!is_numeric($idOParametro)) {
 // Guardamos el nombre del recurso.
 $recurso = $idOParametro;
 // Como no tenemos un ID numérico,
 // establecemos el valor como null.
 $idOParametro = null;
}

// Comprobamos si el recurso solicitado
// corresponde a "proyectos".
if ($recurso === "clientes") {
 // Creamos un objeto del Controller de"proyectos.
 // A través de este objeto podremos ejecutar
 // los métodos correspondientes.
 $controller = new ClientesController();
 // Si el método HTTP es GET y no se recibió un ID,
 // significa que se están solicitando todos los"proyectos.
 if ($metodo === "GET" && $idOParametro === null) {
 // Ejecutamos el método listar() del Controller.
 $controller->listarClientes();
 // Si el método HTTP es GET y existe un ID,
 // significa que se está solicitando un producto específico.
 } elseif ($metodo === "GET") {     
 // Ejecutamos verUno() enviando el ID recibido.
 $controller->verUnCliente($idOParametro);
 // Si el método HTTP es POST,
 // significa que se quiere crear un nuevo producto.
 } elseif ($metodo === "POST") {
 // Obtenemos los datos enviados por el cliente
 // en el cuerpo de la solicitud.
 //
 // file_get_contents("php://input") obtiene
 // el contenido enviado.
 //
 // json_decode(..., true) convierte el JSON recibido
 // en un arreglo asociativo de PHP.
 $datos = json_decode(file_get_contents("php://input"), true);
 // Enviamos los datos al método crear()
 // del Controller.
 $controller->crearCliente($datos);
 // Si el método HTTP es PUT,
 // significa que se quiere modificar un producto.
 } elseif ($metodo === "PUT") {
 // Obtenemos los datos enviados por el cliente
 // y los convertimos de JSON a un arreglo de PHP.
 $datos = json_decode(file_get_contents("php://input"), true);
 // Ejecutamos el método editar() del Controller.
 // Le enviamos el ID del producto y los nuevos datos.
 $controller->editarCliente($idOParametro, $datos);
 // Si el método HTTP es DELETE,
 // significa que se quiere eliminar un producto.
 } elseif ($metodo === "DELETE") {
 // Ejecutamos el método eliminar()
 // enviando el ID del producto.
 $controller->eliminarCliente($idOParametro);
 }
} else {
 // Si el recurso solicitado no coincide con "proyectos",
 // enviamos un mensaje de error.
 //
 // El código 404 indica que la ruta o recurso
 // no fue encontrado.
 Response::json(["error" => "Ruta no válida"], 404);
}

?>