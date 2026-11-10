
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

        // Verificar los campos obligatorios.
        $campos = [
            "titulo",
            "superficie",
            "ubicacion",
            "precio",
            "descripcion",
            "nomenclatura",
            "estado",
            "memoria",
            "fecha_inicio",
            "fecha_finalizacion"
        ];

        foreach ($campos as $campo) {

            if (!array_key_exists($campo, $datos)) {
                Response::json([
                    "error" => "Falta el campo: " . $campo
                ], 400);
                return;
            }
        }

        // Crear el proyecto.
        $id_obra = $this->modelo->create(
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

        if (!$id_obra) {
            Response::json([
                "error" => "No se pudo crear el proyecto"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => "Proyecto creado correctamente",
            "id_obra" => $id_obra
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
    public function cambiarVisibilidad($id) {

        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json([
                "error" => "Proyecto no encontrado"
            ], 404);
            return;
        }

        $actualizado = $this->modelo->cambiarVisibilidadCatalogo($id);

        if (!$actualizado) {
            Response::json([
                "error" => "No se pudo cambiar la visibilidad"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => "Visibilidad del catálogo actualizada"
        ], 200);
    }
}
