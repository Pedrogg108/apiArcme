<?php
// controllers/UsuarioController.php
// Incluimos el Model Usuario porque el Controller
// necesita utilizar sus métodos para trabajar con los Proyectoss.
require_once __DIR__ .
"/../models/Usuario.php";
// Incluimos la clase Response para poder enviar las respuestas de la API en
//formato JSON.
require_once __DIR__ .
"/../utils/response.php";

class UsuarioController {
    private $modelo;

    public function __construct() {
 // Creamos un objeto de la clase Proyectos.
 // Proyectos hereda de Model y por eso puede utilizar
 // la conexión con la base de datos.
 $this->modelo = new Usuario();//aca estaba new Proyectos
 }

  public function registro($datos) {
 // Enviamos al Model los datos necesarios
 // para crear el nuevo Proyectos.
 $this->modelo->create(
 $datos["dni"],
$datos["contraseña"],
 $datos["nombre"],
$datos["telefono"],
$datos["correo"],

 );
}
}
?>