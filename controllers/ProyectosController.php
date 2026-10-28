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
    public function crear($datos, $archivos = []) {

        // Verificar que estén los campos obligatorios.
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

        // Crear el proyecto y recuperar su ID.
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

        $fotosGuardadas = [];

        // Procesar las fotografías si se enviaron.
        if (
            isset($archivos["fotos"]) &&
            is_array($archivos["fotos"]["name"])
        ) {

            $fotos = $archivos["fotos"];

            // Directorio físico donde se guardarán las imágenes.
            $directorio = dirname(__DIR__) .
                          "/public/uploads/proyectos";

            if (
                !is_dir($directorio) &&
                !mkdir($directorio, 0755, true) &&
                !is_dir($directorio)
            ) {
                Response::json([
                    "error" => "No se pudo crear la carpeta de fotografías",
                    "id_obra" => $id_obra
                ], 500);
                return;
            }

            // Tipos MIME permitidos.
            $tiposPermitidos = [
                "image/jpeg" => "jpg",
                "image/png" => "png",
                "image/webp" => "webp"
            ];

            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $orden = 1;

            foreach ($fotos["name"] as $i => $nombreOriginal) {

                // Ignorar espacios vacíos del selector.
                if (
                    !isset($fotos["error"][$i]) ||
                    $fotos["error"][$i] === UPLOAD_ERR_NO_FILE
                ) {
                    continue;
                }

                if ($fotos["error"][$i] !== UPLOAD_ERR_OK) {
                    Response::json([
                        "error" => "Error al recibir una fotografía",
                        "id_obra" => $id_obra
                    ], 400);
                    return;
                }

                $rutaTemporal = $fotos["tmp_name"][$i];

                // Limitar cada imagen a 5 MB.
                if ($fotos["size"][$i] > 5 * 1024 * 1024) {
                    Response::json([
                        "error" => "Cada fotografía debe pesar como máximo 5 MB",
                        "id_obra" => $id_obra
                    ], 400);
                    return;
                }

                if (!is_uploaded_file($rutaTemporal)) {
                    Response::json([
                        "error" => "El archivo recibido no es válido",
                        "id_obra" => $id_obra
                    ], 400);
                    return;
                }

                // Detectar el tipo real del archivo.
                $mime = $finfo->file($rutaTemporal);

                if (!isset($tiposPermitidos[$mime])) {
                    Response::json([
                        "error" => "Solo se permiten imágenes JPG, PNG y WEBP",
                        "id_obra" => $id_obra
                    ], 400);
                    return;
                }

                // Generar un nombre que no dependa del nombre original.
                $nombreArchivo = bin2hex(random_bytes(16)) .
                                 "." . $tiposPermitidos[$mime];

                $destino = $directorio . "/" . $nombreArchivo;

                if (!move_uploaded_file($rutaTemporal, $destino)) {
                    Response::json([
                        "error" => "No se pudo guardar una fotografía",
                        "id_obra" => $id_obra
                    ], 500);
                    return;
                }

                // Ruta pública que se guardará en MySQL.
                $url = "/apiArcme/public/uploads/proyectos/" .
                       $nombreArchivo;

                // Registrar la fotografía en fotocobra.
                $guardado = $this->modelo->agregarFoto(
                    $id_obra,
                    $url,
                    $orden
                );

                if (!$guardado) {

                    // Evitar dejar este archivo si falló el INSERT.
                    if (is_file($destino)) {
                        unlink($destino);
                    }

                    Response::json([
                        "error" => "No se pudo registrar una fotografía en la base de datos",
                        "id_obra" => $id_obra
                    ], 500);
                    return;
                }

                $fotosGuardadas[] = [
                    "url" => $url,
                    "orden" => $orden
                ];

                $orden++;
            }
        }

        Response::json([
            "mensaje" => "Proyecto creado correctamente",
            "id_obra" => $id_obra,
            "fotos" => $fotosGuardadas
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