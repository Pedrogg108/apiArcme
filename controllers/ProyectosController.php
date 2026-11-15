
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

    $proyecto["fotos"] = $this->modelo->getFotos($id);

    Response::json($proyecto, 200);
    }
    // POST /proyectos
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
        "portada",
        "id_cliente"
    ];

    foreach ($campos as $campo) {

        if (!isset($datos[$campo]) || $datos[$campo] === "") {
            Response::json([
                "error" => "Falta el campo: " . $campo
            ], 400);
            return;
        }
    }

    // Validar el ID del cliente.
    if (!filter_var($datos["id_cliente"], FILTER_VALIDATE_INT) ||
        (int)$datos["id_cliente"] <= 0) {

        Response::json([
            "error" => "Debe seleccionar un cliente válido"
        ], 400);
        return;
    }

    // Comprobar que el cliente exista.
    if (!$this->modelo->existeCliente($datos["id_cliente"])) {

        Response::json([
            "error" => "El cliente seleccionado no existe"
        ], 404);
        return;
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
        $datos["portada"],
        $datos["id_cliente"]
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

    // Verificar que se envíe un cliente.
    if (
        !isset($datos["id_cliente"]) ||
        $datos["id_cliente"] === "" ||
        !filter_var($datos["id_cliente"], FILTER_VALIDATE_INT) ||
        (int)$datos["id_cliente"] <= 0
    ) {
        Response::json([
            "error" => "Debe seleccionar un cliente válido"
        ], 400);
        return;
    }

    // Comprobar que el cliente exista.
    if (!$this->modelo->existeCliente($datos["id_cliente"])) {

        Response::json([
            "error" => "El cliente seleccionado no existe"
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

   public function subirFotosExtra($id_obra) {

    $proyecto = $this->modelo->getByid_obra($id_obra);

    if (!$proyecto) {
        Response::json(["error" => "El proyecto no existe"], 404);
        return;
    }
if (!isset($_FILES["fotosExtra"]) || !isset($_POST["ordenes"])) {
    Response::json([
        "error" => "Faltan datos",
        "files" => $_FILES,
        "post" => $_POST,
        "content_type" => $_SERVER["CONTENT_TYPE"] ?? null
    ], 400);
    return;
}

    $fotos = $_FILES["fotosExtra"];
    $ordenes = $_POST["ordenes"];

    if (!is_array($ordenes) || count($fotos["name"]) !== count($ordenes)) {
        Response::json(["error" => "Los archivos y sus órdenes no coinciden"], 400);
        return;
    }

    $carpeta = __DIR__ . "/../public/uploads/proyectosImg/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $tiposPermitidos = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    $fotosGuardadas = [];

    for ($i = 0; $i < count($fotos["name"]); $i++) {

        $orden = (int) $ordenes[$i];

        // Comprobar que la posición sea válida.
        if ($orden < 1 || $orden > 4) {
            Response::json(["error" => "Orden de imagen no válido"], 400);
            return;
        }

        if ($fotos["error"][$i] !== UPLOAD_ERR_OK) {
            Response::json(["error" => "Error al recibir una imagen"], 400);
            return;
        }

        if ($fotos["size"][$i] > 10 * 1024 * 1024) {
            Response::json(["error" => "Cada imagen debe pesar menos de 10 MB"], 400);
            return;
        }

        $tipo = $finfo->file($fotos["tmp_name"][$i]);

        if (!isset($tiposPermitidos[$tipo])) {
            Response::json(["error" => "Formato de imagen no permitido"], 400);
            return;
        }

        $extension = $tiposPermitidos[$tipo];

        $nombre = bin2hex(random_bytes(16)) . "." . $extension;

        $rutaCompleta = $carpeta . $nombre;
        $rutaBD = "uploads/proyectosImg/" . $nombre;

        if (!move_uploaded_file($fotos["tmp_name"][$i], $rutaCompleta)) {
            Response::json(["error" => "No se pudo guardar una imagen"], 500);
            return;
        }

        // Actualizar únicamente la imagen de esa posición.
        $actualizada = $this->modelo->actualizarFoto(
            $id_obra,
            $orden,
            $rutaBD
        );

        if (!$actualizada) {
            unlink($rutaCompleta);

            Response::json([
                "error" => "No se pudo actualizar la imagen número " . $orden
            ], 500);
            return;
        }

        $fotosGuardadas[] = [
            "orden" => $orden,
            "url" => $rutaBD
        ];
    }

    Response::json([
        "mensaje" => "Imágenes actualizadas correctamente",
        "fotos" => $fotosGuardadas
    ], 200);
}
public function listarPorCliente($id_cliente) {
        $proyectos = $this->modelo->getByCliente($id_cliente);
        Response::json($proyectos, 200);
    }


}
