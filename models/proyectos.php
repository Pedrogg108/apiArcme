
<?php

require_once __DIR__ . "/../core/model.php";

class Proyecto extends Model {

    // Obtener todos los proyectos.
    public function getAll() {

        $sql = "SELECT * FROM proyectos ORDER BY id_obra DESC";

        $resultado = $this->pdo->query($sql);

        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un proyecto por su ID.
    public function getByid_obra($id_obra) {

        $sql = "SELECT * FROM proyectos WHERE id_obra = :id_obra";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            "id_obra" => $id_obra
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un proyecto.
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
        $fecha_finalizacion
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
                    fecha_finalizacion
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
                    :fecha_finalizacion
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
            "fecha_finalizacion" => $fecha_finalizacion
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
                    fecha_finalizacion = :fecha_finalizacion
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
            "id_obra" => $id_obra
        ]);
    }

   // Eliminar un proyecto.
public function delete($id_obra) {

    try {

        $this->pdo->beginTransaction();

        // Eliminar los registros relacionados.
        $sqlFotos = "DELETE FROM fotoobra WHERE id_obra = :id_obra";

        $stmtFotos = $this->pdo->prepare($sqlFotos);

        $stmtFotos->execute([
            "id_obra" => $id_obra
        ]);

        // Eliminar el proyecto.
        $sql = "DELETE FROM proyectos WHERE id_obra = :id_obra";

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

    // Obtener los proyectos publicados en el catálogo.
    public function getCatalogo() {

        $sql = "SELECT * FROM proyectos
                WHERE visible_catalogo = 1
                ORDER BY id_obra DESC";

        $resultado = $this->pdo->query($sql);

        return $resultado->fetchAll(PDO::FETCH_ASSOC);
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
}
