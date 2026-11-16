<?php

require_once __DIR__ . "/../models/Proveedores.php";
require_once __DIR__ . "/../utils/response.php";

class ProveedoresController {

    private $modelo;

    public function __construct() {
        $this->modelo = new Proveedor();
    }

    // GET /proveedores
    public function listarProveedores() {
        Response::json($this->modelo->getAll(), 200);
    }

    // GET /proveedores/{id}
    public function verUnProveedor($id) {
        $proveedor = $this->modelo->getByid_proveedor($id);

        if ($proveedor) {
            Response::json($proveedor, 200);
        } else {
            Response::json(["error" => "Proveedor no encontrado"], 404);
        }
    }

    // POST /proveedores
    public function crearProveedor($datos) {
        try {
            $idProveedor = $this->modelo->create(
                $datos["proveedor"],
                $datos["nombre_contacto"],
                $datos["telefono"],
                $datos["correo"],
                $datos["ciudad"],
                $datos["direccion"],
                $datos["descripcion"],
                $datos["categoria"],
                $datos["productos"] ?? []
            );

            Response::json([
                "mensaje" => "Proveedor creado correctamente",
                "id_proveedores" => $idProveedor
            ], 201);

        } catch (Exception $e) {
            Response::json([
                "error" => "No se pudo crear el proveedor",
                "detalle" => $e->getMessage()
            ], 500);
        }
    }

    // PUT /proveedores/{id}
    public function editarProveedor($id, $datos) {
        try {
            $this->modelo->update($id, $datos);

            Response::json([
                "mensaje" => "Proveedor actualizado"
            ], 200);

        } catch (Exception $e) {
            Response::json([
                "error" => "No se pudo actualizar el proveedor",
                "detalle" => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /proveedores/{id}
    public function eliminarProveedor($id) {
        $this->modelo->delete($id);

        Response::json([
            "mensaje" => "Proveedor eliminado"
        ], 200);
    }

}
?>