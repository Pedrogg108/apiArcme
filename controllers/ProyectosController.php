
<?php

require_once __DIR__ . "/../models/proyectos.php";
require_once __DIR__ . "/../utils/response.php";

class ProyectosController {

    private $modelo;

    public function __construct() {
        $this->modelo = new Proyecto();
    }

    // GET /proyectos
    public function listar() {
        Response::json($this->modelo->getAll(), 200);
    }

    // GET /proyectos/{id}
    public function verUno($id) {
        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json([
                "error" => "Proyecto no encontrado"
            ], 404);
            return;
        }

        Response::json($proyecto, 200);
    }

    // POST /proyectos
    public function crear($datos) {

        $creado = $this->modelo->create(
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

        if (!$creado) {
            Response::json([
                "error" => "No se pudo crear el proyecto"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => "Proyecto creado"
        ], 201);
    }

    // PUT /proyectos/{id}
    public function editar($id, $datos) {

        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json([
                "error" => "Proyecto no encontrado"
            ], 404);
            return;
        }

        $actualizado = $this->modelo->update($id, $datos);

        if (!$actualizado) {
            Response::json([
                "error" => "No se pudo actualizar el proyecto"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => "Proyecto actualizado"
        ], 200);
    }

    // DELETE /proyectos/{id}
    public function eliminar($id) {

        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json([
                "error" => "Proyecto no encontrado"
            ], 404);
            return;
        }

        $eliminado = $this->modelo->delete($id);

        if (!$eliminado) {
            Response::json([
                "error" => "No se pudo eliminar el proyecto"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => "Proyecto eliminado"
        ], 200);
    }

    // GET /catalogo
    public function listarCatalogo() {
        Response::json($this->modelo->getCatalogo(), 200);
    }

    // PATCH /proyectos/{id}/catalogo
    public function cambiarVisibilidad($id, $datos) {

        if (!isset($datos["visible_catalogo"])) {
            Response::json([
                "error" => "Falta el campo visible_catalogo"
            ], 400);
            return;
        }

        $visible = filter_var(
            $datos["visible_catalogo"],
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if ($visible === null) {
            Response::json([
                "error" => "visible_catalogo debe ser true o false"
            ], 400);
            return;
        }

        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json([
                "error" => "Proyecto no encontrado"
            ], 404);
            return;
        }

        $actualizado = $this->modelo->cambiarVisibilidadCatalogo(
            $id,
            $visible
        );

        if (!$actualizado) {
            Response::json([
                "error" => "No se pudo cambiar la visibilidad"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => $visible
                ? "Proyecto publicado en el catálogo"
                : "Proyecto ocultado del catálogo",
            "visible_catalogo" => $visible
        ], 200);
    }
}
