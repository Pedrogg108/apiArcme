
<?php

require_once __DIR__ . "/../models/Usuario.php";

class LoginController {

    private $modelo;

    public function __construct() {
        $this->modelo = new Usuario();
    }

    public function login($datos) {

        session_start();

        $dni = $datos["dni"];
        $contrasena = $datos["contrasena"];

        $usuario = $this->modelo->buscarPorDni($dni);

        if ($usuario && password_verify($contrasena, $usuario["contraseña"])) {

            session_regenerate_id(true);

            $_SESSION["dni"] = $usuario["dni"];
            $_SESSION["nombre"] = $usuario["nombre"];

            unset($usuario["contraseña"]);

            echo json_encode([
                "mensaje" => "Login correcto",
                "usuario" => $usuario
            ]);

        } else {
            http_response_code(401);

            echo json_encode([
                "error" => "DNI o contraseña incorrectos"
            ]);
        }
    }

    public function cerrarSesion() {

        session_start();

        session_unset();
        session_destroy();

        echo json_encode([
            "mensaje" => "Sesión cerrada correctamente"
        ]);
    }

    public function verificarSesion() {

    session_start();

    if (isset($_SESSION["dni"])) {

        echo json_encode([
            "sesion" => true,
            "nombre" => $_SESSION["nombre"]
        ]);

    } else {

        echo json_encode([
            "sesion" => false
        ]);

    }
}
}
?>
