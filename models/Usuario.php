
<?php
require_once __DIR__ . "/../core/model.php";

class Usuario extends Model {

    public function registro($dni, $contrasena, $nombre, $telefono, $correo) {

        $contrasenaHash = password_hash(
            $contrasena,
            PASSWORD_DEFAULT
        );

        $sql = "INSERT INTO usuario
                (dni, `contraseña`, nombre, telefono, correo)
                VALUES
                (:dni, :contrasena, :nombre, :telefono, :correo)";

        try {
            $stmt = $this->pdo->prepare($sql);

            $resultado = $stmt->execute([
                "dni" => $dni,
                "contrasena" => $contrasenaHash,
                "nombre" => $nombre,
                "telefono" => $telefono ?: null,
                "correo" => $correo ?: null
            ]);

            return $resultado;

        } catch (PDOException $e) {
            http_response_code(500);

            echo json_encode([
                "error" => "Error al guardar usuario",
                "detalle" => $e->getMessage()
            ]);

            return false;
        }
    }

    public function buscarPorDni($dni) {
    $sql = "SELECT dni, nombre, telefono, correo, contraseña, foto
            FROM usuario
            WHERE dni = :dni
            LIMIT 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(["dni" => $dni]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function existeDni($dni) {

        $sql = "SELECT dni FROM usuario WHERE dni = :dni";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["dni" => $dni]);

        return $stmt->fetch() !== false;
    }

    // Actualiza los datos del perfil del usuario
    public function actualizarPerfil($dni, $nombre, $correo, $telefono) {

        $sql = "UPDATE usuario
                SET nombre = :nombre,
                    correo = :correo,
                    telefono = :telefono
                WHERE dni = :dni";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "nombre" => $nombre,
            "correo" => $correo,
            "telefono" => $telefono,
            "dni" => $dni
        ]);
    }
    // Actualiza la foto del perfil
public function actualizarFoto($dni, $foto) {
    $sql = "UPDATE usuario
            SET foto = :foto
            WHERE dni = :dni";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        "foto" => $foto,
        "dni" => $dni
    ]);
}
}
?>
