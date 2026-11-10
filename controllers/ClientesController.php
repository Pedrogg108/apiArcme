<?php 

require_once __DIR__ .
"/../models/Clientes.php";
// Incluimos la clase Response para poder enviar las respuestas de la API en
//formato JSON.
require_once __DIR__ .
"/../utils/response.php";

class ClientesController { 
    
 // Variable donde se almacena un objeto del Model Clientes.
 // A través de esta variable podemos utilizar
 // los métodos definidos en el Model.
 private $modelo;
 // Constructor de la clase.
 // Se ejecuta automáticamente cuando se crea un objeto ClientesController.
 public function __construct() {
 // Creamos un objeto de la clase Clientes.
 // Clientes hereda de Model y por eso puede utilizar
 // la conexión con la base de datos.
 $this->modelo = new Cliente();
 }

 // GET /proyectos
 //
 // Endpoint utilizado para obtener todos los Proyectoss.
 public function listarClientes() {
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
 public function verUnCliente($id) {
 // Le pedimos al Model que busque el Cliente
 // utilizando el ID recibido.
 $Clientes = $this->modelo->getByid_cliente($id);
 // Comprobamos si el Proyectos fue encontrado.
 //
 // Si existe, lo enviamos con código 200.
 // Si no existe, enviamos un mensaje de error
 // con código 404 (recurso no encontrado).
 $Clientes ? Response::json($Clientes, 200): Response::json(["error"=> "Cliente no encontrado"], 404);
 }

 // POST /Cliente
 //
 // Endpoint utilizado para crear un nuevo Cliente.
 // Esta operación estará disponible solamente para administradores.
 public function crearCliente($datos) {
 // Enviamos al Model los datos necesarios
 // para crear el nuevo Proyectos.
 $this->modelo->create(
 $datos["nombre"],
$datos["telefono"],
 $datos["direccion"],
$datos["ciudad"],
$datos["correo"]

 );
 // Informamos al usuario que el cliente fue creado correctamente.
 // El código 201 significa "creado".
 Response::json(["mensaje" => "Cliente creado"], 201);
 }

 // PUT /Cliente/{id}
 //
 // Endpoint utilizado para modificar un Cliente existente.
 // Recibe el ID del Cliente y los nuevos datos.
 public function editarCliente($id, $datos) {
 // Le pedimos al Model que actualice el Cliente
 // utilizando el ID y los nuevos datos.
 $this->modelo->update($id, $datos);
 // Informamos al usuario que el Cliente
 // fue actualizado correctamente.
 Response::json(["mensaje" => "Cliente actualizado"], 200);
 }

 // DELETE /Clientes/{id}
 //
 // Endpoint utilizado para eliminar un Cliente.
 // Recibe como parámetro el ID del Cliente.
 public function eliminarCliente($id) {
 // Le pedimos al Model que elimine
 // el Cliente indicado.
 $this->modelo->delete($id);
 // Informamos al usuario que el Cliente
 // fue eliminado correctamente.
 Response::json(["mensaje" => "Cliente eliminado"], 200);
 }

}

?>