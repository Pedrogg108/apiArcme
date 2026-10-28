<?php

require_once __DIR__ . "/../core/model.php";

class Proyecto extends Model {

    // Obtener todos los proyectos junto con sus fotografías.
    public function getAll() {

        $sql = "SELECT * FROM proyectos ORDER BY id_obra DESC";

        $resultado = $this->pdo->query($sql);

        $proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proyectos as &$proyecto) {
            $proyecto["fotos"] = $this->getFotos($proyecto["id_obra"]);
        }

        unset($proyecto);

        return $proyectos;
    }

    // Obtener un proyecto por su ID, incluyendo sus fotografías.
    public function getByid_obra($id_obra) {

        $sql = "SELECT * FROM proyectos WHERE id_obra = :id_obra";

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

    // Crear un proyecto y devolver su ID.
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

        return (int) $this->pdo->lastInsertId();
    }

    // Guardar la ruta de una fotografía relacionada con un proyecto.
    public function agregarFoto($id_obra, $url_foto, $orden) {

        $sql = "INSERT INTO fotoobra (
                    id_obra,
                    url_foto,
                    orden
                ) VALUES (
                    :id_obra,
                    :url_foto,
                    :orden
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "id_obra" => $id_obra,
            "url_foto" => $url_foto,
            "orden" => $orden
        ]);
    }

    // Obtener las fotografías de un proyecto.
    public function getFotos($id_obra) {

        $sql = "SELECT id_foto, id_obra, url_foto, orden
                FROM fotoobra
                WHERE id_obra = :id_obra
                ORDER BY orden ASC, id_foto ASC";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            "id_obra" => $id_obra
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            "id_obra" => $id_obra,
            "titulo" => $datos["titulo"],
            "superficie" => $datos["superficie"],
            "ubicacion" => $datos["ubicacion"],
            "precio" => $datos["precio"],
            "descripcion" => $datos["descripcion"],
            "nomenclatura" => $datos["nomenclatura"],
            "estado" => $datos["estado"],
            "memoria" => $datos["memoria"],
            "fecha_inicio" => $datos["fecha_inicio"],
            "fecha_finalizacion" => $datos["fecha_finalizacion"]
        ]);
    }

    // Eliminar un proyecto.
    public function delete($id_obra) {

        $sql = "DELETE FROM proyectos WHERE id_obra = :id_obra";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            "id_obra" => $id_obra
        ]);
    }

    // Obtener los proyectos publicados en el catálogo.
    public function getCatalogo() {

        $sql = "SELECT * FROM proyectos
                WHERE visible_catalogo = 1";

        $resultado = $this->pdo->query($sql);

        $proyectos = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($proyectos as &$proyecto) {
            $proyecto["fotos"] = $this->getFotos($proyecto["id_obra"]);
        }

        unset($proyecto);

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
}