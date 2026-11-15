
<?php

require_once __DIR__ . "/../core/model.php";

class Proyecto extends Model {

    // Obtener todos los proyectos con su cliente y sus fotos.
    public function getAll() {

        $sql = "SELECT p.*, c.nombre AS cliente
                FROM proyectos p
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
                ORDER BY p.id_obra DESC";

        $resultado = $this->pdo->query($sql);

        $proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proyectos as &$proyecto) {
            $proyecto["fotos"] = $this->getFotos($proyecto["id_obra"]);
        }

        return $proyectos;
    }

    // Obtener un proyecto por su ID.
    public function getByid_obra($id_obra) {

        $sql = "SELECT p.*, c.nombre AS cliente
                FROM proyectos p
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
                WHERE p.id_obra = :id_obra";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            "id_obra" => $id_obra
        ]);

        $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($proyecto) {
            $proyecto["fotos"] = $this->getFotos($id_obra);
        }

        return $proyecto;
    }

    // Crear un proyecto vinculado a un cliente.
    public function create(
        $titulo,
        $superficie,
        $ubicacion,
        $precio,
        $descripcion,
        $nomenclatura,
        $estado,
        $memoria,
        $fecha_inicio,
        $fecha_finalizacion,
        $portada,
        $id_cliente
    ) {

        $sql = "INSERT INTO proyectos (
                    titulo,
                    superficie,
                    ubicacion,
                    precio,
                    descripcion,
                    nomenclatura,
                    estado,
                    memoria,
                    fecha_inicio,
                    fecha_finalizacion,
                    portada,
                    id_cliente
                ) VALUES (
                    :titulo,
                    :superficie,
                    :ubicacion,
                    :precio,
                    :descripcion,
                    :nomenclatura,
                    :estado,
                    :memoria,
                    :fecha_inicio,
                    :fecha_finalizacion,
                    :portada,
                    :id_cliente
                )";

        $stmt = $this->pdo->prepare($sql);

        $correcto = $stmt->execute([
            "titulo" => $titulo,
            "superficie" => $superficie,
            "ubicacion" => $ubicacion,
            "precio" => $precio,
            "descripcion" => $descripcion,
            "nomenclatura" => $nomenclatura,
            "estado" => $estado,
            "memoria" => $memoria,
            "fecha_inicio" => $fecha_inicio,
            "fecha_finalizacion" => $fecha_finalizacion,
            "portada" => $portada,
            "id_cliente" => $id_cliente
        ]);

        if (!$correcto) {
            return false;
        }

        return $this->pdo->lastInsertId();
    }

    // Actualizar un proyecto.
    public function update($id_obra, $datos) {

        $sql = "UPDATE proyectos SET
                    titulo = :titulo,
                    superficie = :superficie,
                    ubicacion = :ubicacion,
                    precio = :precio,
                    descripcion = :descripcion,
                    nomenclatura = :nomenclatura,
                    estado = :estado,
                    memoria = :memoria,
                    fecha_inicio = :fecha_inicio,
                    fecha_finalizacion = :fecha_finalizacion,
                    portada = :portada,
                    id_cliente = :id_cliente
                WHERE id_obra = :id_obra";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "titulo" => $datos["titulo"],
            "superficie" => $datos["superficie"],
            "ubicacion" => $datos["ubicacion"],
            "precio" => $datos["precio"],
            "descripcion" => $datos["descripcion"],
            "nomenclatura" => $datos["nomenclatura"],
            "estado" => $datos["estado"],
            "memoria" => $datos["memoria"],
            "fecha_inicio" => $datos["fecha_inicio"],
            "fecha_finalizacion" => $datos["fecha_finalizacion"],
            "portada" => $datos["portada"],
            "id_cliente" => $datos["id_cliente"],
            "id_obra" => $id_obra
        ]);
    }

    // Eliminar un proyecto y sus fotos.
    public function delete($id_obra) {

        try {

            $this->pdo->beginTransaction();

            $sqlFotos = "DELETE FROM fotoobra
                         WHERE id_obra = :id_obra";

            $stmtFotos = $this->pdo->prepare($sqlFotos);

            $stmtFotos->execute([
                "id_obra" => $id_obra
            ]);

            $sql = "DELETE FROM proyectos
                    WHERE id_obra = :id_obra";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                "id_obra" => $id_obra
            ]);

            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    // Obtener proyectos publicados en el catálogo.
    public function getCatalogo() {

        $sql = "SELECT p.*, c.nombre AS cliente
                FROM proyectos p
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
                WHERE p.visible_catalogo = 1
                ORDER BY p.id_obra DESC";

        $resultado = $this->pdo->query($sql);

        $proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proyectos as &$proyecto) {
            $proyecto["fotos"] = $this->getFotos($proyecto["id_obra"]);
        }

        return $proyectos;
    }

    // Alternar la visibilidad de un proyecto.
    public function cambiarVisibilidadCatalogo($id_obra) {

        $sql = "UPDATE proyectos
                SET visible_catalogo =
                    IF(visible_catalogo = 1, 0, 1)
                WHERE id_obra = :id_obra";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "id_obra" => $id_obra
        ]);
    }

    // Obtener proyectos vinculados a un cliente.
    public function getByCliente($id_cliente) {

        $sql = "SELECT p.*, c.nombre AS cliente
                FROM proyectos p
                LEFT JOIN clientes c ON p.id_cliente = c.id_cliente
                WHERE p.id_cliente = :id_cliente
                ORDER BY p.id_obra DESC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            "id_cliente" => $id_cliente
        ]);

        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proyectos as &$proyecto) {
            $proyecto["fotos"] = $this->getFotos($proyecto["id_obra"]);
        }

        return $proyectos;
    }

    // Insertar una foto adicional.
    public function insertarFoto($id_obra, $url, $orden) {

        $sql = "INSERT INTO fotoobra (id_obra, URL_foto, orden)
                VALUES (:id_obra, :url, :orden)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "id_obra" => $id_obra,
            "url" => $url,
            "orden" => $orden
        ]);
    }

    // Obtener fotos adicionales de un proyecto.
    public function getFotos($id_obra) {

        $sql = "SELECT id_foto, id_obra, URL_foto, orden
                FROM fotoobra
                WHERE id_obra = :id_obra
                ORDER BY orden ASC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            "id_obra" => $id_obra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar una foto según su posición.
    public function actualizarFoto($id_obra, $orden, $url) {

        $sql = "SELECT id_foto FROM fotoobra
                WHERE id_obra = :id_obra
                AND orden = :orden";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            "id_obra" => $id_obra,
            "orden" => $orden
        ]);

        $foto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($foto) {

            $sql = "UPDATE fotoobra
                    SET URL_foto = :url
                    WHERE id_foto = :id_foto";

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                "url" => $url,
                "id_foto" => $foto["id_foto"]
            ]);

        } else {

            return $this->insertarFoto($id_obra, $url, $orden);
        }
    }
    // Comprobar si existe un cliente.
public function existeCliente($id_cliente) {

    $sql = "SELECT id_cliente
            FROM clientes
            WHERE id_cliente = :id_cliente";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        "id_cliente" => $id_cliente
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}
}
