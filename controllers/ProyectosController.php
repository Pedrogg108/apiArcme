
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
            "fecha_finalizacion",
            "portada"
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
            $datos["fecha_finalizacion"],
            $datos["portada"]
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

    // POST /proyectos/memoria
    public function subirMemoria() {

        // Verificar que se haya enviado un archivo.
        if (!isset($_FILES["memoria"])) {
            Response::json([
                "error" => "No se seleccionó ningún archivo"
            ], 400);
            return;
        }

        $archivo = $_FILES["memoria"];

        if ($archivo["error"] !== UPLOAD_ERR_OK) {
            Response::json([
                "error" => "Error al subir el archivo"
            ], 400);
            return;
        }

        // Limitar el tamaño a 10 MB.
        if ($archivo["size"] > 10 * 1024 * 1024) {
            Response::json([
                "error" => "El archivo no puede superar los 10 MB"
            ], 400);
            return;
        }

        // Comprobar el tipo real del archivo.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipo = $finfo->file($archivo["tmp_name"]);

        if ($tipo !== "application/pdf") {
            Response::json([
                "error" => "El archivo debe ser un PDF"
            ], 400);
            return;
        }

        // Crear la carpeta si todavía no existe.
        $carpeta = __DIR__ . "/../public/uploads/memorias/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        // Generar un nombre único para el PDF.
        $nombreArchivo = bin2hex(random_bytes(16)) . ".pdf";

        $destino = $carpeta . $nombreArchivo;

        // Guardar el archivo en el servidor.
        if (!move_uploaded_file($archivo["tmp_name"], $destino)) {
            Response::json([
                "error" => "No se pudo guardar el archivo"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => "Memoria subida correctamente",
            "memoria" => "uploads/memorias/" . $nombreArchivo
        ], 201);
    }


    public function subirPortada(){
    
        // Verificar que se haya enviado un archivo.
        if (!isset($_FILES["portada"])) {
            Response::json([
                "error" => "No se seleccionó ningún archivo"
            ], 400);
            return;
        }

        $archivoImg = $_FILES["portada"];

        if ($archivoImg["error"] !== UPLOAD_ERR_OK) {
            Response::json([
                "error" => "Error al subir el archivo"
            ], 400);
            return;
        }

        // Limitar el tamaño a 50 MB.
        if ($archivoImg["size"] > 50 * 1024 * 1024) {
            Response::json([
                "error" => "El archivo no puede superar los 50 MB"
            ], 400);
            return;
        }

        // Comprobar el tipo real del archivo.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipo = $finfo->file($archivoImg["tmp_name"]);

        if ($tipo !== "image/png" && $tipo!== "image/jpeg" && $tipo!== "image/webp") {
            Response::json([
                "error" => "El archivo debe ser un png, jpeg o webp"
            ], 400);
            return;
        }

        // Crear la carpeta si todavía no existe.
        $carpeta = __DIR__ . "/../public/uploads/proyectosImg/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        // Generar un nombre único para portada.
        $extensiones = [
            "image/png" => "png",
           "image/jpeg" => "jpg",
         "image/webp" => "webp"
        ];

        $nombrePortada = bin2hex(random_bytes(16)) . "." . $extensiones[$tipo];

        $destino = $carpeta . $nombrePortada;
        // Guardar el archivo en el servidor.
        if (!move_uploaded_file($archivoImg["tmp_name"], $destino)) {
            Response::json([
                "error" => "No se pudo guardar el archivo"
            ], 500);
            return;
        }

        Response::json([
            "mensaje" => "Portada subida correctamente",
            "portada" => "uploads/proyectosImg/" . $nombrePortada
        ], 201);
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
