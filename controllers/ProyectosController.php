<?php
// controllers/ProyectosController.php
// Incluimos el Model Proyectos porque el Controller
// necesita utilizar sus métodos para trabajar con los Proyectoss.
require_once __DIR__ .
"/../models/proyectos.php";
// Incluimos la clase Response para poder enviar las respuestas de la API en
//formato JSON.
require_once __DIR__ .
"/../utils/response.php";
class ProyectosController {
 // Variable donde se almacena un objeto del Model Proyectos.
 // A través de esta variable podemos utilizar
 // los métodos definidos en el Model.
 private $modelo;
 // Constructor de la clase.
 // Se ejecuta automáticamente cuando se crea un objeto ProyectosController.
 public function __construct() {
 // Creamos un objeto de la clase Proyectos.
 // Proyectos hereda de Model y por eso puede utilizar
 // la conexión con la base de datos.
 $this->modelo = new Proyecto();//aca estaba new Proyectos
 }
 // GET /proyectos
 //
 // Endpoint utilizado para obtener todos los Proyectoss.
 public function listar() {
 // Le pedimos al Model que obtenga todos los Proyectoss
 // mediante el método getAll().
 //
 // Luego enviamos los datos al cliente en formato JSON.
 // El código 200 indica que la solicitud fue exitosa.
 Response::json($this->modelo->getAll(), 200);
 }
 // GET /Proyectoss/{id}
 //
 // Endpoint utilizado para obtener un Proyectos específico.
 // Recibe el ID del Proyectos como parámetro.
 public function verUno($id) {
 // Le pedimos al Model que busque el Proyectos
 // utilizando el ID recibido.
 $Proyectos = $this->modelo->getById_obra($id);
 // Comprobamos si el Proyectos fue encontrado.
 //
 // Si existe, lo enviamos con código 200.
 // Si no existe, enviamos un mensaje de error
 // con código 404 (recurso no encontrado).
 $Proyectos ? Response::json($Proyectos, 200): Response::json(["error"=> "Proyectos no encontrado"], 404);
 }
 // POST /Proyectoss
 //
 // Endpoint utilizado para crear un nuevo Proyectos.
 // Esta operación estará disponible solamente para administradores.
 public function crear($datos) {
 // Enviamos al Model los datos necesarios
 // para crear el nuevo Proyectos.
 $this->modelo->create(
 $datos["titulo"],
$datos["superficie"],
 $datos["ubicacion"],
$datos["precio"],
$datos["descripcion"],
$datos["nomenclatura"],
$datos["estado"],
$datos["memoria"],
$datos["fecha_inicio"],
$datos["fecha_finalizacion"]








 );
 // Informamos al cliente que el Proyectos
 // fue creado correctamente.
 //
 // El código 201 significa "creado".
 Response::json(["mensaje"
=> "Proyectos creado"], 201);
 }
 // PUT /Proyectoss/{id}
 //
 // Endpoint utilizado para modificar un Proyectos existente.
 // Recibe el ID del Proyectos y los nuevos datos.
 public function editar($id, $datos) {
 // Le pedimos al Model que actualice el Proyectos
 // utilizando el ID y los nuevos datos.
 $this->modelo->update($id,
$datos);
 // Informamos al cliente que el Proyectos
 // fue actualizado correctamente.
 Response::json(["mensaje"
=> "Proyectos actualizado"], 200);
 }
 // DELETE /Proyectoss/{id}
 //
 // Endpoint utilizado para eliminar un Proyectos.
 // Recibe como parámetro el ID del Proyectos.
 public function eliminar($id) {
 // Le pedimos al Model que elimine
 // el Proyectos indicado.
 $this->modelo->delete($id);
 // Informamos al cliente que el Proyectos
 // fue eliminado correctamente.
 Response::json(["mensaje"
=> "Proyectos eliminado"], 200);
 }
 }
?>
