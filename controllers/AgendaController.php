<?php
// controllers/EventosController.php
// Recibe lo que pide Angular, le habla al Model y devuelve la respuesta.
require_once __DIR__ . "/../models/Agenda.php";
require_once __DIR__ . "/../utils/response.php";

class EventosController {

    private $modelo;

    public function __construct() {
        $this->modelo = new Evento();
    }

    // GET /agenda
    public function listar() {
        Response::json($this->modelo->getAll(), 200);
    }

    // GET /agenda/{id}
    public function verUno($id) {
        $evento = $this->modelo->getById($id);

        if (!$evento) {
            Response::json(["error" => "Evento no encontrado"], 404);
            return;
        }

        Response::json($evento, 200);
    }

    // POST /agenda
    public function crear($datos) {
        $creado = $this->modelo->create(
            $datos["titulo"],
            $datos["fecha"],
            $datos["hora"],
            $datos["categoria"],
            $datos["descripcion"]
        );

        if (!$creado) {
            Response::json(["error" => "No se pudo crear el evento"], 500);
            return;
        }

        Response::json(["mensaje" => "Evento creado"], 201);
    }

    // PUT /agenda/{id}
    public function editar($id, $datos) {
        $evento = $this->modelo->getById($id);

        if (!$evento) {
            Response::json(["error" => "Evento no encontrado"], 404);
            return;
        }

        $actualizado = $this->modelo->update($id, $datos);

        if (!$actualizado) {
            Response::json(["error" => "No se pudo actualizar el evento"], 500);
            return;
        }

        Response::json(["mensaje" => "Evento actualizado"], 200);
    }

    // DELETE /agenda/{id}
    public function eliminar($id) {
        $evento = $this->modelo->getById($id);

        if (!$evento) {
            Response::json(["error" => "Evento no encontrado"], 404);
            return;
        }

        $eliminado = $this->modelo->delete($id);

        if (!$eliminado) {
            Response::json(["error" => "No se pudo eliminar el evento"], 500);
            return;
        }

        Response::json(["mensaje" => "Evento eliminado"], 200);
    }
}