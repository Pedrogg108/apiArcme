<?php

require_once __DIR__ . "/../models/proyectos.php";
require_once __DIR__ . "/../utils/response.php";

class ProyectosController {

    private $modelo;

    public function __construct() {
        $this->modelo = new Proyecto();
    }

    public function listar() {
        Response::json($this->modelo->getAll(), 200);
    }

    public function verUno($id) {
        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json(["error" => "Proyecto no encontrado"], 404);
            return;
        }

        Response::json($proyecto, 200);
    }

    public function crear($datos) {
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
            "fecha_finalizacion", 
            "id_cliente"
        ];

        foreach ($campos as $campo) {
            if (!array_key_exists($campo, $datos)) {
                Response::json(["error" => "Falta el campo: " . $campo], 400);
                return;
            }
        }

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
            $datos["fecha_finalizacion"],
            $datos["id_cliente"]
        );

        if (!$id_obra) {
            Response::json(["error" => "No se pudo crear el proyecto"], 500);
            return;
        }

        Response::json([
            "mensaje" => "Proyecto creado correctamente",
            "id_obra" => $id_obra
        ], 201);
    }

    public function editar($id, $datos) {
        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json(["error" => "Proyecto no encontrado"], 404);
            return;
        }

        $actualizado = $this->modelo->update($id, $datos);

        if (!$actualizado) {
            Response::json(["error" => "No se pudo actualizar el proyecto"], 500);
            return;
        }

        Response::json(["mensaje" => "Proyecto actualizado"], 200);
    }

    public function eliminar($id) {
        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json(["error" => "Proyecto no encontrado"], 404);
            return;
        }

        $eliminado = $this->modelo->delete($id);

        if (!$eliminado) {
            Response::json(["error" => "No se pudo eliminar el proyecto"], 500);
            return;
        }

        Response::json(["mensaje" => "Proyecto eliminado"], 200);
    }

    public function listarCatalogo() {
        Response::json($this->modelo->getCatalogo(), 200);
    }

    public function cambiarVisibilidad($id) {
        $proyecto = $this->modelo->getByid_obra($id);

        if (!$proyecto) {
            Response::json(["error" => "Proyecto no encontrado"], 404);
            return;
        }

        $actualizado = $this->modelo->cambiarVisibilidadCatalogo($id);

        if (!$actualizado) {
            Response::json(["error" => "No se pudo cambiar la visibilidad"], 500);
            return;
        }

        Response::json(["mensaje" => "Visibilidad del catálogo actualizada"], 200);
    }

    public function listarPorCliente($id_cliente) {
        $proyectos = $this->modelo->getByCliente($id_cliente);
        Response::json($proyectos, 200);
    }
}