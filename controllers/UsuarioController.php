
<?php

require_once __DIR__ . "/../models/Usuario.php";
require_once __DIR__ . "/../utils/response.php";

class UsuarioController {

    private $modelo;

    public function __construct() {
        $this->modelo = new Usuario();
    }

    public function registro($datos) {

        if ($this->modelo->existeDni($datos["dni"])) {
            http_response_code(409);

            echo json_encode([
                "error" => "Este DNI ya está registrado"
            ]);

            return;
        }

        $resultado = $this->modelo->registro(
            $datos["dni"],
            $datos["contrasena"],
            $datos["nombre"],
            $datos["telefono"] ?? null,
            $datos["correo"] ?? null
        );

        if ($resultado) {
            http_response_code(201);

            echo json_encode([
                "mensaje" => "Usuario registrado correctamente"
            ]);
        } else {
            http_response_code(500);

            echo json_encode([
                "error" => "No se pudo registrar el usuario"
            ]);
        }
    }

    public function verificarDni($datos) {

        $existe = $this->modelo->existeDni($datos["dni"]);

        echo json_encode([
            "existe" => $existe
        ]);
    }

    // Obtiene los datos del usuario de la sesión
    public function perfil() {

        session_start();

        if (!isset($_SESSION["dni"])) {
            http_response_code(401);

            echo json_encode([
                "error" => "No hay una sesión iniciada"
            ]);

            return;
        }

        $usuario = $this->modelo->buscarPorDni($_SESSION["dni"]);

        if ($usuario) {

            unset($usuario["contraseña"]);

            echo json_encode($usuario);

        } else {
            http_response_code(404);

            echo json_encode([
                "error" => "Usuario no encontrado"
            ]);
        }
    }

    // Actualiza los datos del perfil
    public function actualizarPerfil($datos) {

        session_start();

        if (!isset($_SESSION["dni"])) {
            http_response_code(401);

            echo json_encode([
                "error" => "No hay una sesión iniciada"
            ]);

            return;
        }

        $nombre = trim($datos["nombre"] ?? "");

        if ($nombre === "") {
            http_response_code(400);

            echo json_encode([
                "error" => "El nombre es obligatorio"
            ]);

            return;
        }

        $correo = trim($datos["correo"] ?? "");
        $telefono = trim($datos["telefono"] ?? "");

        // Si están vacíos, se guardan como NULL
        $correo = $correo === "" ? null : $correo;
        $telefono = $telefono === "" ? null : $telefono;

        $resultado = $this->modelo->actualizarPerfil(
            $_SESSION["dni"],
            $nombre,
            $correo,
            $telefono
        );

        if ($resultado) {

            // Actualizamos también el nombre de la sesión
            $_SESSION["nombre"] = $nombre;

            echo json_encode([
                "mensaje" => "Perfil actualizado correctamente",
                "nombre" => $nombre
            ]);

        } else {
            http_response_code(500);

            echo json_encode([
                "error" => "No se pudieron actualizar los datos"
            ]);
        }
    }
    // Recibe y guarda la foto del perfil
public function subirFoto() {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica que haya una sesión iniciada
    if (!isset($_SESSION["dni"])) {
        http_response_code(401);
        echo json_encode(["error" => "No hay una sesión iniciada"]);
        return;
    }

    // Verifica que se haya enviado una imagen
    if (!isset($_FILES["foto"])) {
        http_response_code(400);
        echo json_encode(["error" => "No se seleccionó ninguna imagen"]);
        return;
    }

    $archivo = $_FILES["foto"];

    if ($archivo["error"] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["error" => "Error al subir la imagen"]);
        return;
    }

    // Limita el tamaño a 5 MB
    if ($archivo["size"] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(["error" => "La imagen no puede superar los 5 MB"]);
        return;
    }

    // Comprueba el tipo real del archivo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipo = $finfo->file($archivo["tmp_name"]);

    $tiposPermitidos = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($tiposPermitidos[$tipo])) {
        http_response_code(400);
        echo json_encode(["error" => "Formato no permitido. Usá JPG, PNG o WEBP"]);
        return;
    }

    // Crea la carpeta si todavía no existe
    $carpeta = __DIR__ . "/../public/uploads/perfiles/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    // Genera un nombre único para la imagen
    $nombreArchivo = bin2hex(random_bytes(16)) . "." . $tiposPermitidos[$tipo];

    $destino = $carpeta . $nombreArchivo;

    // Guarda la imagen en el servidor
    if (!move_uploaded_file($archivo["tmp_name"], $destino)) {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo guardar la imagen"]);
        return;
    }

    // Guarda la ruta en la base de datos
    $rutaFoto = "uploads/perfiles/" . $nombreArchivo;

    $resultado = $this->modelo->actualizarFoto(
        $_SESSION["dni"],
        $rutaFoto
    );

    if ($resultado) {
        echo json_encode([
            "mensaje" => "Foto actualizada correctamente",
            "foto" => $rutaFoto
        ]);
    } else {
        // Si no se pudo actualizar MySQL, elimina el archivo subido
        unlink($destino);

        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar la foto"]);
    }
}
}
?>
